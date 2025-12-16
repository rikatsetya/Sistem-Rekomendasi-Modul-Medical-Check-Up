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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">@lang('crud.categories.index_title')</h4>
                        <div class="row">
                            <div class="col-md-6 text-right">
                                @can('create', App\Models\Category::class)
                                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                        <i class="icon ion-md-add"></i> @lang('crud.common.create')
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable">
                        <table id="categoriesTable" class="dt-multilingual table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-left">@lang('crud.categories.inputs.name')</th>
                                    <th class="text-left">@lang('crud.categories.inputs.parent')</th>
                                    <th class="text-center">Sub Category Count</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->name ?? '-' }}</td>
                                        <td>{{ $category->parent ?? '-' }}</td>
                                        <td class="text-center">{{ $category->subCategories->count()}}</td>
                                        <td class="text-center" style="width: 180px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group demo-inline-spacing">
                                                @can('view', $category)
                                                    <a href="{{ route('categories.show', $category) }}">
                                                        <span class="badge text-bg-warning">
                                                            <i class="icon-base ti tabler-eye icon-me sm-2"></i> Show
                                                        </span>
                                                    </a>
                                                @endcan
                                                @can('update', $category)
                                                    <a href="{{ route('categories.edit', $category) }}">
                                                        <span class="badge text-bg-primary">
                                                            <i class="icon-base ti tabler-edit icon-me sm-2"></i> Edit
                                                        </span>
                                                    </a>
                                                @endcan
                                                @can('delete', $category)
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                                          onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                                          style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <span class="icon-base ti tabler-trash icon-xs sm-2"></span> Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">@lang('crud.common.no_items_found')</td>
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
