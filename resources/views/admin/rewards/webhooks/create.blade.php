@extends('admin.layouts.admin')

@section('title', trans('vote::admin.rewards.title-create'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('vote.admin.smv.rewards.store') }}" method="POST">

                @csrf

                @include('vote::admin.rewards.webhooks._form')
                @include('vote::admin.rewards._form')

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ trans('messages.actions.save') }}</button>
            </form>
        </div>
    </div>
@endsection
