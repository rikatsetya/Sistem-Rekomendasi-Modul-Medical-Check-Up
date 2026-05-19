@extends('layouts.app')

@push('style')
<style>
    /* ---- Stat cards ---- */
    .stat-card            { border-left: 4px solid; border-radius: 10px; }
    .stat-card.pending    { border-color: #8a8a8aff; }
    .stat-card.approved   { border-color: #ffc107; }
    .stat-card.rejected   { border-color: #28a745; }

    /* ---- Risk label colours (text) ---- */
    .risk-sehat   { color: #1a8a4a; font-weight: 600; }
    .risk-sedang  { color: #c0560c; font-weight: 600; }
    .risk-tinggi  { color: #b91c1c; font-weight: 600; }

    /* ---- Table row hover ---- */
    #rekomendasiTable tbody tr { transition: background .15s; }

    /* ---- Toolbar gap ---- */
    .toolbar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
</style>
@endpush

@section('content')

{{-- =========================================================
     FLASH MESSAGES
     ========================================================= --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti tabler-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ti tabler-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- =========================================================
     HEADER CARD
     ========================================================= --}}
<div class="card mb-4">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="ti tabler-heartbeat text-danger me-2"></i>Sistem Rekomendasi Kesehatan
            <span class="badge bg-label-secondary ms-2 fs-6 align-middle">Fuzzy Mamdani</span>
        </h4>
        <small class="text-muted">
            Bangkitkan &amp; validasi rekomendasi diet dan olahraga berbasis data MCU per tahun
        </small>
    </div>
</div>

{{-- =========================================================
     TOOLBAR  —  Tahun Filter  |  Generate Semua  |  Accept All
     ========================================================= --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="toolbar">

            {{-- ── (A) Filter tahun: GET form → reload tabel ── --}}
            <form method="GET" action="{{ route('rekomendasi.index') }}"
                  class="d-flex align-items-center gap-2">
                <label class="fw-semibold small mb-0 text-nowrap">
                    <i class="ti tabler-calendar me-1"></i>Tahun MCU
                </label>
                <select name="tahun"
                        id="yearSelect"
                        class="form-select form-select-sm"
                        style="min-width:110px;"
                        onchange="syncYear(this.value); this.form.submit();">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}"
                                {{ $year == $selectedYear ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>

            <span class="text-muted">|</span>

            {{-- ── (B) Generate Semua: POST form — pakai tahun yang sama ── --}}
            <form method="POST" action="{{ route('rekomendasi.generate-all') }}">
                @csrf
                {{-- hidden year synced via JS when dropdown changes --}}
                <input type="hidden" name="tahun" id="genYear" value="{{ $selectedYear }}">
                <button type="submit"
                        class="btn btn-primary btn-sm"
                        onclick="return confirm('Generate rekomendasi untuk SEMUA karyawan tahun ' + document.getElementById('genYear').value + '?\n\nSemua status akan berubah menjadi Generated.')">
                    <i class="ti tabler-refresh me-1"></i>Generate Rekomendasi
                </button>
            </form>

            {{-- spacer --}}
            <span class="flex-grow-1"></span>

            {{-- ── (C) Publish All: publish semua generated (draft) ── --}}
@if($generatedCount > 0)
    <form method="POST" action="{{ route('rekomendasi.publish-all') }}">
        @csrf
        <input type="hidden" name="tahun" id="publishYear" value="{{ $selectedYear }}">
        <button type="submit"
                class="btn btn-success btn-sm"
                onclick="return confirm(
                    'Publish SEMUA {{ $generatedCount }} rekomendasi untuk tahun ' +
                    document.getElementById('publishYear').value +
                    '?\n\nData akan terlihat oleh karyawan.'
                )">
            <i class="ti tabler-upload me-1"></i>Publish Data
            <span class="badge bg-white text-success ms-1">{{ $generatedCount }}</span>
        </button>
    </form>
@else
    <button class="btn btn-success btn-sm" disabled title="Tidak ada data untuk dipublish">
        <i class="ti tabler-upload me-1"></i>Publish Data
        <span class="badge bg-white text-muted ms-1">0</span>
    </button>
@endif

        </div>
    </div>
</div>

{{-- =========================================================
     STAT CARDS —  Pending / Approved / Rejected
     ========================================================= --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card pending h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-label-secondary rounded">
                    <i class="ti tabler-clock-hour4 ti-lg"></i>
                </div>
                <div>
                    <div class="text-muted small">Data Mentah ({{ $selectedYear }})</div>
<div class="fs-3 fw-bold text-secondary">{{ $rawCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card approved h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-label-warning rounded">
                    <i class="ti tabler-circle-check ti-lg"></i>
                </div>
                <div>
                    <div class="text-muted small">Hasil Generate ({{ $selectedYear }})</div>
<div class="fs-3 fw-bold text-warning">{{ $generatedCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card rejected h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-label-success rounded">
                    <i class="ti tabler-circle-x ti-lg"></i>
                </div>
                <div>
                    <div class="text-muted small">Sudah Dipublish ({{ $selectedYear }})</div>
<div class="fs-3 fw-bold text-success">{{ $publishedCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     TABEL DATA KARYAWAN & REKOMENDASI
     ========================================================= --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="ti tabler-users me-2"></i>
            Data Karyawan &amp; Rekomendasi
            <span class="badge bg-label-primary ms-2">Tahun {{ $selectedYear }}</span>
        </h5>
        <span class="text-muted small">{{ $users->count() }} karyawan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="rekomendasiTable"
                   class="dt-multilingual table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:48px;">#</th>
                        <th>Nama Karyawan</th>
                        <th class="text-center">Gender</th>
                        <th class="text-center">Risk Score</th>
                        <th class="text-center">Risiko</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                        @php
                            /** @var \App\Models\Recommendation|null $rec */
                            $rec = $recsByUser->get($user->id);
                        @endphp
                        <tr>
                            <td class="text-center text-muted small">{{ $i + 1 }}</td>

                            {{-- Nama + Kopeg + Divisi --}}
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <small class="text-muted">
                                    {{ $user->kopeg }}
                                    @if($user->divisi)
                                        &middot; {{ Str::limit($user->divisi, 40) }}
                                    @endif
                                </small>
                            </td>

                            {{-- Gender --}}
                            <td class="text-center">
                                <span class="badge {{ $user->gender === 'l' ? 'bg-label-primary' : 'bg-label-danger' }}">
                                    {{ $user->gender === 'l' ? 'L' : 'P' }}
                                </span>
                            </td>

                            {{-- Risk Score --}}
                            <td class="text-center">
                                @if($rec)
                                    <span class="fw-bold">{{ number_format($rec->risk_score, 1) }}</span>
                                    <small class="text-muted">/100</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Risk Label --}}
                            <td class="text-center">
                                @if($rec)
                                    @php
                                        $riskClass = match($rec->risk_label) {
                                            'Sehat'         => 'bg-label-success',
                                            'Risiko Sedang' => 'bg-label-warning',
                                            default         => 'bg-label-danger',
                                        };
                                    @endphp
                                    <span class="badge {{ $riskClass }}">{{ $rec->risk_label }}</span>
                                @else
                                    <span class="badge bg-label-secondary">Belum generate</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if(!$rec)
                                    <span class="badge bg-label-secondary">Raw</span>
                                @elseif($rec->status === 'draft')
                                    <span class="badge bg-label-warning">Generated</span>
                                @elseif($rec->status === 'published')
                                    <span class="badge bg-label-success">Published</span>
                                @else
                                    <span class="badge bg-label-secondary">Unknown</span>
                                @endif
                            </td>

                            {{-- Aksi: hanya tombol Detail (muncul jika ada rekomendasi) --}}
                            <td class="text-center">
                                @if($rec)
                                    <a href="{{ route('rekomendasi.show', $rec->id) }}?tahun={{ $selectedYear }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Lihat detail &amp; validasi rekomendasi">
                                        <i class="ti tabler-eye me-1"></i>Detail
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="ti tabler-users-off ti-lg d-block mb-2"></i>
                                Belum ada data karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    /**
     * syncYear(year) — dipanggil saat dropdown tahun berubah.
     * Memperbarui field tersembunyi di form Generate dan Accept All
     * agar tahun yang di-POST sesuai dengan tahun yang dipilih.
     */
    function syncYear(year) {
        var g = document.getElementById('genYear');
        var p = document.getElementById('publishYear');
        if (g) g.value = year;
        if (p) p.value = year;
    }

    $(document).ready(function () {
        // Inisialisasi DataTable untuk tabel karyawan
        if (!$.fn.dataTable.isDataTable('#rekomendasiTable')) {
            $('#rekomendasiTable').DataTable({ responsive: true });
        }
    });
</script>
@endpush
