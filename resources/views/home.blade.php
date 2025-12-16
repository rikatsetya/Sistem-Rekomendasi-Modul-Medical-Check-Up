@extends('layouts.app')

@section('content')
@if($subCategory->isEmpty())
        {{-- Empty State --}}
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ti tabler-database-off display-1 text-muted mb-4"></i>
                        <h3>No Data Available</h3>
                        <p class="text-muted">
                            The system has no health check categories yet. 
                            Please contact your administrator to set up the initial data.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
    @php
        // ambil tahun yang dipilih dari URL (default tahun sekarang)
        $selectedYear = request('tahun', now()->year);

        // ambil kategori MCU
        $mcu = $subCategory->where('name', 'Kategori')->first();
        $sistolik = $subCategory->where('name', 'Tekanan darah Sistolik (mmHg)')->first();
        $diastolik = $subCategory->where('name', 'Tekanan darah Diastolik (mmHg)')->first();
        $bmi = $subCategory->where('name', 'IMT (kg/m2)')->first();
        $hemoglobin = $subCategory->where('name', 'Hemoglobin')->first();
        $trombosit = $subCategory->where('name', 'Trombosit')->first();
        $got = $subCategory->where('name', 'GOT')->first();
        $hba1c = $subCategory->where('name', 'HbA1c (NGSP)')->first();
        $kreatinin = $subCategory->where('name', 'Kreatinin')->first();
        $pH = $subCategory->where('name', 'pH')->first();
        $thorax = $subCategory->where('name', 'Foto Thorax')->first();
        $spirometri = $subCategory->where('name', 'Spirometri')->first();
        $kesimpulan = $subCategory->where('name', 'Kesimpulan')->first();
        $saran = $subCategory->where('name', 'Saran')->first();
        $beratJenis = $subCategory->where('name', 'Berat Jenis')->first();
    @endphp
    <div class="row g-6">
        <div class="col-xl-12 col-sm-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <!-- Title -->
                    <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12 align-items-center">
                        <h4 class="card-title">Dashboard</h4>
                    </div>

                    <!-- Tahun -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 d-flex align-items-center">
                        <label for="tahun" class="form-label mb-0 me-2">Tahun</label>
                        @php
                            $currentYear = now()->year;
                            $years = range($currentYear - 20, $currentYear);
                            rsort($years);
                        @endphp
                        <select id="tahun" name="tahun" class="form-select select2">
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nama Terdaftar -->
                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 d-flex align-items-center">
                        <label for="user_id" class="form-label mb-0 me-2">Nama Terdaftar</label>
                        <select name="user_id" id="user_id" class="form-select select2">
                            @foreach ($user as $value => $users)
                                @php
                                    $encryptedId = encrypt($value);
                                @endphp
                                <option value="{{ $encryptedId }}"
                                    {{ $userId == $value ? 'selected' : '' }}>
                                    {{ $users }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @include('app.statistic.kategori-mcu')
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100" role="button" data-bs-toggle="modal" data-bs-target="#modalKategoriMCU">
                @php
                    $mcuNow = $mcu->values->where('tahun', $selectedYear);
                    $mcuBefore = $mcu->values->where('tahun', $selectedYear - 1);

                    $mcuMap = [ 
                        'M1A' => ['rank' => 1, 'desc' => 'Fit', 'color' => 'success'],
                        'M1B' => [
                            'rank' => 2,
                            'desc' => 'Fit Dengan problem tidak serius',
                            'color' => 'success',
                        ],
                        'M2' => ['rank' => 3, 'desc' => 'Fit dengan resiko Rendah', 'color' => 'success'],
                        'M3A' => ['rank' => 4, 'desc' => 'Fit dengan resiko Sedang', 'color' => 'warning'],
                        'M3B' => ['rank' => 5, 'desc' => 'Fit dengan Resiko Tinggi', 'color' => 'warning'],
                        'M4' => ['rank' => 6, 'desc' => 'Temporary Unfit', 'color' => 'danger'],
                        'M5' => ['rank' => 7, 'desc' => 'Unfit', 'color' => 'danger'],
                    ];

                    $currentNilai = $mcuNow->first()->nilai ?? null;
                    $beforeNilai = $mcuBefore->first()->nilai ?? null;

                    $current = $currentNilai ? $mcuMap[$currentNilai] : null;
                    $before = $beforeNilai ? $mcuMap[$beforeNilai] : null;

                    $isImproved = $current && $before && $current['rank'] < $before['rank'];
                    $isWorse = $current && $before && $current['rank'] > $before['rank'];
                    $isEqual = $current && $before && $current['rank'] == $before['rank'];
                @endphp

                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <h6>Kategori MCU</h6>
                        <div class="d-flex align-items-center">
                            @if ($isImproved)
                                <i class="icon-base ti tabler-caret-up-filled text-success"></i>
                            @elseif($isWorse)
                                <i class="icon-base ti tabler-caret-down-filled text-danger"></i>
                            @endif
                            <h4 class="card-text fw-bold ms-1 text-{{ $current['color'] ?? 'secondary' }}">
                                {{ $mcuNow->first()->nilai ?? '-' }}
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <p class="mb-0">Now</p>
                            @if ($current)
                                <h5 class="badge bg-label-{{ $current['color'] ?? 'secondary' }} rounded">
                                    {{ $mcuNow->first()->tahun ?? '-' }}
                                </h5>
                                <h5>{{ $mcuNow->first()->nilai ?? '-' }}</h5>
                                <small class="text-muted d-block">{{ $current['desc'] }}</small>
                            @else
                                <h5 class="badge bg-label-secondary">-</h5>
                            @endif
                        </div>

                        <div class="col-4 d-flex align-items-center justify-content-center">
                            <div class="divider divider-vertical">
                                <div class="divider-text">
                                    <span class="badge-divider-bg bg-label-secondary">
                                        @if ($isImproved)
                                            <i class="icon-base ti tabler-caret-up-filled text-success"></i>
                                        @elseif($isWorse)
                                            <i class="icon-base ti tabler-caret-down-filled text-danger"></i>
                                        @elseif ($isEqual)
                                            <i class="icon-base ti tabler-equal text-success"></i>
                                        @else
                                            <i class="icon-base ti tabler-circle text-secondary"></i>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-4 text-end">
                            <p class="mb-0">Before</p>
                            @if ($before)
                                <h5 class="badge bg-label-{{ $before['color'] ?? 'secondary' }} rounded">
                                    {{ $mcuBefore->first()->tahun ?? '-' }}
                                </h5>
                                <h5>{{ $mcuBefore->first()->nilai ?? '-' }}</h5>
                                <small class="text-muted d-block">{{ $before['desc'] }}</small>
                            @else
                                <h5 class="badge bg-label-secondary">-</h5>
                            @endif
                        </div>
                    </div>

                    {{-- progress bars etc. --}}
                    <div class="d-flex align-items-center mt-3">
                        <div class="progress w-100" style="height: 10px">
                            <div class="progress-bar bg-{{ $current['color'] ?? 'secondary' }}" style="width: 50%">
                            </div>
                            <div class="progress-bar bg-{{ $before['color'] ?? 'secondary' }}" style="width: 50%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sistolik Card (Tanda Vital) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('tanda.vital', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $sistolikMap = [
                            'hypotension' => [
                                'max' => 89,
                                'color' => '#00cfe8',
                                'bg' => 'info',
                                'desc' => 'Hipotensi',
                                'icon' => 'tabler-arrow-down',
                            ],
                            'normal' => [
                                'min' => 90,
                                'max' => 120,
                                'color' => '#28c76f',
                                'bg' => 'success',
                                'desc' => 'Normal',
                                'icon' => 'tabler-check',
                            ],
                            'prehypertension' => [
                                'min' => 121,
                                'max' => 139,
                                'color' => '#ff9f43',
                                'bg' => 'warning',
                                'desc' => 'Pra-Hipertensi',
                                'icon' => 'tabler-alert-triangle',
                            ],
                            'hypertension' => [
                                'min' => 140,
                                'max' => 300,
                                'color' => '#ea5455',
                                'bg' => 'danger',
                                'desc' => 'Hipertensi',
                                'icon' => 'tabler-arrow-up',
                            ],
                        ];
                        $sistolikValue = $sistolik->values->where('tahun', $selectedYear)->first()->nilai ?? null;
                        if ($sistolikValue !== null) {
                            $sistolikValue = round($sistolikValue, 1);
                        }
                        $color = '#6c757d';
                        $desc = 'Tidak diketahui';
                        $bgClass = 'secondary';
                        $icon = 'tabler-help';

                        if ($sistolikValue !== null) {
                            if ($sistolikValue < 90) {
                                $color = $sistolikMap['hypotension']['color'];
                                $desc = $sistolikMap['hypotension']['desc'];
                                $bgClass = $sistolikMap['hypotension']['bg'];
                                $icon = $sistolikMap['hypotension']['icon'];
                            } elseif ($sistolikValue <= 120) {
                                $color = $sistolikMap['normal']['color'];
                                $desc = $sistolikMap['normal']['desc'];
                                $bgClass = $sistolikMap['normal']['bg'];
                                $icon = $sistolikMap['normal']['icon'];
                            } elseif ($sistolikValue <= 139) {
                                $color = $sistolikMap['prehypertension']['color'];
                                $desc = $sistolikMap['prehypertension']['desc'];
                                $bgClass = $sistolikMap['prehypertension']['bg'];
                                $icon = $sistolikMap['prehypertension']['icon'];
                            } else {
                                $color = $sistolikMap['hypertension']['color'];
                                $desc = $sistolikMap['hypertension']['desc'];
                                $bgClass = $sistolikMap['hypertension']['bg'];
                                $icon = $sistolikMap['hypertension']['icon'];
                            }
                        }
                        $percent = $sistolikValue !== null ? min(100, ($sistolikValue / 180) * 100) : 0;
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-primary pb-2 pt-2">
                        <small class="text-primary fw-semibold">
                            <i class="icon-base ti tabler-heart-rate-monitor me-1"></i>TANDA VITAL
                        </small>
                    </div>

                    <div class="card-body pb-1 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $bgClass }}">
                                        <i class="icon-base ti {{ $icon }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Sistolik</h6>
                                    <small class="text-muted">Tekanan Darah</small>
                                </div>
                            </div>
                            <span class="badge bg-label-{{ $bgClass }}">{{ $desc }}</span>
                        </div>

                        <div id="sistolikGauge" class="mt-2"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var sistolikValue = {{ $sistolikValue ?? 0 }};
                            var options = {
                                chart: {
                                    height: 160,
                                    sparkline: {
                                        enabled: true
                                    },
                                    parentHeightOffset: 0,
                                    type: 'radialBar',
                                    offsetY: 10
                                },
                                series: [{{ $percent }}],
                                colors: ['{{ $color }}'],
                                plotOptions: {
                                    radialBar: {
                                        startAngle: -90,
                                        endAngle: 90,
                                        hollow: {
                                            size: '70%'
                                        },
                                        track: {
                                            background: '#f0f0f0',
                                            startAngle: -90,
                                            endAngle: 90,
                                        },
                                        dataLabels: {
                                            show: true,
                                            name: {
                                                show: false
                                            },
                                            value: {
                                                formatter: function() {
                                                    return sistolikValue + ' mmHg';
                                                },
                                                fontSize: '18px',
                                                offsetY: 5,
                                                color: '{{ $color }}'
                                            }
                                        }
                                    }
                                },
                                labels: ['Progress']
                            };

                            var chart = new ApexCharts(document.querySelector("#sistolikGauge"), options);
                            chart.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- Diastolik Card (Tanda Vital) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('tanda.vital', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $diastolik = $subCategory->where('name', 'Tekanan darah Diastolik (mmHg)')->first();
                        $diastolikMap = [
                            'hypotension' => [
                                'max' => 59,
                                'color' => '#00cfe8',
                                'bg' => 'info',
                                'desc' => 'Hipotensi',
                                'icon' => 'tabler-arrow-down',
                            ],
                            'normal' => [
                                'min' => 60,
                                'max' => 80,
                                'color' => '#28c76f',
                                'bg' => 'success',
                                'desc' => 'Normal',
                                'icon' => 'tabler-check',
                            ],
                            'prehypertension' => [
                                'min' => 81,
                                'max' => 89,
                                'color' => '#ff9f43',
                                'bg' => 'warning',
                                'desc' => 'Pra-Hipertensi',
                                'icon' => 'tabler-alert-triangle',
                            ],
                            'hypertension' => [
                                'min' => 90,
                                'max' => 200,
                                'color' => '#ea5455',
                                'bg' => 'danger',
                                'desc' => 'Hipertensi',
                                'icon' => 'tabler-arrow-up',
                            ],
                        ];
                        $diastolikValue = $diastolik->values->where('tahun', $selectedYear)->first()->nilai ?? null;
                        if ($diastolikValue !== null) {
                            $diastolikValue = round($diastolikValue, 1);
                        }
                        $colorDia = '#6c757d';
                        $descDia = 'Tidak diketahui';
                        $bgDia = 'secondary';
                        $iconDia = 'tabler-help';

                        if ($diastolikValue !== null) {
                            if ($diastolikValue < 60) {
                                $colorDia = $diastolikMap['hypotension']['color'];
                                $descDia = $diastolikMap['hypotension']['desc'];
                                $bgDia = $diastolikMap['hypotension']['bg'];
                                $iconDia = $diastolikMap['hypotension']['icon'];
                            } elseif ($diastolikValue <= 80) {
                                $colorDia = $diastolikMap['normal']['color'];
                                $descDia = $diastolikMap['normal']['desc'];
                                $bgDia = $diastolikMap['normal']['bg'];
                                $iconDia = $diastolikMap['normal']['icon'];
                            } elseif ($diastolikValue <= 89) {
                                $colorDia = $diastolikMap['prehypertension']['color'];
                                $descDia = $diastolikMap['prehypertension']['desc'];
                                $bgDia = $diastolikMap['prehypertension']['bg'];
                                $iconDia = $diastolikMap['prehypertension']['icon'];
                            } else {
                                $colorDia = $diastolikMap['hypertension']['color'];
                                $descDia = $diastolikMap['hypertension']['desc'];
                                $bgDia = $diastolikMap['hypertension']['bg'];
                                $iconDia = $diastolikMap['hypertension']['icon'];
                            }
                        }
                        $percentDia = $diastolikValue !== null ? min(100, ($diastolikValue / 120) * 100) : 0;
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-primary pb-2 pt-2">
                        <small class="text-primary fw-semibold">
                            <i class="icon-base ti tabler-heart-rate-monitor me-1"></i>TANDA VITAL
                        </small>
                    </div>

                    <div class="card-body pb-1 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $bgDia }}">
                                        <i class="icon-base ti {{ $iconDia }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Diastolik</h6>
                                    <small class="text-muted">Tekanan Darah</small>
                                </div>
                            </div>
                            <span class="badge bg-label-{{ $bgDia }}">{{ $descDia }}</span>
                        </div>

                        <div id="diastolikGauge" class="mt-2"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var diastolikValue = {{ $diastolikValue ?? 0 }};
                            var optionsDia = {
                                chart: {
                                    height: 160,
                                    sparkline: {
                                        enabled: true
                                    },
                                    parentHeightOffset: 0,
                                    type: 'radialBar',
                                    offsetY: 10
                                },
                                series: [{{ $percentDia }}],
                                colors: ['{{ $colorDia }}'],
                                plotOptions: {
                                    radialBar: {
                                        startAngle: -90,
                                        endAngle: 90,
                                        hollow: {
                                            size: '70%'
                                        },
                                        track: {
                                            background: '#f0f0f0',
                                            startAngle: -90,
                                            endAngle: 90,
                                        },
                                        dataLabels: {
                                            show: true,
                                            name: {
                                                show: false
                                            },
                                            value: {
                                                formatter: function() {
                                                    return diastolikValue + ' mmHg';
                                                },
                                                fontSize: '18px',
                                                offsetY: 5,
                                                color: '{{ $colorDia }}'
                                            }
                                        }
                                    }
                                },
                                labels: ['Progress']
                            };

                            var chartDia = new ApexCharts(document.querySelector("#diastolikGauge"), optionsDia);
                            chartDia.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- BMI Card (Tanda Vital) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('tanda.vital', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $bmiData = $bmi->values->sortBy('tahun');
                        $bmiYears = $bmiData->pluck('tahun')->toArray();
                        $bmiValues = $bmiData->pluck('nilai')->map(fn($val) => round($val, 1))->toArray();
                        $currentBmi = $bmi->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $bmiColor = 'secondary';
                        $bmiStatus = 'No Data';
                        $bmiIcon = 'tabler-help';

                        if ($currentBmi !== null) {
                            if ($currentBmi < 18.5) {
                                $bmiColor = 'info';
                                $bmiStatus = 'Underweight';
                                $bmiIcon = 'tabler-arrow-down';
                            } elseif ($currentBmi <= 24.9) {
                                $bmiColor = 'success';
                                $bmiStatus = 'Normal';
                                $bmiIcon = 'tabler-check';
                            } elseif ($currentBmi <= 29.9) {
                                $bmiColor = 'warning';
                                $bmiStatus = 'Overweight';
                                $bmiIcon = 'tabler-alert-circle';
                            } else {
                                $bmiColor = 'danger';
                                $bmiStatus = 'Obese';
                                $bmiIcon = 'tabler-alert-triangle';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-primary pb-2 pt-2">
                        <small class="text-primary fw-semibold">
                            <i class="icon-base ti tabler-heart-rate-monitor me-1"></i>TANDA VITAL
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $bmiColor }}">
                                        <i class="icon-base ti {{ $bmiIcon }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">BMI</h6>
                                    <small class="text-muted">Body Mass Index</small>
                                </div>
                            </div>
                            <h3 class="mb-0 text-{{ $bmiColor }}">{{ $currentBmi ?? '-' }}</h3>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $bmiColor }}">{{ $bmiStatus }}</span>
                        </div>
                        <div id="bmiTrend"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var bmiYears = @json($bmiYears);
                            var bmiValues = @json($bmiValues);


                            var optionsBmi = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'BMI',
                                    data: bmiValues
                                }],
                                xaxis: {
                                    categories: bmiYears,
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    },
                                },
                                yaxis: {
                                    min: 0,
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    },
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                }
                            };

                            var chartBmi = new ApexCharts(document.querySelector("#bmiTrend"), optionsBmi);
                            chartBmi.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- Hemoglobin Card (Komponen Darah) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('komponen.darah', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $hbData = $hemoglobin->values->sortBy('tahun');
                        $hbYears = $hbData->pluck('tahun')->toArray();
                        $hbValues = $hbData->pluck('nilai')->map(fn($val) => round($val, 1))->toArray();
                        $currentHb = $hemoglobin->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $hbColor = 'secondary';
                        $hbStatus = 'Unknown';
                        $hbIcon = 'tabler-help';

                        if ($currentHb !== null) {
                            if ($currentHb >= 13.8 && $currentHb <= 17.2) {
                                $hbColor = 'success';
                                $hbStatus = 'Normal';
                                $hbIcon = 'tabler-check';
                            } elseif ($currentHb < 13.8) {
                                $hbColor = 'danger';
                                $hbStatus = 'Low';
                                $hbIcon = 'tabler-arrow-down';
                            } else {
                                $hbColor = 'warning';
                                $hbStatus = 'High';
                                $hbIcon = 'tabler-arrow-up';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-danger pb-2 pt-2">
                        <small class="text-danger fw-semibold">
                            <i class="icon-base ti tabler-droplet-filled me-1"></i>KOMPONEN DARAH
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $hbColor }}">
                                        <i class="icon-base ti tabler-droplet"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Hemoglobin</h6>
                                    <small class="text-muted">g/dL</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 text-{{ $hbColor }}">
                                    {{ $currentHb !== null ? number_format($currentHb, 1) : '-' }}
                                </h3>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $hbColor }}">
                                <i class="icon-base ti {{ $hbIcon }} me-1"></i>{{ $hbStatus }}
                            </span>
                        </div>
                        <div id="hemoglobinTrend"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var hbYears = @json($hbYears);
                            var hbValues = @json($hbValues);

                            var optionsHb = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'Hemoglobin',
                                    data: hbValues
                                }],
                                xaxis: {
                                    categories: hbYears,
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                yaxis: {
                                    min: 0,
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#e50000']
                            };

                            var chartHb = new ApexCharts(document.querySelector("#hemoglobinTrend"), optionsHb);
                            chartHb.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- Trombosit Card (Komponen Darah) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('komponen.darah', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $plateletData = $trombosit->values->sortBy('tahun');
                        $plateletYears = $plateletData->pluck('tahun')->toArray();
                        $plateletValues = $plateletData->pluck('nilai')->map(fn($val) => round($val, 1))->toArray();
                        $currentPlatelet = $trombosit->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $plateletColor = 'secondary';
                        $plateletStatus = 'Unknown';
                        $plateletIcon = 'tabler-help';

                        if ($currentPlatelet !== null) {
                            if ($currentPlatelet >= 150 && $currentPlatelet <= 450) {
                                $plateletColor = 'success';
                                $plateletStatus = 'Normal';
                                $plateletIcon = 'tabler-check';
                            } elseif ($currentPlatelet < 150) {
                                $plateletColor = 'danger';
                                $plateletStatus = 'Low';
                                $plateletIcon = 'tabler-arrow-down';
                            } else {
                                $plateletColor = 'warning';
                                $plateletStatus = 'High';
                                $plateletIcon = 'tabler-arrow-up';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-danger pb-2 pt-2">
                        <small class="text-danger fw-semibold">
                            <i class="icon-base ti tabler-droplet-filled me-1"></i>KOMPONEN DARAH
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $plateletColor }}">
                                        <i class="icon-base ti tabler-cell"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Trombosit</h6>
                                    <small class="text-muted">x10³/µL</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 text-{{ $plateletColor }}">
                                    {{ $currentPlatelet !== null ? number_format($currentPlatelet, 0) : '-' }}
                                </h3>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $plateletColor }}">
                                <i class="icon-base ti {{ $plateletIcon }} me-1"></i>{{ $plateletStatus }}
                            </span>
                        </div>
                        <div id="plateletChart"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var plateletOptions = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'Trombosit (x10³/µL)',
                                    data: @json($plateletValues)
                                }],
                                xaxis: {
                                    categories: @json($plateletYears),
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(0); // tanpa desimal
                                        }
                                    },
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#e50000']
                            };

                            var plateletChart = new ApexCharts(document.querySelector("#plateletChart"), plateletOptions);
                            plateletChart.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- GOT Card (Kimia Darah) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('kimia.darah', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $gotData = $got->values->sortBy('tahun');
                        $gotYears = $gotData->pluck('tahun')->toArray();
                        $gotValues = $gotData->pluck('nilai')->map(fn($val) => round($val, 1))->toArray();
                        $currentGot = $got->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $gotColor = 'secondary';
                        $gotStatus = 'Unknown';
                        $gotIcon = 'tabler-help';

                        if ($currentGot !== null) {
                            if ($currentGot >= 8 && $currentGot <= 40) {
                                $gotColor = 'success';
                                $gotStatus = 'Normal';
                                $gotIcon = 'tabler-check';
                            } elseif ($currentGot < 8) {
                                $gotColor = 'danger';
                                $gotStatus = 'Low';
                                $gotIcon = 'tabler-arrow-down';
                            } else {
                                $gotColor = 'warning';
                                $gotStatus = 'High';
                                $gotIcon = 'tabler-arrow-up';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-info pb-2 pt-2">
                        <small class="text-info fw-semibold">
                            <i class="icon-base ti tabler-flask me-1"></i>KIMIA DARAH
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $gotColor }}">
                                        <i class="icon-base ti tabler-activity"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">GOT (SGOT)</h6>
                                    <small class="text-muted">U/L</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 text-{{ $gotColor }}">
                                    {{ $currentGot !== null ? number_format($currentGot, 1) : '-' }}
                                </h3>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $gotColor }}">
                                <i class="icon-base ti {{ $gotIcon }} me-1"></i>{{ $gotStatus }}
                            </span>
                        </div>
                        <div id="gotChart"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var gotOptions = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'GOT (U/L)',
                                    data: @json($gotValues)
                                }],
                                xaxis: {
                                    categories: @json($gotYears),
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(1); // 1 desimal
                                        }
                                    },
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#187bcd'] // warna ungu untuk GOT
                            };

                            var gotChart = new ApexCharts(document.querySelector("#gotChart"), gotOptions);
                            gotChart.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- HbA1c Card (Kimia Darah) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('kimia.darah', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $hba1cData = $hba1c->values->sortBy('tahun');
                        $hba1cYears = $hba1cData->pluck('tahun')->toArray();
                        $hba1cValues = $hba1cData->pluck('nilai')->map(fn($val) => round($val, 1))->toArray();
                        $currentHba1c = $hba1c->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $hba1cColor = 'secondary';
                        $hba1cStatus = 'Unknown';
                        $hba1cIcon = 'tabler-help';

                        if ($currentHba1c !== null) {
                            if ($currentHba1c < 5.7) {
                                $hba1cColor = 'success';
                                $hba1cStatus = 'Normal';
                                $hba1cIcon = 'tabler-check';
                            } elseif ($currentHba1c <= 6.4) {
                                $hba1cColor = 'warning';
                                $hba1cStatus = 'Pre-diabetes';
                                $hba1cIcon = 'tabler-alert-circle';
                            } else {
                                $hba1cColor = 'danger';
                                $hba1cStatus = 'Diabetes';
                                $hba1cIcon = 'tabler-alert-triangle';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-info pb-2 pt-2">
                        <small class="text-info fw-semibold">
                            <i class="icon-base ti tabler-flask me-1"></i>KIMIA DARAH
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $hba1cColor }}">
                                        <i class="icon-base ti tabler-percentage"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">HbA1c</h6>
                                    <small class="text-muted">NGSP</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 text-{{ $hba1cColor }}">
                                    {{ $currentHba1c !== null ? number_format($currentHba1c, 1) . '%' : '-' }}
                                </h3>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $hba1cColor }}">
                                <i class="icon-base ti {{ $hba1cIcon }} me-1"></i>{{ $hba1cStatus }}
                            </span>
                        </div>
                        <div id="hba1cChart"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var hba1cOptions = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'HbA1c (%)',
                                    data: @json($hba1cValues)
                                }],
                                xaxis: {
                                    categories: @json($hba1cYears),
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(1) + '%'; // 1 desimal + tanda %
                                        }
                                    },
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#187bcd'] // warna oranye untuk HbA1c
                            };

                            var hba1cChart = new ApexCharts(document.querySelector("#hba1cChart"), hba1cOptions);
                            hba1cChart.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- Kreatinin Card (Kimia Darah) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('kimia.darah', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $creatinineData = $kreatinin->values->sortBy('tahun');
                        $creatinineYears = $creatinineData->pluck('tahun')->toArray();
                        $creatinineValues = $creatinineData->pluck('nilai')->map(fn($val) => round($val, 2))->toArray();
                        $currentCreatinine = $kreatinin->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $creatinineColor = 'secondary';
                        $creatinineStatus = 'Unknown';
                        $creatinineIcon = 'tabler-help';

                        if ($currentCreatinine !== null) {
                            if ($currentCreatinine >= 0.7 && $currentCreatinine <= 1.3) {
                                $creatinineColor = 'success';
                                $creatinineStatus = 'Normal';
                                $creatinineIcon = 'tabler-check';
                            } elseif ($currentCreatinine < 0.7) {
                                $creatinineColor = 'danger';
                                $creatinineStatus = 'Low';
                                $creatinineIcon = 'tabler-arrow-down';
                            } else {
                                $creatinineColor = 'warning';
                                $creatinineStatus = 'High';
                                $creatinineIcon = 'tabler-arrow-up';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-info pb-2 pt-2">
                        <small class="text-info fw-semibold">
                            <i class="icon-base ti tabler-flask me-1"></i>KIMIA DARAH
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $creatinineColor }}">
                                        <i class="icon-base ti tabler-test-pipe"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Kreatinin</h6>
                                    <small class="text-muted">Faal Ginjal</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 text-{{ $creatinineColor }}">
                                    {{ $currentCreatinine !== null ? number_format($currentCreatinine, 2) : '-' }}
                                </h3>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $creatinineColor }}">
                                <i class="icon-base ti {{ $creatinineIcon }} me-1"></i>{{ $creatinineStatus }}
                            </span>
                        </div>
                        <div id="creatinineChart"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var creatinineOptions = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'Kreatinin (mg/dL)',
                                    data: @json($creatinineValues)
                                }],
                                xaxis: {
                                    categories: @json($creatinineYears),
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(2); // 2 desimal
                                        }
                                    },
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#187bcd'] // warna biru muda untuk kreatinin
                            };

                            var creatinineChart = new ApexCharts(document.querySelector("#creatinineChart"), creatinineOptions);
                            creatinineChart.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- pH Urin Card (Urin Rutin) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('urin.rutin', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $phData = $pH->values->sortBy('tahun');
                        $phYears = $phData->pluck('tahun')->toArray();
                        $phValues = $phData->pluck('nilai')->map(fn($val) => round($val, 2))->toArray();
                        $currentPh = $pH->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $phColor = 'secondary';
                        $phStatus = 'Unknown';
                        $phIcon = 'tabler-help';

                        if ($currentPh !== null) {
                            if ($currentPh >= 4.5 && $currentPh <= 8.0) {
                                $phColor = 'success';
                                $phStatus = 'Normal';
                                $phIcon = 'tabler-check';
                            } elseif ($currentPh < 4.5) {
                                $phColor = 'danger';
                                $phStatus = 'Acidic';
                                $phIcon = 'tabler-arrow-down';
                            } else {
                                $phColor = 'warning';
                                $phStatus = 'Alkaline';
                                $phIcon = 'tabler-arrow-up';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-warning pb-2 pt-2">
                        <small class="text-warning fw-semibold">
                            <i class="icon-base ti tabler-droplet-half-2 me-1"></i>URIN RUTIN
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $phColor }}">
                                        <i class="icon-base ti tabler-chart-dots"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">pH Urin</h6>
                                    <small class="text-muted">Tingkat Keasaman</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 text-{{ $phColor }}">
                                    {{ $currentPh !== null ? number_format($currentPh, 1) : '-' }}
                                </h3>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $phColor }}">
                                <i class="icon-base ti {{ $phIcon }} me-1"></i>{{ $phStatus }}
                            </span>
                        </div>
                        <div id="phChart"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var phOptions = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'pH Urin',
                                    data: @json($phValues)
                                }],
                                xaxis: {
                                    categories: @json($phYears),
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                yaxis: {
                                    min: 0,
                                    max: 10,
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(1);
                                        }
                                    },
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#ffd700'] // ungu untuk pH
                            };

                            var phChart = new ApexCharts(document.querySelector("#phChart"), phOptions);
                            phChart.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        {{-- Berat Jenis Card (Urin Rutin) --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('urin.rutin', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}"
                class="text-decoration-none">
                <div class="card h-100 card-hover-shadow">
                    @php
                        $bjData = $beratJenis->values->sortBy('tahun');
                        $bjYears = $bjData->pluck('tahun')->toArray();
                        $bjValues = $bjData->pluck('nilai')->map(fn($val) => round($val, 3))->toArray();
                        $currentBJ = $beratJenis->values->where('tahun', $selectedYear)->first()->nilai ?? null;

                        $bjColor = 'secondary';
                        $bjStatus = 'Unknown';
                        $bjIcon = 'tabler-help';

                        if ($currentBJ !== null) {
                            if ($currentBJ >= 1.005 && $currentBJ <= 1.03) {
                                $bjColor = 'success';
                                $bjStatus = 'Normal';
                                $bjIcon = 'tabler-check';
                            } elseif ($currentBJ < 1.005) {
                                $bjColor = 'info';
                                $bjStatus = 'Rendah';
                                $bjIcon = 'tabler-arrow-down';
                            } else {
                                $bjColor = 'warning';
                                $bjStatus = 'Tinggi';
                                $bjIcon = 'tabler-arrow-up';
                            }
                        }
                    @endphp

                    {{-- Category Header --}}
                    <div class="card-header border-bottom bg-label-warning pb-2 pt-2">
                        <small class="text-warning fw-semibold">
                            <i class="icon-base ti tabler-droplet-half-2 me-1"></i>URIN RUTIN
                        </small>
                    </div>

                    <div class="card-body pb-2 pt-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $bjColor }}">
                                        <i class="icon-base ti tabler-weight"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">Berat Jenis</h6>
                                    <small class="text-muted">Specific Gravity</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 text-{{ $bjColor }}">
                                    {{ $currentBJ !== null ? number_format($currentBJ, 3) : '-' }}
                                </h3>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-label-{{ $bjColor }}">
                                <i class="icon-base ti {{ $bjIcon }} me-1"></i>{{ $bjStatus }}
                            </span>
                        </div>
                        <div id="bjChart"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const vuexyColors = getVuexyThemeColors();
                            var bjOptions = {
                                chart: {
                                    type: 'line',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    }
                                },
                                series: [{
                                    name: 'Berat Jenis',
                                    data: @json($bjValues)
                                }],
                                xaxis: {
                                    categories: @json($bjYears),
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                yaxis: {
                                    min: 1.000,
                                    max: 1.040,
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(3);
                                        }
                                    },
                                    labels: {
                                        style: {
                                            colors: vuexyColors.text
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#ffd700'] // biru cyan khas Vuexy
                            };

                            var bjChart = new ApexCharts(document.querySelector("#bjChart"), bjOptions);
                            bjChart.render();
                        });
                    </script>
                </div>
            </a>
        </div>

        <style>
            .card-hover-shadow {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(0, 0, 0, 0.08);
            }

            .card-hover-shadow:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
            }

            .card-header {
                transition: all 0.3s ease;
            }

            a.text-decoration-none:hover {
                text-decoration: none !important;
            }
        </style>

        <div class="col-xl-3 col-sm-6">
            <div class="row g-6">
                <div class="col-xl-12 col-sm-12">
                    <a href="{{ route('pemeriksaan.diagnostik', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}">
                        <div class="card">
                            @php
                                // Ambil kesimpulan untuk tahun terpilih
                                $thoraxNow = $thorax->values->where('tahun', $selectedYear)->first()->nilai ?? null;
                            @endphp
                            <div class="card-header">
                                <div class="d-flex justify-content-between w-100">
                                    <h6>Foto Thorax</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-primary">
                                            {{ $selectedYear }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($thoraxNow)
                                    <p class="mb-0 text-body">
                                        {{ $thoraxNow }}
                                    </p>
                                @else
                                    <p class="mb-0 text-muted fst-italic">
                                        Tidak ada Foto Thorax untuk tahun ini.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-12 col-sm-12">
                    <a href="{{ route('pemeriksaan.diagnostik', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}">
                        <div class="card">
                            @php
                                // Ambil kesimpulan untuk tahun terpilih
                                $spirometriNow = $spirometri->values->where('tahun', $selectedYear)->first()->nilai ?? null;
                            @endphp
                            <div class="card-header">
                                <div class="d-flex justify-content-between w-100">
                                    <h6>Spirometri</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-primary">
                                            {{ $selectedYear }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($spirometriNow)
                                    <p class="mb-0 text-body">
                                        {{ $spirometriNow }}
                                    </p>
                                @else
                                    <p class="mb-0 text-muted fst-italic">
                                        Tidak ada Spirometri untuk tahun ini.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-sm-6">
            <a href="{{ route('kesimpulan.saran', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}">
                <div class="card h-100">
                    @php
                        // Ambil kesimpulan untuk tahun terpilih
                        $kesimpulanNow = $kesimpulan->values->where('tahun', $selectedYear)->first()->nilai ?? null;
                    @endphp
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h6>Kesimpulan</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-primary">
                                    {{ $selectedYear }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($kesimpulanNow)
                            <p class="mb-0 text-body">
                                {{ $kesimpulanNow }}
                            </p>
                        @else
                            <p class="mb-0 text-muted fst-italic">
                                Tidak ada kesimpulan untuk tahun ini.
                            </p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-6 col-sm-6">
            <a href="{{ route('kesimpulan.saran', ['id' => encrypt($userId) ?? encrypt(Auth::user()->id)]) }}">
                <div class="card h-100">
                    @php
                        // Ambil kesimpulan untuk tahun terpilih
                        $saranNow = $saran->values->where('tahun', $selectedYear)->first()->nilai ?? null;
                    @endphp
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h6>Saran</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-primary">
                                    {{ $selectedYear }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($saranNow)
                            <p class="mb-0 text-body">
                                {{ $saranNow }}
                            </p>
                        @else
                            <p class="mb-0 text-muted fst-italic">
                                Tidak ada saran untuk tahun ini.
                            </p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endif
@endsection
@push('scripts')
    <script>
        $('#tahun').on('change', function() {
            const selectedYear = $(this).val(); // pakai .val()
            const selectedUser = $('#user_id').val(); // pakai .val()
            window.location.href = `?tahun=${selectedYear}&user_id=${selectedUser}`;
        });

        $('#user_id').on('change', function() {
            const selectedUser = $(this).val(); // pakai .val()
            const selectedYear = $('#tahun').val(); // pakai .val()
            window.location.href = `?tahun=${selectedYear}&user_id=${selectedUser}`;
        });
    </script>
@endpush
