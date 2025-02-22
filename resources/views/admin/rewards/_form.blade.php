@csrf

@include('vote::admin.elements.select')

<div class="row gx-3">
    <div class="col-md-4 mb-3">
        <label class="form-label" for="nameInput">{{ trans('messages.fields.name') }}</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $reward->name ?? '') }}" required>

        @error('name')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="chancesInput">{{ trans('vote::messages.fields.chances') }}</label>

        <div class="input-group @error('chances') has-validation @enderror">
            <input type="number" min="0" max="100" step="0.01" class="form-control @error('chances') is-invalid @enderror" id="chancesInput" name="chances" value="{{ old('chances', $reward->chances ?? '0') }}" required>
            <div class="input-group-text">%</div>

            @error('chances')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="moneyInput">{{ trans('messages.fields.money') }}</label>

        <div class="input-group @error('money') has-validation @enderror">
            <input type="number" min="0" step="0.01" max="999999" class="form-control @error('money') is-invalid @enderror" id="moneyInput" name="money" value="{{ old('money', $reward->money ?? '') }}">
            <div class="input-group-text">{{ money_name() }}</div>

            @error('money')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
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

    <div class="mb-3 col-md-6">
        <label class="form-label" for="imageInput">{{ trans('messages.fields.image') }}</label>
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="imageInput" name="image" accept=".jpg,.jpeg,.jpe,.png,.gif,.bmp,.svg,.webp" data-image-preview="imagePreview">

        @error('image')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror

        <img src="{{ ($reward->image ?? false) ? $reward->imageUrl() : '#' }}" class="mt-2 img-fluid rounded img-preview {{ ($reward->image ?? false) ? '' : 'd-none' }}" alt="Image" id="imagePreview">
    </div>
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="needOnlineSwitch" name="need_online" @checked(old('need_online', $reward->need_online ?? true))>
    <label class="form-check-label" for="needOnlineSwitch">{{ trans('vote::admin.rewards.require_online') }}</label>
</div>

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="singleSwitch" name="single_server" @checked(old('single_server', $reward->single_server ?? false))>
    <label class="form-check-label" for="singleSwitch">{{ trans('vote::admin.rewards.single_server') }}</label>
</div>

<div class="mb-3">
    <label class="form-label">{{ trans('vote::messages.fields.commands') }}</label>

    @include('admin.elements.list-input', ['name' => 'commands', 'values' => $reward->commands ?? []])

    <div class="form-text">@lang('vote::admin.rewards.commands')</div>
</div>

@if($cron)
    <div class="mb-3" v-scope='{ rewards: @json($reward->monthly_rewards ?? ['']) }'>
        <label class="form-label">{{ trans('vote::admin.rewards.monthly') }}</label>

        <div class="row gx-3">
            <div v-for="(value, index) in rewards" class="col-md-4 mb-2" :key="index">
                <div class="input-group">
                    <span class="input-group-text">
                        #
                    </span>
                    <input type="number" class="form-control" name="monthly_rewards[]" v-model="rewards[index]" :value="value" min="1" max="50">
                    <button @click="rewards.splice(index, 1)" class="btn btn-outline-danger" type="button">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="my-1">
            <button @click="rewards.push('')" type="button" class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
            </button>
        </div>

        <div class="form-text">@lang('vote::admin.rewards.monthly_info')</div>
    </div>
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> @lang('vote::admin.rewards.cron')
    </div>
@endif

<div class="mb-3 form-check form-switch">
    <input type="checkbox" class="form-check-input" id="enableSwitch" name="is_enabled" @checked(old('is_enabled', $reward->is_enabled ?? true))>
    <label class="form-check-label" for="enableSwitch">{{ trans('vote::admin.rewards.enable') }}</label>
</div>
