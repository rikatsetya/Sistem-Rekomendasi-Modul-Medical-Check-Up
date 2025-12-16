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
                        <h4 class="mb-1">Tambah User</h4>
                        <p class="mb-0 text-muted">Tambahkan user baru untuk sistem PPID</p>
                    </div>
                    <div class="d-flex align-content-center flex-wrap gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-label-secondary">
                            <i class="icon ion-md-return-left text-primary"></i> Batal
                        </a>
                        <button type="submit" form="userForm" class="btn btn-primary">
                            <i class="icon ion-md-save"></i> Simpan User
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <x-form method="POST" action="{{ route('users.store') }}" id="userForm" enctype="multipart/form-data">
                    @include('app.users.form-inputs')
                </x-form>
            </div>
        </div>
    </div>
@endsection
