function toggleStep(step) {
    document.querySelectorAll('[data-vote-step]').forEach(function (el) {
        el.classList.add('d-none');
    });

    const current = document.querySelector('[data-vote-step="' + step + '"]');
    if (current) {
        current.classList.remove('d-none');
    }
}

function clearVoteAlert() {
    document.getElementById('status-message').innerHTML = '';
}

function displayVoteAlert(message, level) {
    document.getElementById('status-message').innerHTML = '<div class="alert alert-' + level + '" role="alert">' + message + '</div>';
}

function catchVoteError(error) {
    if (error.response && error.response.data && error.response.data.message) {
        displayVoteAlert(error.response.data.message, 'danger');
        return;
    }

    console.error(error);

    displayVoteAlert(error.toString(), 'danger');
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
        link.querySelector('.vote-timer').innerText = getTimeDifference(nextVoteTime);
    } else {
        link.classList.remove('disabled');
        link.querySelector('.vote-timer').innerText = '';
        link.removeAttribute('data-vote-time');
    }
}

const voteDoneCallbacks = [];

function initVote() {
    document.querySelectorAll('[data-vote-url]').forEach(function (el) {
        const voteTime = el.dataset['voteTime'];
        const url = el.getAttribute('href');

        if (voteTime && voteTime > Date.now()) {
            el.classList.add('disabled');
            updateVoteLink(el);

            const timer = setInterval(function () {
                updateVoteLink(el);
            }, 1000);

            voteDoneCallbacks.push(function () {
                clearInterval(timer);
            })
        }

        if (url.includes('{player}')) {
            el.setAttribute('href', url.replace('{player}', window.username));
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

            clearVoteAlert();

            el.classList.add('disabled');
            document.getElementById('vote-card').classList.add('voting');

            refreshVote(el.dataset['voteUrl']);
        };

        el.addEventListener('click', clickListener);
        el.addEventListener('auxclick', clickListener);

        voteDoneCallbacks.push(function () {
            el.removeEventListener('click', clickListener);
            el.removeEventListener('auxclick', clickListener);
        })
    });
}

function setupVoteTimers(name) {
    const loaderIcon = voteNameForm.querySelector('.load-spinner');

    if (loaderIcon) {
        loaderIcon.classList.remove('d-none');
    }

    axios.get(voteNameForm.action + '/' + name)
        .then(function (response) {
            toggleStep(2);

            const sites = response.data.sites;
            window.username = name;

            for (let id in sites) {
                const el = document.querySelector('[data-vote-id="' + id + '"]');

                if (el && sites[id]) {
                    el.classList.remove('disabled');
                    el.setAttribute('data-vote-time', sites[id]);
                }
            }

            initVote();
        })
        .catch(function (error) {
            catchVoteError(error);
        })
        .finally(function () {
            if (loaderIcon) {
                loaderIcon.classList.add('d-none');
            }
        });
}

const voteNameForm = document.getElementById('voteNameForm');

if (voteNameForm) {
    voteNameForm.addEventListener('submit', function (ev) {
        ev.preventDefault();

        let tempUsername = document.getElementById('stepNameInput').value;

        clearVoteAlert();

        setupVoteTimers(tempUsername);
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

            document.getElementById('vote-card').classList.remove('voting');

            if (response.data.status === 'select_server') {
                showServerSelect(url, response.data.servers);
                return;
            }

            rewardDelivered(response.data.message);
        }).catch(function (error) {
            document.getElementById('vote-card').classList.remove('voting');

            catchVoteError(error);
        });
    }, 5000);
}

function rewardDelivered(message) {
    displayVoteAlert(message, 'success');

    voteDoneCallbacks.forEach(function (callback) {
        callback();
    });

    setupVoteTimers(window.username);
}

function showServerSelect(baseURL, servers) {
    const serverSelect = document.getElementById('server-select');

    Object.entries(servers).forEach(function ([serverId, serverName]) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-primary';
        button.innerText = serverName;

        button.addEventListener('click', function () {
            document.getElementById('vote-card').classList.add('voting');

            axios.post(baseURL + '/done', {
                user: window.username,
                server: serverId,
            }).then(function (response) {
                rewardDelivered(response.data.message);
                serverSelect.innerHTML = '';
            }).catch(function (error) {
                catchVoteError(error);
            }).finally(function () {
                document.getElementById('vote-card').classList.remove('voting');
            });
        });

        serverSelect.appendChild(button);
    })

    toggleStep('server')
}

if (window.username) {
    initVote();
}
