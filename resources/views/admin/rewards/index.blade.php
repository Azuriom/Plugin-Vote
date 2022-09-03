@extends('admin.layouts.admin')

@section('title', trans('vote::admin.rewards.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ trans('messages.fields.name') }}</th>
                        <th scope="col">{{ trans('messages.fields.image') }}</th>
                        <th scope="col">{{ trans('vote::messages.fields.chances') }}</th>
                        <th scope="col">{{ trans('messages.fields.enabled') }}</th>
                        <th scope="col">{{ trans('messages.fields.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($rewards as $reward)
                        <tr>
                            <th scope="row">{{ $reward->id }}</th>
                            <td>{{ $reward->name }}</td>
                            <td>
                                @if($reward->image)
                                    <img src="{{ $reward->imageUrl() }}" class="img-small rounded" alt="{{ $reward->name }}">
                                @else
                                    {{ trans('messages.none') }}
                                @endif
                            </td>
                            <td>{{ $reward->chances }} %</td>
                            <td>
                                <span class="badge bg-{{ $reward->is_enabled ? 'success' : 'danger' }}">
                                    {{ trans_bool($reward->is_enabled) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('vote.admin.rewards.edit', $reward) }}" class="mx-1" title="{{ trans('messages.actions.edit') }}" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                <a href="{{ route('vote.admin.rewards.destroy', $reward) }}" class="mx-1" title="{{ trans('messages.actions.delete') }}" data-toggle="tooltip" data-confirm="delete"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

            <a class="btn btn-primary" href="{{ route('vote.admin.rewards.create') }}">
                <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
            </a>
        </div>
    </div>
@endsection
