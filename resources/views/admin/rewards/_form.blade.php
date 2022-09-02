@csrf

@include('vote::admin.elements.select')

<div class="mb-3" v-scope="{ icon: '{{ $reward->image ?? '' }}' }">
    <label class="form-label" for="imageSelect">{{ trans('messages.fields.image') }}</label>
    <div class="input-group mb-3">
        <a class="btn btn-outline-success" href="{{ route('admin.images.create') }}" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-upload"></i>
        </a>
        <select class="form-select @error('image') is-invalid @enderror" id="imageSelect" v-model="icon" name="image">
            <option value="" @selected(isset($reward) ? !$reward->image : false)>
                {{ trans('messages.none') }}
            </option>

            @foreach($images as $image)
                <option value="{{ $image->file }}" @selected($image->file === (isset($reward) ? !$reward->image : ""))>
                    {{ $image->name }}
                </option>
            @endforeach
        </select>
    </div>

    <img v-if="icon" :src="icon ? '{{ image_url() }}/' + icon : '#'" class="img-fluid rounded img-preview-sm" alt="Image">

    @error('image')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nameInput">{{ trans('messages.fields.name') }}</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $reward->name ?? '') }}" required>

    @error('name')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="serversSelect">{{ trans('vote::messages.fields.servers') }}</label>

    <select class="form-select @error('servers') is-invalid @enderror" id="serversSelect" name="servers[]" multiple>
        @foreach($servers as $server)
            <option value="{{ $server->id }}" @selected(isset($reward) && $reward->servers->contains($server))>
                {{ $server->name }}
            </option>
        @endforeach
    </select>

    @error('servers')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="chancesInput">{{ trans('vote::messages.fields.chances') }}</label>

    <div class="input-group @error('chances') has-validation @enderror">
        <input type="number" min="0" max="100" step="0.01" class="form-control @error('chances') is-invalid @enderror" id="chancesInput" name="chances" value="{{ old('chances', $reward->chances ?? '0') }}" required>
        <div class="input-group-text">%</div>

        @error('chances')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="moneyInput">{{ trans('messages.fields.money') }}</label>

    <div class="input-group @error('money') has-validation @enderror">
        <input type="number" min="0" step="0.01" max="999999" class="form-control @error('money') is-invalid @enderror" id="moneyInput" name="money" value="{{ old('money', $reward->money ?? '') }}">
        <div class="input-group-text">{{ money_name() }}</div>

        @error('money')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="needOnlineSwitch" name="need_online" @checked(old('need_online', $reward->need_online ?? true))>
    <label class="form-check-label" for="needOnlineSwitch">{{ trans('vote::admin.rewards.require_online') }}</label>
</div>

<div class="mb-3">
    <label class="form-label">{{ trans('vote::messages.fields.commands') }}</label>

    @include('admin.elements.list-input', ['name' => 'commands', 'values' => $reward->commands ?? []])

    <small class="form-text">@lang('vote::admin.rewards.commands')</small>
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="enableSwitch" name="is_enabled" @checked(old('is_enabled', $reward->is_enabled ?? true))>
    <label class="form-check-label" for="enableSwitch">{{ trans('vote::admin.rewards.enable') }}</label>
</div>
