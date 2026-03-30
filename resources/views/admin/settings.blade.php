@extends('admin.layouts.admin')

@section('title', trans('vote::admin.settings.title'))

@include('vote::admin.elements.select')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">

            <form action="{{ route('vote.admin.settings') }}" method="POST" id="settingsForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="topPlayersCount">{{ trans('vote::admin.settings.count') }}</label>
                    <input type="number" class="form-control" id="topPlayersCount" name="top-players-count" min="5" max="100" value="{{ old('top-players-count', $topPlayersCount) }}" required="required">
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

                    <div class="form-text">{!! game()->trans('commands') !!}</div>
                </div>

                <div v-scope="{ goalEnabled: voteGoalEnabled, voteCommands: voteCommandsList }">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" name="goal_enabled" id="goalEnabled" v-model="goalEnabled" @checked($goalEnabled)>
                        <label class="form-check-label" for="goalEnabled">{{ trans('vote::admin.goal.enable') }}</label>
                    </div>

                    <div v-if="goalEnabled" class="card card-body mb-3">
                        <div class="mb-3">
                            <label class="form-label" for="goalTargetInput">{{ trans('vote::admin.goal.target') }}</label>

                            <div class="input-group @error('goal_target') has-validation @enderror">
                                <input type="number" min="1" class="form-control @error('goal_target') is-invalid @enderror"
                                       id="goalTargetInput" name="goal_target" value="{{ old('goal_target', $goalTarget ?? '') }}" required>
                                <div class="input-group-text">{{ trans('vote::admin.goal.votes') }}</div>

                                @error('goal_target')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <h3 class="h5">{{ trans('vote::admin.goal.commands') }}</h3>

                        @include('vote::admin.commands._goal')

                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="goalAutoReset" name="goal_auto_reset" @checked($goalAutoReset)>
                            <label class="form-check-label" for="goalAutoReset">{{ trans('vote::admin.goal.auto_reset') }}</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>

            </form>

        </div>
    </div>
@endsection

@push('footer-scripts')
    <script>
        const voteCommandsList = @json(old('goal_commands', $goalCommands ?? []));
        const voteGoalEnabled = {{ old('goal_enabled', $goalEnabled) ? 'true' : 'false' }};

        if (!voteGoalEnabled && !voteCommandsList.length) {
            voteCommandsList.push({ commands: [''], server: 0 });
        }
    </script>
@endpush
