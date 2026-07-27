@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti tabler-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti tabler-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4>Recommendation Rules</h4>
                </div>
                <div>

                    <a href="{{ route('rekomendasi.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>
                        back
                    </a>
                    <a href="{{ route('recommendation-rules.create') }}" class="btn btn-primary">
                        <i class="ti tabler-plus me-2"></i>
                        Add Rule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="ti tabler-users me-2"></i>
                Aturan Rekomendasi
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="dataTables_wrapper dt-light ">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    {{-- SHOW ENTRIES --}}
                    <div class="">
                        <form method="GET" class="d-flex align-items-center gap-2 mb-0">
                            {{-- keep existing params --}}
                            <input type="hidden" name="search" value="{{ request('search') }}">

                            <label class="mb-0 small fw-semibold">Show</label>

                            <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach ([10, 25, 50] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>

                            <span class="small">entries</span>
                        </form>
                    </div>

                    {{-- SEARCH --}}
                    <div class="">
                        <form method="GET" class="d-flex align-items-center gap-2 mb-0">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                            <span class="small fw-semibold">Search</span>
                            <input type="search" name="search" value="{{ request('search') }}"
                                placeholder="Cari karyawan..." class="form-control form-control-sm">
                        </form>
                    </div>
                </div>
                <table class="dataTable table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kelompok</th>
                            <th>Tingkat Risiko</th>
                            <th>Kategori</th>
                            <th>Rentang Nilai</th>
                            <th>Rekomendasi</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rules as $i => $rule)
                            <tr>
                                <td class="text-center text-muted small">
                                    {{ $rules->firstItem() + $i }}
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $rule->group_code)) }}</td>
                                <td class="text-center">
                                    @if ($rule)
                                        @php
                                            $riskClass = match ($rule->severity_level) {
                                                'ringan' => 'bg-label-success',
                                                'sedang' => 'bg-label-warning',
                                                default => 'bg-label-danger',
                                            };
                                        @endphp
                                        <span
                                            class="badge {{ $riskClass }}">{{ ucfirst($rule->severity_level) }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">Belum generate</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($rule->category) }}</td>
                                <td>{{ $rule->min_score }} – {{ $rule->max_score }}</td>
                                <td>{{ $rule->recommendation_text }}</td>
                                <td class="text-center" style="width: 160px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group demo-inline-spacing">

                                        {{-- Edit --}}
                                        @can('update', $rule)
                                            <a href="{{ route('recommendation-rules.edit', encrypt($rule->id)) }}">
                                                <span class="badge text-bg-primary">
                                                    <i class="icon-base ti tabler-edit icon-me sm-2"></i>
                                                    Edit
                                                </span>
                                            </a>
                                        @endcan

                                        {{-- Show --}}
                                        @can('view', $rule)
                                            <a href="{{ route('recommendation-rules.show', encrypt($rule->id)) }}">
                                                <span class="badge text-bg-warning">
                                                    <i class="icon-base ti tabler-eye icon-me sm-2"></i>
                                                    Show
                                                </span>
                                            </a>
                                        @endcan

                                        {{-- Delete --}}
                                        @can('delete', $rule)
                                            <form action="{{ route('recommendation-rules.destroy', encrypt($rule->id)) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Yakin ingin menghapus rule ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <span class="icon-base ti tabler-trash icon-xs sm-2"></span>
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No recommendation rules found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center px-3 py-2 flex-wrap gap-2">

                    {{-- INFO TEXT --}}
                    <div class="dataTables_info">
                        Menampilkan
                        {{ $rules->firstItem() ?? 0 }}
                        –
                        {{ $rules->lastItem() ?? 0 }}
                        dari
                        {{ $rules->total() }}
                        rule rekomendasi
                    </div>

                    <div class="dataTables_paginate paging_simple_numbers">

                        {{-- PREVIOUS --}}
                        @if ($rules->onFirstPage())
                            <span class="paginate_button disabled">‹</span>
                        @else
                            <a href="{{ $rules->previousPageUrl() }}" class="paginate_button">‹</a>
                        @endif

                        {{-- PAGE NUMBERS --}}
                        @include('layouts.smart-pagination', ['paginator' => $rules])

                        {{-- NEXT --}}
                        @if ($rules->hasMorePages())
                            <a href="{{ $rules->nextPageUrl() }}" class="paginate_button">›</a>
                        @else
                            <span class="paginate_button disabled">›</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
