function toggleStep(step) {
    document.querySelectorAll('[data-vote-step]').forEach(function (el) {
        el.classList.add('d-none');
    });

    const current = document.querySelector('[data-vote-step="' + step + '"]');
    if (current) {
        current.classList.remove('d-none');
    }

    clearVoteAlert();
}

function clearVoteAlert() {
    document.getElementById('status-message').innerHTML = '';
}

function displayVoteAlert(message, level) {
    document.getElementById('status-message').innerHTML = '<div class="alert alert-' + level + '" role="alert">' + message + '</div>';
}

function getTimeDifference(date) {
    const difference = date - new Date().getTime();
    const hours = Math.floor(difference / (1000 * 60 * 60));
    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((difference % (1000 * 60)) / 1000);

    return (hours < 10 ? '0' : '') + hours
        + ':' + (minutes < 10 ? '0' : '') + minutes
        + ':' + (seconds < 10 ? '0' : '') + seconds;
}

function updateVoteLink(link) {
    const nextVoteTime = link.dataset['voteTime'];

    if (!nextVoteTime) {
        return;
    }

    if (nextVoteTime > Date.now()) {
        link.querySelector('.vote-timer').innerHTML = getTimeDifference(nextVoteTime);
    } else {
        link.classList.remove('disabled');
        link.querySelector('.vote-timer').innerHTML = '';
        link.removeAttribute('data-vote-time');
    }
}

function initVote() {
    document.querySelectorAll('[data-vote-url]').forEach(function (el) {
        const voteTime = el.dataset['voteTime'];

        if (voteTime && voteTime > Date.now()) {
            el.classList.add('disabled');

            setInterval(function () {
                updateVoteLink(el)
            }, 1000);
        }

        const clickListener = function (ev) {
            const middleClickCode = 1;
            if (ev.type === 'auxclick' && ev.button !== middleClickCode) {
                return;
            }

            if ((voteTime && voteTime > Date.now()) || el.classList.contains('disabled')) {
                ev.preventDefault();
                return;
            }

            el.classList.add('disabled');
            document.getElementById('vote-card').classList.add('voting');

            refreshVote(el.dataset['voteUrl']);
        };

        el.addEventListener('click', clickListener);
        el.addEventListener('auxclick', clickListener);
    });
}

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

        axios.get(voteNameForm.action + '/' + tempUsername)
            .then(function (response) {
                toggleStep(2);

                const sites = response.data.sites;
                window.username = tempUsername;

                for (let id in sites) {
                    const el = document.querySelector('[data-vote-id="' + id + '"]');

                    if (el && sites[id]) {
                        el.setAttribute('data-vote-time', sites[id]);
                    }
                }

                initVote();
            })
            .catch(function (error) {
                console.log(error);

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
            user: window.username,
        }).then(function (response) {
            if (response.data.status === 'pending') {
                refreshVote(url);
                return;
            }

            displayVoteAlert(response.data.message, 'success');

            document.getElementById('vote-card').classList.remove('voting');
        }).catch(function (error) {
            document.getElementById('vote-card').classList.remove('voting');

            displayVoteAlert(error.response.data.message ? error.response.data.message : error, 'danger');
        });
    }, 5000);
}

if (window.username) {
    initVote();
}
