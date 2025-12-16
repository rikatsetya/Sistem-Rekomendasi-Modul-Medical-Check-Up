@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <a href="{{ route('permissions.index') }}" class="me-3">
                                <i class="icon ion-md-arrow-back"></i>
                            </a>
                            @lang('crud.permissions.show_title')
                        </h4>
                        @can('create', App\Models\Permission::class)
                            <a href="{{ route('permissions.create') }}" class="btn btn-outline-primary">
                                <i class="icon ion-md-add"></i> @lang('crud.common.create')
                            </a>
                        @endcan
                    </div>

                    <!-- Permission Name -->
                    <div class="mb-4">
                        <div class="text-muted text-uppercase small mb-1">@lang('crud.permissions.inputs.name')</div>
                        <div class="fw-bold">{{ $permission->name ?? '-' }}</div>
                    </div>

                    <!-- Assigned Roles -->
                    @if ($permission->roles && $permission->roles->isNotEmpty())
                        <hr class="my-4">
                        <div class="mb-2">
                            <h6 class="text-muted text-uppercase small">@lang('Assigned roles')</h6>
                            <div>
                                @foreach ($permission->roles as $role)
                                    <span class="badge bg-label-info me-1 mb-1">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Footer Actions -->
                    <div class="mt-4">
                        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
                            <i class="icon ion-md-return-left"></i> @lang('crud.common.back')
                        </a>
                        @can('update', $permission)
                            <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-primary ms-2">
                                <i class="icon ion-md-create"></i> @lang('crud.common.edit')
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
