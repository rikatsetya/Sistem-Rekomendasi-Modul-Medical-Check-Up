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
                    {{-- Header Title + Button --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">@lang('crud.sub_categories.index_title')</h4>
                        <div class="row">
                            <div class="col-md-6 text-right">
                                @can('create', App\Models\SubCategory::class)
                                    <a href="{{ route('sub-categories.create') }}" class="btn btn-primary">
                                        <i class="icon ion-md-add"></i> @lang('crud.common.create')
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="card-datatable">
                        <table id="subCategoriesTable" class="dt-multilingual table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-left">@lang('crud.sub_categories.inputs.category_id')</th>
                                    <th class="text-left">@lang('crud.sub_categories.inputs.name')</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subCategories as $subCategory)
                                    <tr>
                                        <td>{{ optional($subCategory->category)->name ?? '-' }}</td>
                                        <td>{{ $subCategory->name ?? '-' }}</td>
                                        <td class="text-center" style="width: 180px;">
                                            <div role="group" aria-label="Row Actions"
                                                class="btn-group demo-inline-spacing">
                                                @can('view', $subCategory)
                                                    <a href="{{ route('sub-categories.show', $subCategory) }}">
                                                        <span class="badge text-bg-warning">
                                                            <i class="icon-base ti tabler-eye icon-me sm-2"></i> Show
                                                        </span>
                                                    </a>
                                                @endcan
                                                @can('update', $subCategory)
                                                    <a href="{{ route('sub-categories.edit', $subCategory) }}">
                                                        <span class="badge text-bg-primary">
                                                            <i class="icon-base ti tabler-edit icon-me sm-2"></i> Edit
                                                        </span>
                                                    </a>
                                                @endcan
                                                @can('delete', $subCategory)
                                                    <form action="{{ route('sub-categories.destroy', $subCategory) }}"
                                                        method="POST"
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
