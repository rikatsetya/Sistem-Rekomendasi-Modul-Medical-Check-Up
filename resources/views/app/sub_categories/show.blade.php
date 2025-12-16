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
                        <h4 class="mb-1">Detail Sub Kategori</h4>
                        <p class="mb-0 text-muted">Informasi lengkap sub kategori yang dipilih</p>
                    </div>
                    <div class="d-flex align-content-center flex-wrap gap-2">
                        <a href="{{ route('sub-categories.index') }}" class="btn btn-label-secondary">
                            <i class="icon ion-md-return-left text-primary"></i> Kembali
                        </a>
                        @can('create', App\Models\SubCategory::class)
                            <a href="{{ route('sub-categories.create') }}" class="btn btn-primary">
                                <i class="icon ion-md-add"></i> Tambah Baru
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- Card Detail --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Detail Sub Kategori</h5>
                        </div>
                        <div class="card-body">
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <small class="text-light fw-medium">Kategori</small>
                                    <p class="mb-0">{{ optional($subCategory->category)->name ?? '-' }}</p>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-light fw-medium">Nama Sub Kategori</small>
                                    <p class="mb-0">{{ $subCategory->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
