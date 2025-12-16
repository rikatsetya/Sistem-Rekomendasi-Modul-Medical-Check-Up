@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Header Title + Buttons --}}
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 row-gap-3">
                    <div class="d-flex flex-column justify-content-center">
                        <h4 class="mb-1">@lang('crud.sub_categories.create_title')</h4>
                        <p class="mb-0 text-muted">
                            @lang('crud.sub_categories.create_subtitle', [], 'Silakan isi data sub kategori')
                        </p>
                    </div>
                    <div class="d-flex align-content-center flex-wrap gap-2">
                        <a href="{{ route('sub-categories.index') }}" class="btn btn-label-secondary">@lang('crud.common.back')
                        </a>
                        <button type="submit" form="subCategoryForm" class="btn btn-primary"> @lang('crud.common.create')
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <x-form method="POST" action="{{ route('sub-categories.store') }}" id="subCategoryForm">
                    @include('app.sub_categories.form-inputs')
                </x-form>
            </div>
        </div>
    </div>
@endsection
