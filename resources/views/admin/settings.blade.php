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

                <hr class="my-4">

                <h4 class="mb-3">
                    {{ trans('vote::admin.settings.goal_section') }}
                </h4>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="goalEnabled" name="goal_enabled" @checked($goalEnabled)>
                        <label class="form-check-label" for="goalEnabled">{{ trans('vote::admin.settings.goal_enable') }}</label>
                    </div>
                </div>

                <div class="row gx-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="goalTarget">{{ trans('vote::admin.settings.goal_target') }}</label>
                        <input type="number" class="form-control @error('goal_target') is-invalid @enderror" id="goalTarget" name="goal_target" min="1" value="{{ old('goal_target', $goalTarget) }}">

                        @error('goal_target')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="goalServers">{{ trans('vote::admin.settings.goal_server') }}</label>
                        <select class="form-select @error('goal_servers') is-invalid @enderror" id="goalServers" name="goal_servers[]" multiple>
                            @foreach($servers as $server)
                                <option value="{{ $server->id }}" @selected(in_array($server->id, $goalServers ?? []))>
                                    {{ $server->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('goal_servers')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="goalAutoReset" name="goal_auto_reset" @checked($goalAutoReset)>
                        <label class="form-check-label" for="goalAutoReset">{{ trans('vote::admin.settings.goal_auto_reset') }}</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ trans('vote::admin.settings.goal_commands') }}</label>

                    <div id="goal_commands-input">
                        @forelse($goalCommands ?? [] as $value)
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="goal_commands[]" value="{{ $value }}">
                                <button class="btn btn-outline-danger goal_commands-remove" type="button">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        @empty
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="goal_commands[]" placeholder="">
                                <button class="btn btn-outline-danger goal_commands-remove" type="button">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <div class="my-1">
                        <button type="button" id="goal_commands-add-button" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
                        </button>
                    </div>

                    <div class="form-text">@lang('vote::admin.settings.goal_commands_info')</div>
                </div>

            </form>

            <form action="{{ route('vote.admin.settings.reset-goal') }}" method="POST" id="resetGoalForm">
                @csrf
            </form>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" form="resetGoalForm" class="btn btn-danger">
                    <i class="bi bi-arrow-counterclockwise"></i> {{ trans('vote::admin.settings.goal_reset') }}
                </button>
                <button type="submit" form="settingsForm" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
            </div>

        </div>
    </div>
@endsection

@push('footer-scripts')
    <script>
        const addGoalListener = function(el) {
            el.addEventListener('click', function () {
                const element = el.parentNode;
                element.parentNode.removeChild(element);
            });
        }

        document.querySelectorAll('.goal_commands-remove').forEach(function (el) {
            addGoalListener(el);
        });

        document.getElementById('goal_commands-add-button').addEventListener('click', function () {
            let input = '<div class="input-group mb-2"><input type="text" name="goal_commands[]" class="form-control">';
            input += '<button class="btn btn-outline-danger goal_commands-remove" type="button"><i class="bi bi-x-lg"></i></button>';
            input += '</div>';

            const newElement = document.createElement('div');
            newElement.innerHTML = input;

            addGoalListener(newElement.querySelector('.goal_commands-remove'));

            document.getElementById('goal_commands-input').appendChild(newElement);
        });
    </script>
@endpush
