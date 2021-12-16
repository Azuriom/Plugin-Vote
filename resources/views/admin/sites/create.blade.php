@extends('admin.layouts.admin')

@section('title', trans('vote::admin.sites.title-create'))

@section('content')
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div>{{$error}}</div>
        @endforeach
    @endif
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('vote.admin.sites.store') }}" method="POST">
                @include('vote::admin.sites._form')

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ trans('messages.actions.save') }}</button>
            </form>
        </div>
    </div>

    @include('vote::admin.sites._list')
@endsection
