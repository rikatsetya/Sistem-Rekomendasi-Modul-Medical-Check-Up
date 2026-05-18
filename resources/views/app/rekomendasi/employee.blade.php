@extends('layouts.app')

@push('style')
<style>
    /* ---- Risk Score Badge ---- */
    .risk-hero {
        border-radius: 16px;
        padding: 2rem 1.5rem;
        text-align: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .risk-hero.sehat        { background: linear-gradient(135deg, #1a8a4a 0%, #20c997 100%); }
    .risk-hero.risiko-sedang{ background: linear-gradient(135deg, #c0560c 0%, #ffc107 100%); }
    .risk-hero.risiko-tinggi{ background: linear-gradient(135deg, #8b0000 0%, #dc3545 100%); }
    .risk-hero h2            { font-size: 3rem; font-weight: 800; opacity:.95; margin:0; }
    .risk-hero .label        { font-size: 1.1rem; opacity:.85; font-weight:600; }
    .risk-hero .sub          { font-size:.85rem; opacity:.7; }
    .risk-hero::after {
        content:''; position:absolute; top:-40px; right:-40px;
        width:180px; height:180px; border-radius:50%;
        background: rgba(255,255,255,.08);
    }

    /* ---- Recommendation cards ---- */
    .rec-card { border-radius:12px; border:none; box-shadow:0 2px 12px rgba(0,0,0,.07); }
    .rec-card .card-header { border-radius:12px 12px 0 0; }
    .rec-card .rec-text { white-space: pre-wrap; font-size:.875rem; line-height:1.7; }

    /* ---- Score progress ---- */
    .score-bar { height:14px; border-radius:8px; background:#e9ecef; overflow:hidden; }
    .score-bar-fill { height:100%; border-radius:8px; transition: width .8s ease; }

    /* ---- Info row ---- */
    .info-label { font-size:.75rem; color:#9aa0ac; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
    .info-value { font-size:.95rem; font-weight:600; }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- =====================================================================
     HEADER
     ===================================================================== --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-0">
                <i class="ti tabler-heartbeat text-danger me-2"></i>
                Rekomendasi Kesehatan Saya
            </h4>
            <small class="text-muted">{{ $user->name }}</small>
        </div>
    </div>
</div>

@forelse($recommendations as $rec)
    @php
        $heroClass = match($rec->risk_label) {
            'Sehat'         => 'sehat',
            'Risiko Sedang' => 'risiko-sedang',
            default         => 'risiko-tinggi',
        };
        $scoreColor = match($rec->risk_label) {
            'Sehat'         => '#20c997',
            'Risiko Sedang' => '#ffc107',
            default         => '#dc3545',
        };
    @endphp

    <div class="row g-4 mb-5">

        {{-- === Risk Score Hero === --}}
        <div class="col-lg-4">
            <div class="risk-hero {{ $heroClass }} h-100">
                <div class="sub mb-1">Skor Risiko Kesehatan</div>
                <h2>{{ number_format($rec->risk_score, 1) }}</h2>
                <div class="sub mb-3">dari 100</div>
                <div class="label">{{ $rec->risk_label }}</div>
                <div class="sub mt-2">Tahun MCU: {{ $rec->tahun }}</div>

                <div class="mt-3 px-2">
                    <div class="score-bar">
                        <div class="score-bar-fill"
                             style="width:{{ $rec->risk_score }}%;
                                    background:#fff; opacity:.5;">
                        </div>
                    </div>
                </div>

                <div class="mt-3 small opacity-75">
                    <i class="ti tabler-stethoscope me-1"></i>
                    Divalidasi oleh: {{ $rec->doctor?->name ?? '—' }}<br>
                    {{ $rec->validated_at?->format('d M Y') ?? '' }}
                </div>
            </div>
        </div>

        {{-- === MCU Values Summary === --}}
        <div class="col-lg-8">
            <div class="card rec-card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ti tabler-clipboard-list me-2"></i>Ringkasan Nilai MCU</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $mcuItems = [
                                ['label'=>'IMT (BMI)',       'value'=>$rec->bmi,          'unit'=>'kg/m²'],
                                ['label'=>'Sistolik',        'value'=>$rec->sistolik,     'unit'=>'mmHg'],
                                ['label'=>'Diastolik',       'value'=>$rec->diastolik,    'unit'=>'mmHg'],
                                ['label'=>'Glukosa Puasa',   'value'=>$rec->glukosa_puasa,'unit'=>'mg/dL'],
                                ['label'=>'Kolesterol Total','value'=>$rec->kolesterol,   'unit'=>'mg/dL'],
                                ['label'=>'Asam Urat',       'value'=>$rec->asam_urat,    'unit'=>'mg/dL'],
                                ['label'=>'Trigliserida',    'value'=>$rec->trigliserida, 'unit'=>'mg/dL'],
                            ];
                        @endphp
                        @foreach($mcuItems as $item)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="info-label">{{ $item['label'] }}</div>
                                <div class="info-value">
                                    {{ $item['value'] ?? '—' }}
                                    <small class="text-muted fw-normal">{{ $item['unit'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Doctor notes if any --}}
            @if($rec->doctor_notes)
                <div class="alert alert-info d-flex gap-2 align-items-start mb-3">
                    <i class="ti tabler-message-2 mt-1"></i>
                    <div>
                        <div class="fw-semibold small mb-1">Catatan Dokter</div>
                        {{ $rec->doctor_notes }}
                    </div>
                </div>
            @endif
        </div>

        {{-- === Rekomendasi Diet === --}}
        <div class="col-lg-6">
            <div class="card rec-card h-100">
                <div class="card-header" style="background:linear-gradient(135deg,#1a8a4a,#20c997); color:#fff;">
                    <h6 class="mb-0 text-white">
                        <i class="ti tabler-salad me-2"></i>Rekomendasi Pola Makan
                    </h6>
                </div>
                <div class="card-body">
                    <p class="rec-text mb-0">{{ $rec->rec_diet }}</p>
                </div>
            </div>
        </div>

        {{-- === Rekomendasi Olahraga === --}}
        <div class="col-lg-6">
            <div class="card rec-card h-100">
                <div class="card-header" style="background:linear-gradient(135deg,#1456a0,#4d9ef7); color:#fff;">
                    <h6 class="mb-0 text-white">
                        <i class="ti tabler-run me-2"></i>Rekomendasi Olahraga
                    </h6>
                </div>
                <div class="card-body">
                    <p class="rec-text mb-0">{{ $rec->rec_exercise }}</p>
                </div>
            </div>
        </div>

        {{-- === Catatan Tambahan === --}}
        @if($rec->rec_notes)
            <div class="col-12">
                <div class="card rec-card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ti tabler-notes me-2 text-warning"></i>Catatan</h6>
                    </div>
                    <div class="card-body">
                        <p class="rec-text mb-0">{{ $rec->rec_notes }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12"><hr class="border-2"></div>

    </div>{{-- /row per rec --}}

@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="ti tabler-heart-off ti-xl text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Belum ada rekomendasi yang tersedia</h5>
            <p class="text-muted small">
                Rekomendasi kesehatan akan ditampilkan setelah dokter memvalidasi hasil analisis MCU Anda.
            </p>
        </div>
    </div>
@endforelse

@endsection
