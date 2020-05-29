@extends('layouts.app')

@section('title', trans('vote::messages.title'))

@section('content')
    <div class="container content">

        <h2>{{ trans('vote::messages.sections.vote') }}</h2>

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

        <nav>
            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
              <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">{{ trans('vote::messages.last-month') }}</a>
              <a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">{{ trans('vote::messages.this-month') }}</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
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
            </div>
            <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
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
            </div>
        </div>

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

@push('scripts')
    <script src="{{ plugin_asset('vote', 'js/vote.js') }}" defer></script>
    <script>
        const voteRoute = '{{ route('vote.verify-user', '') }}';
        let username @auth = '{{ auth()->user()->name }}' @endauth;
    </script>
@endpush

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
