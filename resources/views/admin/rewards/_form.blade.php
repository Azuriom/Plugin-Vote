@csrf

<div class="form-group">
    <label for="nameInput">{{ trans('messages.fields.name') }}</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $reward->reward->name ?? '') }}" required>

    @error('name')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="serverSelect">{{ trans('vote::messages.fields.server') }}</label>
    <select class="custom-select @error('server_id') is-invalid @enderror" id="serverSelect" name="server_id" required>
        @foreach($servers as $server)
            <option value="{{ $server->id }}" @if(($reward->reward->server_id ?? 0) === $server->id) selected @endif>{{ $server->name }}</option>
        @endforeach
    </select>

    @error('server_id')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="chancesInput">{{ trans('vote::messages.fields.chances') }}</label>

    <div class="input-group">
        <input type="text" class="form-control @error('chances') is-invalid @enderror" id="chancesInput" name="chances" value="{{ old('chances', $reward->chances ?? '0') }}" required>
        <div class="input-group-append">
            <div class="input-group-text">%</div>
        </div>

        @error('chances')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="form-group">
    <label for="moneyInput">{{ trans('messages.fields.money') }}</label>

    <div class="input-group">
        <input type="text" class="form-control @error('money') is-invalid @enderror" id="moneyInput" name="money" value="{{ old('money', $reward->money ?? '') }}">
        <div class="input-group-append">
            <div class="input-group-text">{{ money_name() }}</div>
        </div>

        @error('money')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="form-group custom-control custom-switch">
    <input type="checkbox" class="custom-control-input" id="needOnlineSwitch" name="need_online" @if(old('need_online', $reward->need_online ?? true)) checked @endif>
    <label class="custom-control-label" for="needOnlineSwitch">{{ trans('vote::admin.rewards.need-online') }}</label>
</div>

<div class="form-group">
    <label>{{ trans('vote::messages.fields.commands') }}</label>

    @include('vote::admin.elements.commands', ['commands' => $reward->commands ?? []])
</div>

<div class="form-group custom-control custom-switch">
    <input type="checkbox" class="custom-control-input" id="enableSwitch" name="is_enabled" @if(old('is_enabled', $reward->is_enabled ?? true)) checked @endif>
    <label class="custom-control-label" for="enableSwitch">{{ trans('vote::admin.rewards.enable') }}</label>
</div>
