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

        /* ============================================================
                                   MEMBERSHIP BAR
                                   ============================================================ */
        .mf-bar-wrap {
            height: 16px;
            border-radius: 6px;
            background: #e9ecef;
            overflow: hidden;
        }

        .mf-bar {
            height: 100%;
            border-radius: 6px;
            transition: width .6s ease;
        }

        .mf-lbl {
            font-size: .72rem;
            color: #6c757d;
            margin-bottom: 2px;
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
    {{-- ============================================================
     FLASH MESSAGES
     ============================================================ --}}
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
     NAVIGASI DATA (Prev / Position / Next)
     ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">

        {{-- Prev --}}
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

        {{-- Position --}}
        <span class="badge bg-label-primary">
            {{ $position }} / {{ $total }}
        </span>

        {{-- Next --}}
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
     MAIN ROW: Fuzzy Info (kiri) + Form Validasi (kanan)
     ============================================================ --}}
    <div class="row g-4">

        {{-- ========================================================
         KOLOM KIRI — Risk Score + Input Snapshot + MF bars
         ======================================================== --}}
        <div class="col-xl-4 col-lg-5">

            {{-- Risk Score card --}}
            <div class="card mb-4 text-center">
                <div class="card-body py-4">
                    <div class="text-muted small text-uppercase fw-semibold mb-1 letter-spacing-1">
                        Skor Risiko (Centroid CoA)
                    </div>
                    <div
                        class="score-gauge
                    {{ $rec->risk_label === 'Sehat'
                        ? 'score-sehat'
                        : ($rec->risk_label === 'Risiko Sedang'
                            ? 'score-sedang'
                            : 'score-tinggi') }}">
                        {{ number_format($rec->risk_score, 2) }}
                    </div>
                    <div class="text-muted small mb-3">dari 100</div>

                    @php
                        $riskBadge = match ($rec->risk_label) {
                            'Sehat' => 'bg-label-success',
                            'Risiko Sedang' => 'bg-label-warning',
                            default => 'bg-label-danger',
                        };
                    @endphp
                    <span class="badge {{ $riskBadge }} fs-6 px-4 py-2">{{ $rec->risk_label }}</span>

                    {{-- Progress bar visual --}}
                    <div class="mt-3 px-2">
                        <div class="mf-bar-wrap" style="height:14px;">
                            <div class="mf-bar"
                                style="width:{{ $rec->risk_score }}%;
                                    background: {{ $rec->risk_label === 'Sehat' ? '#1a8a4a' : ($rec->risk_label === 'Risiko Sedang' ? '#c0560c' : '#b91c1c') }};">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>0</span><span>Sehat · Sedang · Tinggi</span><span>100</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MCU Input Snapshot --}}
            <div class="card mb-4">
                <div class="card-header py-2">
                    <h6 class="mb-0"><i class="ti tabler-clipboard-list me-2"></i>Nilai MCU</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @php
                                $mcuRows = [
                                    ['IMT (BMI)', $rec->bmi, 'kg/m²'],
                                    ['Sistolik', $rec->sistolik, 'mmHg'],
                                    ['Diastolik', $rec->diastolik, 'mmHg'],
                                    ['Glukosa Puasa', $rec->glukosa_puasa, 'mg/dL'],
                                    ['Kolesterol Total', $rec->kolesterol, 'mg/dL'],
                                    ['Asam Urat', $rec->asam_urat, 'mg/dL'],
                                    ['Trigliserida', $rec->trigliserida, 'mg/dL'],
                                ];
                            @endphp
                            @foreach ($mcuRows as [$label, $val, $unit])
                                <tr>
                                    <td class="ps-3 text-muted small">{{ $label }}</td>
                                    <td class="fw-semibold text-end">{{ $val ?? '—' }}</td>
                                    <td class="pe-3 text-muted small text-end">{{ $unit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Membership Degree Visualisation --}}
            @php
                use App\Services\FuzzyMamdaniService;
                $fuzzy = app(FuzzyMamdaniService::class);
                $mfInputs = [
                    'bmi' => (float) $rec->bmi,
                    'sistolik' => (float) $rec->sistolik,
                    'glukosa' => (float) $rec->glukosa_puasa,
                    'kolesterol' => (float) $rec->kolesterol,
                    'asam_urat' => (float) $rec->asam_urat,
                    'trigliserida' => (float) $rec->trigliserida,
                    'gender' => $rec->user->gender ?? 'L',
                ];
                $m = $fuzzy->fuzzify($mfInputs);
                $ro = $fuzzy->applyRules($m);
            @endphp

            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0"><i class="ti tabler-chart-bar me-2"></i>Derajat Keanggotaan</h6>
                </div>
                <div class="card-body small">

                    {{-- BMI --}}
                    <div class="fw-semibold mt-1 mb-2">BMI</div>
                    @foreach (['kurus' => 'Kurus', 'normal' => 'Normal', 'gemuk' => 'Gemuk', 'obesitas' => 'Obesitas'] as $k => $lbl)
                        <div class="mf-lbl">{{ $lbl }}: {{ number_format($m['bmi'][$k], 3) }}</div>
                        <div class="mf-bar-wrap mb-2">
                            <div class="mf-bar bg-primary" style="width:{{ $m['bmi'][$k] * 100 }}%"></div>
                        </div>
                    @endforeach

                    {{-- Sistolik --}}
                    <div class="fw-semibold mt-3 mb-2">Sistolik</div>
                    @foreach (['normal' => 'Normal', 'pra_hiper' => 'Pra-Hiper.', 'hiper_g1' => 'Hiper G1', 'hiper_g2' => 'Hiper G2'] as $k => $lbl)
                        <div class="mf-lbl">{{ $lbl }}: {{ number_format($m['sistolik'][$k], 3) }}</div>
                        <div class="mf-bar-wrap mb-2">
                            <div class="mf-bar bg-info" style="width:{{ $m['sistolik'][$k] * 100 }}%"></div>
                        </div>
                    @endforeach

                    {{-- Glukosa --}}
                    <div class="fw-semibold mt-3 mb-2">Glukosa Puasa</div>
                    @foreach (['normal' => 'Normal', 'pra_dm' => 'Pra-DM', 'dm' => 'Diabetes'] as $k => $lbl)
                        <div class="mf-lbl">{{ $lbl }}: {{ number_format($m['glukosa'][$k], 3) }}</div>
                        <div class="mf-bar-wrap mb-2">
                            <div class="mf-bar bg-warning" style="width:{{ $m['glukosa'][$k] * 100 }}%"></div>
                        </div>
                    @endforeach

                    {{-- Output Agregasi --}}
                    <div class="fw-semibold mt-3 mb-2">Output Agregasi (MAX)</div>
                    <div class="mf-lbl">Sehat: {{ number_format($ro['sehat'], 3) }}</div>
                    <div class="mf-bar-wrap mb-2">
                        <div class="mf-bar bg-success" style="width:{{ $ro['sehat'] * 100 }}%"></div>
                    </div>
                    <div class="mf-lbl">Risiko Sedang: {{ number_format($ro['risiko_sedang'], 3) }}</div>
                    <div class="mf-bar-wrap mb-2">
                        <div class="mf-bar bg-warning" style="width:{{ $ro['risiko_sedang'] * 100 }}%"></div>
                    </div>
                    <div class="mf-lbl">Risiko Tinggi: {{ number_format($ro['risiko_tinggi'], 3) }}</div>
                    <div class="mf-bar-wrap mb-2">
                        <div class="mf-bar bg-danger" style="width:{{ $ro['risiko_tinggi'] * 100 }}%"></div>
                    </div>
                </div>
            </div>

        </div>{{-- /kolom kiri --}}

        {{-- ========================================================
         KOLOM KANAN — Form Rekomendasi + Tombol Validasi
         ======================================================== --}}
        <div class="col-xl-8 col-lg-7">

            {{-- =====================================================
             FORM VALIDASI
             ===================================================== --}}
            <form method="POST" action="{{ route('rekomendasi.update', $rec->id) }}" id="validateForm">
                @csrf
                @method('PUT')
                {{-- Tahun diteruskan sebagai hidden input agar controller bisa redirect kembali --}}
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

                        <textarea name="rec_diet" class="form-control" rows="8" id="rec_diet">
{{ old('rec_diet', $dietItems ? '• ' . implode("\n• ", $dietItems) : $rec->rec_diet) }}
</textarea>
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

                        <textarea name="rec_exercise" class="form-control" rows="8" id="rec_exercise">
{{ old('rec_exercise', $exerciseItems ? '• ' . implode("\n• ", $exerciseItems) : $rec->rec_exercise) }}
</textarea>
                    </div>
                </div>

                {{-- Catatan Sistem --}}
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="ti tabler-notes me-2 text-warning"></i>Catatan Sistem
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

                        <textarea name="rec_notes" class="form-control" rows="4" id="rec_notes">
{{ old('rec_notes', $noteItems ? "• " . implode("\n• ", $noteItems) : $rec->rec_notes) }}
</textarea>
                    </div>
                </div>

                {{-- Catatan Dokter --}}
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="ti tabler-message-2 me-2"></i>Catatan Dokter (opsional)
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $noteDoc = is_array($rec->doctor_notes)
                                ? $rec->doctor_notes
                                : (is_string($rec->doctor_notes)
                                    ? json_decode($rec->doctor_notes, true)
                                    : []);
                        @endphp

                        <textarea name="doctor_notes" class="form-control" rows="4" id="doctor_notes">
{{ old('doctor_notes', $noteDoc ? "• " . implode("\n• ", $noteDoc) : $rec->doctor_notes) }}
</textarea>
                    </div>
                    </div>
                    <button type="button"
             class="btn btn-primary px-4"
             data-bs-toggle="modal"
             data-bs-target="#confirmSaveModal">
         <i class="ti tabler-device-floppy me-2"></i>Simpan Perubahan
     </button>
                </div>


            </form>
            {{-- /validateForm --}}
            {{-- ============================================================
            MODAL KONFIRMASI SIMPAN PERUBAHAN
            ============================================================ --}}
            <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="ti tabler-alert-circle text-warning me-2"></i>
                                Konfirmasi Perubahan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p class="mb-0">
                                Anda yakin ingin <strong>menyimpan perubahan rekomendasi</strong> ini?
                            </p>
                            <small class="text-muted">
                                Data akan diperbarui dan status rekomendasi akan disimpan sebagai draft.
                            </small>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>

                            {{-- Tombol submit form --}}
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
