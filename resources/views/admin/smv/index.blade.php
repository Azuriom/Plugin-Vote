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

    <div class="card shadow mb-4">
        <div class="card-header">
            {{ trans('vote::admin.smv.rewards') }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ trans('messages.fields.name') }}</th>
                        <th scope="col">{{ trans('vote::admin.smv.fields.webhook') }}</th>
                        <th scope="col">{{ trans('vote::messages.fields.server') }}</th>
                        <th scope="col">{{ trans('vote::messages.fields.chances') }}</th>
                        <th scope="col">{{ trans('vote::admin.smv.fields.limit') }}</th>
                        <th scope="col">{{ trans('messages.fields.enabled') }}</th>
                        <th scope="col">{{ trans('messages.fields.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rewards as $reward)
                        <tr>
                            <th scope="row">{{ $reward->id }}</th>
                            <td>{{ $reward->reward->name }}</td>
                            <td>{{ $reward->webhook }}</td>
                            <td>{{ $reward->reward->server->name ?? '?' }}</td>
                            <td>{{ $reward->reward->chances }} %</td>
                            <td>{{ $reward->limit === 0 ? 'Aucune limite' : $reward->limit }}</td>
                            <td>
                                <span class="badge badge-{{ $reward->reward->is_enabled ? 'success' : 'danger' }}">
                                    {{ trans_bool($reward->reward->is_enabled) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('vote.admin.smv.rewards.edit', $reward) }}" class="mx-1" title="{{ trans('messages.actions.edit') }}" data-toggle="tooltip"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('vote.admin.smv.rewards.destroy', $reward) }}" class="mx-1" title="{{ trans('messages.actions.delete') }}" data-toggle="tooltip" data-confirm="delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info"></i> {!! trans('vote::admin.smv.rewards_info') !!}
            </div>
            <a class="btn btn-primary" href="{{ route('vote.admin.smv.rewards.create') }}">
                <i class="fas fa-plus"></i> {{ trans('messages.actions.add') }}
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            {{ trans('vote::admin.smv.logs') }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ trans('vote::admin.smv.fields.player') }}</th>
                        <th scope="col">{{ trans('vote::admin.smv.fields.webhook') }}</th>
                        <th scope="col">{{ trans('messages.fields.name') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <th scope="row">{{ $log->id }}</th>
                                <th scope="row">{{ $log->name }}</th>
                                <th scope="row">{{ $log->webhook->webhook }}</th>
                                <th scope="row">{{ $log->webhook->name }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>

@endsection
