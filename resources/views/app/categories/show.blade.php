@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-4">
                {{-- Header Title + Buttons --}}
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 row-gap-3">
                    <div class="d-flex flex-column justify-content-center">
                        <h4 class="mb-1">@lang('crud.categories.show_title')</h4>
                        <p class="mb-0 text-muted">Informasi lengkap kategori dan subkategori terkait</p>
                    </div>
                    <div class="d-flex align-content-center flex-wrap gap-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-label-secondary">
                            <i class="icon ion-md-return-left text-primary"></i> @lang('crud.common.back')
                        </a>
                        @can('create', App\Models\Category::class)
                            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                <i class="icon ion-md-add"></i> @lang('crud.common.create')
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- Left Column: Category Detail --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Detail Kategori</h5>
                        </div>
                        <div class="card-body">
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <small class="text-light fw-medium">@lang('crud.categories.inputs.name')</small>
                                    <p class="mb-0">{{ $category->name ?? '-' }}</p>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-light fw-medium">@lang('crud.categories.inputs.parent')</small>
                                    <p class="mb-0">{{ $category->parent ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Subcategories --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Sub Kategori</h5>
                            @can('create', App\Models\SubCategory::class)
                                <a href="{{ route('sub-categories.create', ['category_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="icon ion-md-add"></i> Tambah
                                </a>
                            @endcan
                        </div>
                        <div class="card-body">
                            @if($category->subCategories->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($category->subCategories as $subCategory)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $subCategory->name }}</span>
                                            <div class="d-flex gap-1">
                                                @can('view', $subCategory)
                                                    <a href="{{ route('sub-categories.show', $subCategory) }}">
                                                        <span class="badge text-bg-warning">
                                                            <i class="icon-base ti tabler-eye icon-sm"></i>
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
                                                            <span class="icon-base ti tabler-trash icon-sm"></span>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">Tidak ada sub kategori.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
