@extends('layouts.app')

@push('style')
    <style>
        /* ============================================================
                   RISK SCORE GAUGE
                   ============================================================ */
        .score-gauge {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1;
        }

        .score-sehat {
            color: #1a8a4a;
        }

        .score-sedang {
            color: #c0560c;
        }

        .score-tinggi {
            color: #b91c1c;
        }

        .score-kritis {
            color: #1a1a2e;
        }

        .mcu-section-label[aria-expanded="true"] .tabler-chevron-down {
            transform: rotate(180deg);
            transition: transform 0.2s ease;
        }

        .mcu-section-label .tabler-chevron-down {
            transition: transform 0.2s ease;
        }

        /* ============================================================
                   GROUP SCORE BARS
                   ============================================================ */
        .gs-bar-wrap {
            height: 14px;
            border-radius: 5px;
            background: #e9ecef;
            overflow: hidden;
        }

        .gs-bar {
            height: 100%;
            border-radius: 5px;
            transition: width .6s ease;
        }

        .gs-group-title {
            font-size: .79rem;
            font-weight: 700;
            margin-top: .75rem;
            margin-bottom: .3rem;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        /* ============================================================
                   MCU TABLE SECTION LABELS
                   ============================================================ */
        .mcu-section-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            background: #f8f9fa;
            padding: .3rem .75rem;
        }

        /* ============================================================
                   RECOMMENDATION TEXTAREAS
                   ============================================================ */
        .rec-area {
            min-height: 130px;
            font-size: .875rem;
            white-space: pre-wrap;
            font-family: inherit;
            resize: vertical;
        }

        /* ============================================================
                   STATUS BANNER
                   ============================================================ */
        .status-banner {
            border-radius: 10px;
            padding: .75rem 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .status-banner.pending {
            background: #fff8e1;
            color: #856404;
            border: 1px solid #ffd54f;
        }

        .status-banner.approved {
            background: #e8f5e9;
            color: #1a5c2a;
            border: 1px solid #a5d6a7;
        }

        .status-banner.rejected {
            background: #fce4e4;
            color: #7f1d1d;
            border: 1px solid #f48181;
        }
    </style>
@endpush

@section('content')

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ti tabler-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="ti tabler-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ============================================================
     HEADER
     ============================================================ --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="mb-0">
                    <i class="ti tabler-heartbeat text-danger me-2"></i>
                    {{ $rec->user->name }}
                </h4>
                <small class="text-muted">
                    Kopeg: <strong>{{ $rec->user->kopeg }}</strong>
                    &nbsp;|&nbsp; Gender:
                    <strong>{{ $rec->user->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                    &nbsp;|&nbsp; Tahun MCU: <strong>{{ $rec->tahun }}</strong>
                    &nbsp;|&nbsp; Generated: {{ $rec->created_at->format('d M Y, H:i') }}
                </small>
            </div>
            <a href="{{ route('rekomendasi.index', ['tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti tabler-arrow-left me-1"></i>Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- ============================================================
     NAVIGASI (Prev / Position / Next)
     ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        @if ($prevId)
            <a href="{{ route('rekomendasi.show', $prevId) }}?tahun={{ $tahun }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="ti tabler-arrow-left me-1"></i>Sebelumnya
            </a>
        @else
            <button class="btn btn-outline-secondary btn-sm" disabled>
                <i class="ti tabler-arrow-left me-1"></i>Sebelumnya
            </button>
        @endif

        <span class="badge bg-label-primary">{{ $position }} / {{ $total }}</span>

        @if ($nextId)
            <a href="{{ route('rekomendasi.show', $nextId) }}?tahun={{ $tahun }}"
                class="btn btn-outline-secondary btn-sm">
                Berikutnya<i class="ti tabler-arrow-right ms-1"></i>
            </a>
        @else
            <button class="btn btn-outline-secondary btn-sm" disabled>
                Berikutnya<i class="ti tabler-arrow-right ms-1"></i>
            </button>
        @endif
    </div>

    {{-- ============================================================
     MAIN ROW
     ============================================================ --}}
    <div class="row g-4">

        {{-- ============================================================
         KOLOM KIRI — Risk Score + Snapshot 23 Parameter + Skor 7 Kelompok
         ============================================================ --}}
        <div class="col-xl-4 col-lg-5">

            {{-- ── Risk Score Card ──────────────────────────────────────── --}}
            <div class="card mb-4 text-center">
                <div class="card-body py-4">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">
                        Skor Risiko Global (Centroid CoA)
                    </div>

                    @php
                        $scoreClass = match ($rec->risk_label) {
                            'Ringan' => 'score-sehat',
                            'Sedang' => 'score-sedang',
                            'Tinggi' => 'score-tinggi',
                            'Kritis' => 'score-kritis',
                            default => 'score-tinggi',
                        };
                        $barColor = match ($rec->risk_label) {
                            'Ringan' => '#1a8a4a',
                            'Sedang' => '#c0560c',
                            'Tinggi' => '#b91c1c',
                            'Kritis' => '#1a1a2e',
                            default => '#b91c1c',
                        };
                        $riskBadge = match ($rec->risk_label) {
                            'Ringan' => 'bg-label-success',
                            'Sedang' => 'bg-label-warning',
                            'Tinggi' => 'bg-label-danger',
                            'Kritis' => 'bg-label-dark',
                            default => 'bg-label-secondary',
                        };
                    @endphp

                    <div class="score-gauge {{ $scoreClass }}">
                        {{ number_format($rec->risk_score, 2) }}
                    </div>
                    <div class="text-muted small mb-3">dari 100</div>
                    <span class="badge {{ $riskBadge }} fs-6 px-4 py-2">{{ $rec->risk_label }}</span>

                    @if ($rec->duration)
                        <div class="mt-2 small text-muted">
                            <i class="ti tabler-calendar-time me-1"></i>
                            Durasi Program: <strong>{{ $rec->duration }}</strong>
                        </div>
                    @endif

                    {{-- Progress bar --}}
                    <div class="mt-3 px-2">
                        <div class="gs-bar-wrap" style="height:14px;">
                            <div class="gs-bar" style="width:{{ $rec->risk_score }}%; background:{{ $barColor }};">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>0</span>
                            <span>Ringan · Sedang · Tinggi · Kritis</span>
                            <span>100</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Skor Per Kelompok Fuzzy Hierarki ────────────────────── --}}
            @php
                // group_scores di-cast ke array oleh model Recommendation
                $groupScores = is_array($rec->group_scores)
                    ? $rec->group_scores
                    : (is_string($rec->group_scores)
                        ? json_decode($rec->group_scores, true)
                        : []);

                $groupMeta = [
                    'fungsi_hati' => ['label' => 'Fungsi Hati', 'icon' => '🫀', 'color' => '#e74c3c'],
                    'diabetes' => ['label' => 'Diabetes / Gula Darah', 'icon' => '🩸', 'color' => '#e67e22'],
                    'profil_lipid' => ['label' => 'Profil Lipid', 'icon' => '💊', 'color' => '#9b59b6'],
                    'fungsi_ginjal' => ['label' => 'Fungsi Ginjal', 'icon' => '🫘', 'color' => '#3498db'],
                    'asam_urat' => ['label' => 'Asam Urat', 'icon' => '🔬', 'color' => '#1abc9c'],
                    'kardiovaskular' => ['label' => 'Kardiovaskular & Vital', 'icon' => '❤️', 'color' => '#e84393'],
                    'antropometri' => ['label' => 'Antropometri & Obesitas', 'icon' => '⚖️', 'color' => '#27ae60'],
                ];
            @endphp

            <div class="card mb-4">
                <div class="card-header py-2">
                    <h6 class="mb-0">
                        <i class="ti tabler-chart-bar me-2"></i>Skor Per Kelompok Fuzzy
                    </h6>
                </div>
                <div class="card-body small">
                    @if (empty($groupScores))
                        <div class="text-muted text-center py-3">
                            <i class="ti tabler-info-circle me-1"></i>
                            Data skor kelompok belum tersedia. Generate ulang rekomendasi.
                        </div>
                    @else
                        @foreach ($groupMeta as $key => $meta)
                            @php
                                $gs = $groupScores[$key] ?? 0;
                                $gSev =
                                    $gs <= 29 ? 'Ringan' : ($gs <= 49 ? 'Sedang' : ($gs <= 69 ? 'Tinggi' : 'Kritis'));
                                $gSevClass = match ($gSev) {
                                    'Ringan' => 'text-success',
                                    'Sedang' => 'text-warning',
                                    'Tinggi' => 'text-danger',
                                    'Kritis' => 'text-dark',
                                    default => 'text-secondary',
                                };
                            @endphp
                            <div class="gs-group-title">
                                {{ $meta['icon'] }} {{ $meta['label'] }}
                                <span class="ms-auto fw-normal {{ $gSevClass }}">
                                    {{ number_format($gs, 1) }}
                                    <small class="text-muted">({{ $gSev }})</small>
                                </span>
                            </div>
                            <div class="gs-bar-wrap mb-2">
                                <div class="gs-bar" style="width:{{ $gs }}%; background:{{ $meta['color'] }};">
                                </div>
                            </div>
                        @endforeach

                        <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">Rata-rata Global</small>
                            <strong class="small">{{ number_format($rec->risk_score, 2) }} / 100</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Snapshot 23 Parameter MCU (dikelompokkan per 7 grup) ─── --}}
            {{-- ── Snapshot 23 Parameter MCU (dikelompokkan per 7 grup) ─── --}}
            <div class="card mb-4">
                <div class="card-header py-2">
                    <h6 class="mb-0">
                        <i class="ti tabler-clipboard-list me-2"></i>Nilai MCU (23 Parameter)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <tbody>

                            {{-- Kelompok 1 --}}
                            <tr>
                                <td colspan="3" class="mcu-section-label" style="cursor:pointer;"
                                    data-bs-toggle="collapse" data-bs-target="#mcu-group-1" aria-expanded="false">
                                    🫀 Fungsi Hati
                                    <i class="ti tabler-chevron-down float-end"></i>
                                </td>
                            </tr>
                            <tr class="collapse" id="mcu-group-1">
                                <td colspan="3" class="p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="ps-3 text-muted small">GOT (AST)</td>
                                            <td class="fw-semibold text-end">{{ $rec->got ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">U/L</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">GPT (ALT)</td>
                                            <td class="fw-semibold text-end">{{ $rec->gpt ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">U/L</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Kelompok 2 --}}
                            <tr>
                                <td colspan="3" class="mcu-section-label" style="cursor:pointer;"
                                    data-bs-toggle="collapse" data-bs-target="#mcu-group-2" aria-expanded="false">
                                    🩸 Diabetes / Gula Darah
                                    <i class="ti tabler-chevron-down float-end"></i>
                                </td>
                            </tr>
                            <tr class="collapse" id="mcu-group-2">
                                <td colspan="3" class="p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="ps-3 text-muted small">Glukosa Puasa</td>
                                            <td class="fw-semibold text-end">{{ $rec->glukosa_puasa ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Glukosa 2j PP</td>
                                            <td class="fw-semibold text-end">{{ $rec->glukosa_2j_pp ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">HbA1c (NGSP)</td>
                                            <td class="fw-semibold text-end">{{ $rec->hba1c ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">%</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Kelompok 3 --}}
                            <tr>
                                <td colspan="3" class="mcu-section-label" style="cursor:pointer;"
                                    data-bs-toggle="collapse" data-bs-target="#mcu-group-3" aria-expanded="false">
                                    💊 Profil Lipid
                                    <i class="ti tabler-chevron-down float-end"></i>
                                </td>
                            </tr>
                            <tr class="collapse" id="mcu-group-3">
                                <td colspan="3" class="p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="ps-3 text-muted small">Chol. Total</td>
                                            <td class="fw-semibold text-end">{{ $rec->chol_total ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Chol. LDL Direk</td>
                                            <td class="fw-semibold text-end">{{ $rec->chol_ldl ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Chol. HDL</td>
                                            <td class="fw-semibold text-end">{{ $rec->chol_hdl ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Trigliserida</td>
                                            <td class="fw-semibold text-end">{{ $rec->trigliserida ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Apo-B</td>
                                            <td class="fw-semibold text-end">{{ $rec->apo_b ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Kelompok 4 --}}
                            <tr>
                                <td colspan="3" class="mcu-section-label" style="cursor:pointer;"
                                    data-bs-toggle="collapse" data-bs-target="#mcu-group-4" aria-expanded="false">
                                    🫘 Fungsi Ginjal
                                    <i class="ti tabler-chevron-down float-end"></i>
                                </td>
                            </tr>
                            <tr class="collapse" id="mcu-group-4">
                                <td colspan="3" class="p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="ps-3 text-muted small">Urea N (BUN)</td>
                                            <td class="fw-semibold text-end">{{ $rec->urea_n ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Ureum</td>
                                            <td class="fw-semibold text-end">{{ $rec->ureum ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Kreatinin</td>
                                            <td class="fw-semibold text-end">{{ $rec->kreatinin ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">eLFG (CKD-EPI)</td>
                                            <td class="fw-semibold text-end">{{ $rec->elfg ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mL/min</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Kelompok 5 --}}
                            <tr>
                                <td colspan="3" class="mcu-section-label" style="cursor:pointer;"
                                    data-bs-toggle="collapse" data-bs-target="#mcu-group-5" aria-expanded="false">
                                    🔬 Asam Urat
                                    <i class="ti tabler-chevron-down float-end"></i>
                                </td>
                            </tr>
                            <tr class="collapse" id="mcu-group-5">
                                <td colspan="3" class="p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="ps-3 text-muted small">Asam Urat</td>
                                            <td class="fw-semibold text-end">{{ $rec->asam_urat ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mg/dL</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Kelompok 6 --}}
                            <tr>
                                <td colspan="3" class="mcu-section-label" style="cursor:pointer;"
                                    data-bs-toggle="collapse" data-bs-target="#mcu-group-6" aria-expanded="false">
                                    ❤️ Kardiovaskular & Tanda Vital
                                    <i class="ti tabler-chevron-down float-end"></i>
                                </td>
                            </tr>
                            <tr class="collapse" id="mcu-group-6">
                                <td colspan="3" class="p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="ps-3 text-muted small">Nadi</td>
                                            <td class="fw-semibold text-end">{{ $rec->nadi ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">kali/mnt</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Pernafasan</td>
                                            <td class="fw-semibold text-end">{{ $rec->pernafasan ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">kali/mnt</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">TD Sistolik</td>
                                            <td class="fw-semibold text-end">{{ $rec->sistolik ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mmHg</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">TD Diastolik</td>
                                            <td class="fw-semibold text-end">{{ $rec->diastolik ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">mmHg</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Kelompok 7 --}}
                            <tr>
                                <td colspan="3" class="mcu-section-label" style="cursor:pointer;"
                                    data-bs-toggle="collapse" data-bs-target="#mcu-group-7" aria-expanded="false">
                                    ⚖️ Antropometri & Obesitas
                                    <i class="ti tabler-chevron-down float-end"></i>
                                </td>
                            </tr>
                            <tr class="collapse" id="mcu-group-7">
                                <td colspan="3" class="p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="ps-3 text-muted small">Tinggi Badan</td>
                                            <td class="fw-semibold text-end">{{ $rec->tinggi_badan ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">cm</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Berat Badan</td>
                                            <td class="fw-semibold text-end">{{ $rec->berat_badan ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">kg</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">IMT (BMI)</td>
                                            <td class="fw-semibold text-end">{{ $rec->imt ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">kg/m²</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 text-muted small">Lingkar Perut</td>
                                            <td class="fw-semibold text-end">{{ $rec->lingkar_perut ?? '—' }}</td>
                                            <td class="pe-3 text-muted small text-end">cm</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- /kolom kiri --}}

        {{-- ============================================================
         KOLOM KANAN — Form Rekomendasi + Tombol Validasi
         ============================================================ --}}
        <div class="col-xl-8 col-lg-7">

            {{-- Form Validasi --}}
            <form method="POST" action="{{ route('rekomendasi.update', encrypt($rec->id)) }}" id="validateForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                {{-- Diet --}}
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="ti tabler-salad me-2 text-success"></i>Rekomendasi Pola Makan
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $dietItems = is_array($rec->rec_diet)
                                ? $rec->rec_diet
                                : (is_string($rec->rec_diet)
                                    ? json_decode($rec->rec_diet, true)
                                    : []);
                        @endphp
                        <textarea name="rec_diet" class="form-control rec-area" rows="8" id="rec_diet">{{ old('rec_diet', $dietItems ? '• ' . implode("\n• ", $dietItems) : $rec->rec_diet) }}</textarea>
                    </div>
                </div>

                {{-- Olahraga --}}
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="ti tabler-run me-2 text-primary"></i>Rekomendasi Olahraga
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $exerciseItems = is_array($rec->rec_exercise)
                                ? $rec->rec_exercise
                                : (is_string($rec->rec_exercise)
                                    ? json_decode($rec->rec_exercise, true)
                                    : []);
                        @endphp
                        <textarea name="rec_exercise" class="form-control rec-area" rows="8" id="rec_exercise">{{ old('rec_exercise', $exerciseItems ? '• ' . implode("\n• ", $exerciseItems) : $rec->rec_exercise) }}</textarea>
                    </div>
                </div>

                {{-- Catatan Sistem --}}
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="ti tabler-notes me-2 text-warning"></i>Catatan
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $noteItems = is_array($rec->rec_notes)
                                ? $rec->rec_notes
                                : (is_string($rec->rec_notes)
                                    ? json_decode($rec->rec_notes, true)
                                    : []);
                        @endphp
                        <textarea name="rec_notes" class="form-control rec-area" rows="8" id="rec_notes">{{ old('rec_notes', $noteItems ? '• ' . implode("\n• ", $noteItems) : $rec->rec_notes) }}</textarea>
                    </div>
                </div>

                <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal"
                    data-bs-target="#confirmSaveModal">
                    <i class="ti tabler-device-floppy me-2"></i>Simpan Perubahan
                </button>
            </form>

            {{-- Modal Konfirmasi --}}
            <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="ti tabler-alert-circle text-warning me-2"></i>Konfirmasi Perubahan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">Anda yakin ingin <strong>menyimpan perubahan rekomendasi</strong> ini?</p>
                            <small class="text-muted">
                                Data akan diperbarui dan status rekomendasi akan disimpan sebagai draft.
                            </small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary"
                                onclick="document.getElementById('validateForm').submit();">
                                <i class="ti tabler-device-floppy me-1"></i>Ya, Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /kolom kanan --}}

    </div>{{-- /row --}}
@endsection
