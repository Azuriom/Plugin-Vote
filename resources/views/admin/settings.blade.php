@extends('admin.layouts.admin')

@section('title', trans('vote::admin.settings.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">

            <form action="{{ route('vote.admin.settings') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="topPlayersCount">{{ trans('vote::admin.settings.count') }}</label>
                    <input type="number" class="form-control" id="topPlayersCount" name="top-players-count" min="5" max="100" value="{{ $topPlayersCount }}" required="required">
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="displayRewards" name="display-rewards" @checked($displayRewards)>
                        <label class="form-check-label" for="displayRewards">{{ trans('vote::admin.settings.display-rewards') }}</label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="ipCompatibility" name="ip_compatibility" @checked($ipCompatibility) aria-describedby="ipCompatibilityLabel">
                        <label class="form-check-label" for="ipCompatibility">{{ trans('vote::admin.settings.ip_compatibility') }}</label>
                    </div>
                    <div id="ipCompatibilityLabel" class="form-text">{{ trans('vote::admin.settings.ip_compatibility_info') }}</div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="authRequired" name="auth_required" @checked($authRequired)>
                        <label class="form-check-label" for="authRequired">{{ trans('vote::admin.settings.auth_required') }}</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ trans('vote::admin.settings.commands') }}</label>

                    @include('admin.elements.list-input', ['name' => 'commands', 'values' => $commands])

                    <div class="form-text">@lang('vote::admin.rewards.commands')</div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>

            </form>

        </div>
    </div>
@endsection
