@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <a href="{{ route('permissions.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                        @lang('crud.permissions.create_title')
                    </h4>

                    <x-form method="POST" action="{{ route('permissions.store') }}" class="mt-4">
                        @include('app.permission.form-inputs')

                        <div class="mt-4">
                            <a href="{{ route('permissions.index') }}" class="btn btn-light">
                                <i class="icon ion-md-return-left text-primary"></i>
                                @lang('crud.common.back')
                            </a>

                            <button type="submit" class="btn btn-primary float-right">
                                <i class="icon ion-md-save"></i>
                                @lang('crud.common.create')
                            </button>
                        </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection
