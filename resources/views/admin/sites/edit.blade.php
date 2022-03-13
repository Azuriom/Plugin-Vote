@extends('admin.layouts.admin')

@section('title', trans('vote::admin.sites.edit', ['site' => $site->name]))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('vote.admin.sites.update', $site) }}" method="POST">
                @method('PUT')

                @include('vote::admin.sites._form')

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
                <a href="{{ route('vote.admin.sites.destroy', $site) }}" class="btn btn-danger" data-confirm="delete">
                    <i class="bi bi-trash"></i> {{ trans('messages.actions.delete') }}
                </a>
            </form>
        </div>
    </div>
@endsection
