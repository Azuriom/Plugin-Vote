@csrf

<div class="mb-3">
    <label class="form-label" for="nameInput">{{ trans('messages.fields.name') }}</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $reward->name ?? '') }}" required>

    @error('name')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="serverSelect">{{ trans('vote::messages.fields.server') }}</label>
    <select class="form-select @error('server_id') is-invalid @enderror" id="serverSelect" name="server_id" required>
        @foreach($servers as $server)
            <option value="{{ $server->id }}" @selected(($reward->server_id ?? 0) === $server->id)>{{ $server->name }}</option>
        @endforeach
    </select>

    @error('server_id')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="chancesInput">{{ trans('vote::messages.fields.chances') }}</label>

    <div class="input-group @error('chances') has-validation @enderror">
        <input type="text" class="form-control @error('chances') is-invalid @enderror" id="chancesInput" name="chances" value="{{ old('chances', $reward->chances ?? '0') }}" required>
        <div class="input-group-text">%</div>

        @error('chances')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="moneyInput">{{ trans('messages.fields.money') }}</label>

    <div class="input-group @error('money') has-validation @enderror">
        <input type="text" class="form-control @error('money') is-invalid @enderror" id="moneyInput" name="money" value="{{ old('money', $reward->money ?? '') }}">
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

    @include('admin.elements.list-input', ['name' => 'commands', 'values' => $commands ?? []])
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="enableSwitch" name="is_enabled" @checked(old('is_enabled', $reward->is_enabled ?? true))>
    <label class="form-check-label" for="enableSwitch">{{ trans('vote::admin.rewards.enable') }}</label>
</div>
