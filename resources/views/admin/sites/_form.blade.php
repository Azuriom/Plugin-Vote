@csrf

<div class="mb-3">
    <label class="form-label" for="nameInput">{{ trans('messages.fields.name') }}</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $site->name ?? '') }}" required>

    @error('name')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="urlInput">{{ trans('messages.fields.url') }}</label>
    <input type="url" class="form-control @error('url') is-invalid @enderror" id="urlInput" name="url" value="{{ old('url', $site->url ?? '') }}" required>

    @error('url')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror

    <small class="form-text">@lang('vote::admin.sites.variable')</small>
    <small id="verificationStatusLabel" class="form-text text-info d-none"></small>
</div>

<div class="d-none" id="verificationGroup">

    <div class="mb-3 form-check form-switch">
        <input type="checkbox" class="form-contro-input" id="verificationSwitch" name="has_verification" @checked($site->has_verification ?? true)>
        <label class="form-check-label" for="verificationSwitch">{{ trans('vote::admin.sites.verifications.enable') }}</label>
    </div>

    <div class="mb-3 d-none" id="keyGroup">
        <label id="keyLabel" for="keyInput">Verification</label>
        <input type="text" min="0" class="form-control @error('verification_key') is-invalid @enderror" id="keyInput" name="verification_key" value="{{ old('verification_key', $site->verification_key ?? '') }}">

        @error('verification_key')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

</div>

<div class="mb-3">
    <label class="form-label" for="delayInput">{{ trans('vote::admin.sites.delay') }}</label>

    <div class="input-group @error('vote_delay') has-validation @enderror">
        <input type="number" min="0" class="form-control @error('vote_delay') is-invalid @enderror" id="delayInput" name="vote_delay" value="{{ old('vote_delay', $site->vote_delay ?? '') }}" required>
        <span class="input-group-text">{{ trans('vote::admin.sites.minutes') }}</span>

        @error('vote_delay')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">{{ trans('vote::messages.fields.rewards') }}</label>

    <div class="card">
        <div class="card-body">
            @forelse($rewards as $reward)
                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="rewards{{ $reward->id }}" name="rewards[]" value="{{ $reward->id }}" @if(isset($site) && $site->rewards->contains($reward)) checked @endif>
                    <label class="form-check-label" for="rewards{{ $reward->id }}">{{ $reward->name }}</label>
                </div>
            @empty
                <a href="{{ route('vote.admin.rewards.create') }}" class="btn btn-success btn-sm" target="_blank" rel="noopener noreferrer"><i class="fas fa-plus"></i> {{ trans('messages.actions.add') }}
                </a>
            @endforelse
        </div>
    </div>

    @error('rewards')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
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
