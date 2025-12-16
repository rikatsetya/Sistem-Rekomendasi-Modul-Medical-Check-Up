@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-body">
                    <!-- Header with title and action -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <a href="{{ route('roles.index') }}" class="me-3">
                                <i class="icon ion-md-arrow-back"></i>
                            </a>
                            @lang('crud.roles.show_title')
                        </h4>
                        @can('create', App\Models\Role::class)
                            <a href="{{ route('roles.create') }}" class="btn btn-outline-primary">
                                <i class="icon ion-md-add"></i> @lang('crud.common.create')
                            </a>
                        @endcan
                    </div>

                    <!-- Role Details -->
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small mb-1">@lang('crud.roles.inputs.name')</div>
                            <div class="fw-bold">{{ $role->name ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Assigned Permissions (Optional) -->
                    @if ($role->permissions->isNotEmpty())
                        <hr class="my-4" />
                        <div>
                            <h5 class="mb-3">@lang('Permissions granted')</h5>
                            <div class="row">
                                @foreach ($role->permissions as $permission)
                                    <div class="col-md-4 mb-2">
                                        <span class="badge bg-label-primary me-1">
                                            {{ ucfirst($permission->name) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Footer Actions -->
                    <div class="mt-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                            <i class="icon ion-md-return-left"></i> @lang('crud.common.back')
                        </a>
                        @can('update', $role)
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary ms-2">
                                <i class="icon ion-md-create"></i> @lang('crud.common.edit')
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
