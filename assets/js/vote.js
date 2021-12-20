function toggleStep(step) {
    document.querySelectorAll('[data-vote-step]').forEach(function (el) {
        el.classList.add('d-none');
    });

    const currentEl = document.querySelector('[data-vote-step="' + step + '"]');
    if (currentEl) {
        currentEl.classList.remove('d-none');
    }

    clearVoteAlert();
}

function clearVoteAlert() {
    document.getElementById('vote-alert').innerHTML = '';
}

function displayVoteAlert(message, level) {
    document.getElementById('vote-alert').innerHTML = '<div class="alert alert-' + level + '" role="alert">' + message + '</div>';
}

document.querySelectorAll('[data-site-url]').forEach(function (el) {
    el.addEventListener('click', function (ev) {
        ev.preventDefault();

        if (el.classList.contains('disabled')) {
            return;
        }

        document.getElementById('vote-spinner').classList.remove('d-none');

        axios.post(el.dataset['siteUrl'], {
            user: username,
        }).then(function () {
            let url = el.getAttribute('href');
            if (url.includes('{player}')){
                url = url.replace('{player}', username);
            }
            window.open(url, '_blank');

            el.classList.add('disabled');

            refreshVote(el.dataset['siteUrl']);
        }).catch(function (error) {
            displayVoteAlert(error.response.data.message ? error.response.data.message : error, 'danger');

            document.getElementById('vote-spinner').classList.add('d-none');
        });
    });
});

const voteNameForm = document.getElementById('voteNameForm');

if (voteNameForm) {
    voteNameForm.addEventListener('submit', function (ev) {
        ev.preventDefault();

        let tempUsername = document.getElementById('stepNameInput').value;
        const loaderIcon = voteNameForm.querySelector('.load-spinner');

        if (loaderIcon) {
            loaderIcon.classList.remove('d-none');
        }

        clearVoteAlert();

        axios.get(voteRoute + '/' + tempUsername)
            .then(function () {
                toggleStep(2);

                username = tempUsername;
            })
            .catch(function (error) {
                displayVoteAlert(error.response.data.message, 'danger');
            })
            .finally(function () {
                if (loaderIcon) {
                    loaderIcon.classList.add('d-none');
                }
            });
    });
}

function refreshVote(url) {
    setTimeout(function () {
        axios.post(url + '/done', {
            user: username,
        }).then(function (response) {
            if (response.data.status === 'pending') {
                refreshVote(url);
                return;
            }

            displayVoteAlert(response.data.message, 'success');

            document.getElementById('vote-spinner').classList.add('d-none');
        }).catch(function (error) {
            document.getElementById('vote-spinner').classList.add('d-none');

            displayVoteAlert(error.response.data.message ? error.response.data.message : error, 'danger');
        });
    }, 5000);
}
