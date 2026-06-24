@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-6">
        <div class="col-xl-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-title">Kesimpulan & Saran Tahunan</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-sm-12">

            <div class="card-datatable table-responsive">
                <table id="kesimpulan-saran" class="dt-multilingual table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Kesimpulan</th>
                            <th class="text-center">Saran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['Kesimpulan'] ?? '-' }}</td>
                                <td class="text-center">{{ $item['Saran'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            // Helper function to clean and parse data for display
            $parseData = function ($data) {
                if (is_array($data)) {
                    return $data;
                }
                $decoded = json_decode($data, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
                return array_filter(array_map('trim', explode('•', $data)));
            };

            $notes = $parseData($notesNow ?? '');
            $makanan = $parseData($makananNow ?? '');
            $olahraga = $parseData($olahragaNow ?? '');
        @endphp
        <div class="col-xl-12 col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fw-bold mb-1">Rekomendasi Pola Makan & Olahraga</h4>
                        <p class="text-muted">Laporan personal untuk gaya hidup sehat Anda.</p>
                    </div>
                    <form method="GET" class="d-inline-block">
                        <select name="tahun" class="form-select select2" onchange="this.form.submit()">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
        {{-- Notes Section (Highlight) --}}
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">

                        <div>
                            <h6 class="mb-1 text-primary mb-2"><i class="ti tabler-info-circle ti-lg me-3"></i>Catatan Sistem
                            </h6>
                            @if (!empty($notes))
                                <ul class="mb-0 ps-3">
                                    @foreach ($notes as $item)
                                        <li class="mb-2">{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mb-0 text-primary opacity-75">Tidak ada catatan khusus untuk periode ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recommendations (Two Columns) --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="ti tabler-bowl-spoon text-primary me-2"></i>
                    <h5 class="card-title mb-0">Rekomendasi Pola Makan</h5>
                </div>
                <div class="card-body">
                    @if (!empty($makanan))
                        <ul class="list-unstyled mb-0">
                            @foreach ($makanan as $item)
                                <li class="d-flex mb-3">
                                    <i class="ti tabler-circle-check text-success me-2 mt-1 flex-shrink-0"></i>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="ti tabler-salad text-muted mb-2 flex-shrink-0"></i>
                            <p class="text-muted">Data pola makan belum tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="ti tabler-run text-primary me-2"></i>
                    <h5 class="card-title mb-0">Rekomendasi Olahraga</h5>
                </div>
                <div class="card-body">
                    @if (!empty($olahraga))
                        <ul class="list-unstyled mb-0">
                            @foreach ($olahraga as $item)
                                <li class="d-flex mb-3">
                                    <i class="ti tabler-circle-check text-primary me-2 mt-1 flex-shrink-0"></i>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="ti tabler-barbell text-muted mb-2 flex-shrink-0"></i>
                            <p class="text-muted">Data olahraga belum tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
