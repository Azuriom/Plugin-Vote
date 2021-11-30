@extends('admin.layouts.admin')

@section('title', trans('vote::admin.smv.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">

            <form action="{{ route('vote.admin.smv.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="keyInput">{{ trans('vote::admin.smv.fields.key') }}</label>
                    <input type="text" class="form-control @error('key') is-invalid @enderror" id="keyInput" name="key" value="{{ old('key', $key ?? '') }}" required placeholder="smv_sk_">
                    <small></small>

                    @error('key')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> {!! trans('vote::admin.smv.info') !!}
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ trans('messages.actions.save') }}
                </button>

            </form>

        </div>
    </div>
@endsection
