@csrf

@include('vote::admin.elements.select')
@include('vote::admin.elements.time-picker')

<div class="row gx-3">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="nameInput">{{ trans('messages.fields.name') }}</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $site->name ?? '') }}" required>

        @error('name')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label" for="rewardSelect">{{ trans('vote::messages.fields.rewards') }}</label>

        <select class="form-select @error('rewards') is-invalid @enderror" id="rewardSelect" name="rewards[]" multiple required>
            @foreach($rewards as $reward)
                <option value="{{ $reward->id }}" @selected(isset($site) && $site->rewards->contains($reward) ?? false)>
                    {{ $reward->name }}
                </option>
            @endforeach
        </select>

        @empty($rewards)
            <a href="{{ route('vote.admin.rewards.create') }}" class="btn btn-success btn-sm" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
            </a>
        @endif

        @error('rewards')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="urlInput">{{ trans('messages.fields.url') }}</label>
    <input type="url" class="form-control @error('url') is-invalid @enderror" id="urlInput" name="url" value="{{ old('url', $site->url ?? '') }}" required>

    @error('url')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror

    <div class="form-text">@lang('vote::admin.sites.variable')</div>
    <div id="verificationStatusLabel" class="form-text text-info d-none"></div>
</div>

<div class="d-none" id="verificationGroup">
    <div class="mb-3 form-check form-switch">
        <input type="checkbox" class="form-check-input" id="verificationSwitch" name="has_verification" @checked($site->has_verification ?? true)>
        <label class="form-check-label" for="verificationSwitch">{{ trans('vote::admin.sites.verifications.enable') }}</label>
    </div>

    <div class="mb-3 d-none" id="keyGroup">
        <label id="keyLabel" for="keyInput">{{ trans('vote::admin.sites.verifications.title') }}</label>
        <input type="text" min="0" class="form-control @error('verification_key') is-invalid @enderror" id="keyInput" name="verification_key" value="{{ old('verification_key', $site->verification_key ?? '') }}">

        @error('verification_key')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row gx-3" v-scope="{ intervalReset: '{{ old('vote_reset_at', $site->vote_reset_at ?? '') ? '' : '1' }}' }">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="intervalReset">{{ trans('vote::admin.sites.type') }}</label>

        <select class="form-select" id="intervalReset" name="reset_interval" v-model="intervalReset">
            <option value="1">
                {{ trans('vote::admin.sites.interval') }}
            </option>
            <option value="">
                {{ trans('vote::admin.sites.daily') }}
            </option>
        </select>
    </div>

    <div v-show="intervalReset" class="col-md-6 mb-3">
        <label class="form-label" for="delayInput">{{ trans('vote::admin.sites.delay') }}</label>

        <div class="input-group @error('vote_delay') has-validation @enderror">
            <input type="number" min="0" class="form-control @error('vote_delay') is-invalid @enderror" id="delayInput" name="vote_delay" value="{{ old('vote_delay', $site->vote_delay ?? '') }}" :required="intervalReset">
            <span class="input-group-text">{{ trans('vote::admin.sites.minutes') }}</span>

            @error('vote_delay')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div v-show="!intervalReset" class="col-md-6 mb-3">
        <label class="form-label" for="timeInput">{{ trans('vote::admin.sites.time') }}</label>
        <input type="text" class="form-control time-picker @error('vote_reset_at') is-invalid @enderror" id="timeInput" name="vote_reset_at" value="{{ old('vote_reset_at', $site->vote_reset_at ?? '00:00') }}" :required="!intervalReset">

        @error('vote_reset_at')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="enableSwitch" name="is_enabled" @checked($site->is_enabled ?? true)>
    <label class="form-check-label" for="enableSwitch">{{ trans('vote::admin.sites.enable') }}</label>
</div>

@push('footer-scripts')
    <script>
        const urlInput = document.getElementById('urlInput');
        const verificationStatusLabel = document.getElementById('verificationStatusLabel');
        const verificationGroup = document.getElementById('verificationGroup');
        const verificationKeyGroup = document.getElementById('keyGroup');
        const verificationKeyLabel = document.getElementById('keyLabel');

        function updateVoteVerification() {
            if (urlInput.value === '') {
                verificationGroup.classList.add('d-none');
                verificationStatusLabel.classList.add('d-none');
                return;
            }

            axios.get('{{ route('vote.admin.sites.verification') }}?url=' + encodeURIComponent(urlInput.value))
                .then(function (response) {
                    verificationStatusLabel.innerText = response.data.info;
                    verificationStatusLabel.classList.remove('d-none');

                    if (!response.data.supported) {
                        verificationGroup.classList.add('d-none');
                        return;
                    }

                    if (response.data.automatic) {
                        verificationKeyGroup.classList.add('d-none');
                        verificationGroup.classList.remove('d-none');
                        return;
                    }

                    verificationKeyLabel.innerText = response.data.label;
                    verificationKeyGroup.classList.remove('d-none');
                    verificationGroup.classList.remove('d-none');
                }).catch(function () {
                verificationGroup.classList.add('d-none');
                verificationStatusLabel.classList.add('d-none');
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateVoteVerification();
        });

        urlInput.addEventListener('focusout', function () {
            updateVoteVerification();
        });
    </script>
@endpush
