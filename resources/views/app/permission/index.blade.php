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
                    <div style="display: flex; justify-content: space-between;">
                        <h4 class="card-title">
                            @lang('crud.permissions.index_title')
                        </h4>
                        <div class="row">

                    <div class="col-md-6 text-right">
                        @can('create', App\Models\Permission::class)
                            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                                <i class="icon ion-md-add"></i> @lang('crud.common.create')
                            </a>
                        @endcan
                    </div>
                </div>
                    </div>

                    <div class="card-datatable">
                        <table id="permissionsTable" class="dt-multilingual table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-left">
                                        @lang('crud.permissions.inputs.name')
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name ?? '-' }}</td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions"
                                                class="btn-group demo-inline-spacing">
                                                @can('update', $permission)
                                                    <a href="{{ route('permissions.edit', $permission) }}">
                                                        <span class="badge text-bg-primary">
                                                            <i class="icon-base ti tabler-edit icon-me sm-2"></i> Edit
                                                        </span>
                                                    </a>
                                                @endcan

                                                @can('view', $permission)
                                                    <a href="{{ route('permissions.show', $permission) }}">
                                                        <span class="badge text-bg-warning">
                                                            <i class="icon-base ti tabler-eye icon-me sm-2"></i> Show
                                                        </span>
                                                    </a>
                                                @endcan

                                                @can('delete', $permission)
                                                    <form action="{{ route('permissions.destroy', $permission) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                                        style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <span class="icon-base ti tabler-trash icon-xs sm-2"></span>Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            @lang('crud.common.no_items_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
