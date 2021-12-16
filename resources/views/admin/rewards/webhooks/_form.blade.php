<div class="form-group">
    <label for="webhookSelect">{{ trans('vote::admin.smv.fields.webhook') }}</label>
    <select class="custom-select @error('webhook') is-invalid @enderror" id="webhookSelect" name="webhook" required>
        @foreach($webhooks as $webhook)
            <option value="{{ $webhook }}" @if(($reward->webhook ?? '') === $webhook) selected @endif>{{ $webhook }}</option>
        @endforeach
    </select>
    <small>{!! trans('vote::admin.smv.webhook.info') !!}</small>

    @error('webhook')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="limitInput">{{ trans('vote::admin.smv.fields.limit') }}</label>

    <div class="input-group">
        <input type="text" class="form-control @error('limit') is-invalid @enderror" id="limitInput" name="limit" value="{{ old('limit', $reward->limit ?? 0) }}">

        @error('limit')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    <small>{{ trans('vote::admin.smv.limit') }}</small>
</div>

