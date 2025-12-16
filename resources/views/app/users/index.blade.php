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
                        <h4 class="card-title">@lang('crud.users.index_title')</h4>
                    </div>

                    <div class="card-datatable">
                        <table id="usersTable" class="dt-multilingual table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-left">
                                        @lang('crud.users.inputs.name')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.users.inputs.email')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.users.inputs.kopeg')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.users.inputs.devisi')
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->name ?? '-' }}</td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>{{ $user->kopeg ?? '-' }}</td>
                                        <td>{{ $user->devisi ?? '-' }}</td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions"
                                                class="btn-group demo-inline-spacing">
                                                @can('update', $user)
                                                    <a href="{{ route('users.edit', $user) }}">
                                                        <span class="badge text-bg-primary"><i
                                                                class="icon-base ti tabler-edit icon-me sm-2"></i> Edit</span>
                                                    </a>
                                                    @endcan @can('view', $user)
                                                    <a href="{{ route('users.show', $user) }}">
                                                        <span class="badge text-bg-warning"><i
                                                                class="icon-base ti tabler-eye icon-me sm-2"></i> Show</span>
                                                    </a>
                                                    @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
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
