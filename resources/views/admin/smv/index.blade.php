@extends('admin.layouts.admin')

@section('title', trans('vote::admin.smv.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">

            <form action="{{ route('vote.admin.smv.store') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ trans('messages.actions.save') }}
                </button>

            </form>

        </div>
    </div>
@endsection
