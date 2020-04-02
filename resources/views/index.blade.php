@extends('layouts.app')

@section('title', trans('vote::messages.title'))

@push('styles')
    <style>
        #vote-spinner {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(70, 70, 70, 0.6);
            z-index: 10;
        }
    </style>
@endpush

@section('content')
    <div class="container content">

        <h2>Vote</h2>

        <div id="vote-alert"></div>

        <div class="vote vote-now text-center position-relative mb-4 px-3 py-5 border rounded">
            <div class="@auth d-none @endauth" data-vote-step="1">
                <form id="voteNameForm">
                    <div class="form-group">
                        <input type="text" id="stepNameInput" name="name" class="form-control" @auth value="{{ auth()->user()->name }}" @endauth placeholder="{{ trans('messages.fields.name') }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ trans('messages.actions.continue') }}
                        <span class="d-none spinner-border spinner-border-sm load-spinner" role="status"></span>
                    </button>
                </form>
            </div>
            <div class="@guest d-none @endguest h-100" data-vote-step="2">
                <div id="vote-spinner" class="d-none h-100">
                    <div class="spinner-border text-white" role="status"></div>
                </div>

                @forelse($sites as $site)
                    <a class="btn btn-primary" href="{{ $site->url }}" target="_blank" rel="noopener noreferrer" data-site-url="{{ route('vote.vote', $site) }}">{{ $site->name }}</a>
                @empty
                    <div class="alert alert-warning" role="alert">{{ trans('vote::messages.no-site') }}</div>
                @endforelse
            </div>
            <div class="d-none" data-vote-step="3">
                <p id="vote-result"></p>
            </div>
        </div>

        <h2>{{ trans('vote::messages.sections.top') }}</h2>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ trans('messages.fields.name') }}</th>
                <th scope="col">{{ trans('vote::messages.fields.votes') }}</th>
            </tr>
            </thead>
            <tbody>

            @foreach($votes as $id => $vote)
                <tr>
                    <th scope="row">#{{ $id }}</th>
                    <td>{{ $vote['user']->name }}</td>
                    <td>{{ $vote['votes'] }}</td>
                </tr>
            @endforeach

            </tbody>
        </table>

        @if(display_rewards())
            <h2>{{ trans('vote::messages.sections.rewards') }}</h2>

            <table class="table">
                <thead>
                <tr>
                    <th scope="col">{{ trans('messages.fields.name') }}</th>
                    <th scope="col">{{ trans('vote::messages.fields.chances') }}</th>
                </tr>
                </thead>
                <tbody>

                @foreach($rewards as $reward)
                    <tr>
                        <th scope="row">{{ $reward->name }}</th>
                        <td>{{ $reward->chances }} %</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        @endif

    </div>
@endsection

@push('footer-scripts')
    <script>
        let username @auth = '{{ auth()->user()->name }}' @endauth;

        const voteNameForm = document.getElementById('voteNameForm');
        let voting = false;

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

        voteNameForm.addEventListener('submit', function (ev) {
            ev.preventDefault();

            let tempUsername = document.getElementById('stepNameInput').value;
            const loaderIcon = voteNameForm.querySelector('.load-spinner');

            if (loaderIcon) {
                loaderIcon.classList.remove('d-none');
            }

            clearVoteAlert();

            axios.get('{{ route('vote.verify-user', '') }}/' + tempUsername)
                .then(function (response) {
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

        document.querySelectorAll('[data-site-url]').forEach(function (el) {
            el.addEventListener('click', function (ev) {
                if (voting === true) {
                    return;
                }

                ev.preventDefault();

                if (el.classList.contains('disabled')) {
                    return;
                }

                document.getElementById('vote-spinner').classList.remove('d-none');

                axios.post(el.dataset['siteUrl'], {
                    user: username,
                }).then(function (response) {
                    voting = true;
                    el.click();
                    voting = false;

                    el.classList.add('disabled');

                    refreshVote(el.dataset['siteUrl']);
                }).catch(function (error) {
                    displayVoteAlert(error.response.data.message, 'danger');

                    document.getElementById('vote-spinner').classList.add('d-none');
                });
            });
        });

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

                    displayVoteAlert(error.response.data.message, 'danger');
                });
            }, 3000);
        }
    </script>
@endpush
