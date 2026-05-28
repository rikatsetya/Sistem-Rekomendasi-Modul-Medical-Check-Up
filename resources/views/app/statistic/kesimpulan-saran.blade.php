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
        <div class="col-xl-12 col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="text-title mb-0">
                        Rekomendasi Pola Makan & Olahraga
                    </h4>

                    <form method="GET" class="m-0">
                        <select name="tahun" class="form-select select2" onchange="this.form.submit()">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-title">Catatan Sistem</h4>
                </div>
                <div class="card-body">
                    @if ($notesNow)
                        @php
                            if (is_array($notesNow)) {
                                $items = $notesNow;
                            } elseif (is_string($notesNow)) {
                                // Coba decode JSON dulu
                                $decoded = json_decode($notesNow, true);

                                if (is_array($decoded)) {
                                    $items = $decoded;
                                } else {
                                    // Pecah berdasarkan bullet "•"
                                    $items = array_filter(array_map('trim', explode('•', $notesNow)));
                                }
                            } else {
                                $items = [];
                            }
                        @endphp

                        @if (!empty($items))
                            <ul class="mb-0 ps-3">
                                @foreach ($items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-body">{{ $notesNow }}</p>
                        @endif
                    @else
                        <p class="mb-0 text-muted fst-italic">
                            Konten rekomendasi pola makan akan ditampilkan di sini.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-title">Rekomendasi Pola Makan</h4>
                </div>
                <div class="card-body">
                    @if ($makananNow)
                        @php
                            if (is_array($makananNow)) {
                                $items = $makananNow;
                            } elseif (is_string($makananNow)) {
                                // Coba decode JSON dulu
                                $decoded = json_decode($makananNow, true);

                                if (is_array($decoded)) {
                                    $items = $decoded;
                                } else {
                                    // Pecah berdasarkan bullet "•"
                                    $items = array_filter(array_map('trim', explode('•', $makananNow)));
                                }
                            } else {
                                $items = [];
                            }
                        @endphp

                        @if (!empty($items))
                            <ul class="mb-0 ps-3">
                                @foreach ($items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-body">{{ $makananNow }}</p>
                        @endif
                    @else
                        <p class="mb-0 text-muted fst-italic">
                            Konten rekomendasi pola makan akan ditampilkan di sini.
                        </p>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-xl-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-title">Rekomendasi Olahraga</h4>
                </div>
                <div class="card-body">
                    @if ($olahragaNow)
                        @php
                            if (is_array($olahragaNow)) {
                                $items = $olahragaNow;
                            } elseif (is_string($olahragaNow)) {
                                // Coba decode JSON dulu
                                $decoded = json_decode($olahragaNow, true);

                                if (is_array($decoded)) {
                                    $items = $decoded;
                                } else {
                                    // Pecah berdasarkan bullet "•"
                                    $items = array_filter(array_map('trim', explode('•', $olahragaNow)));
                                }
                            } else {
                                $items = [];
                            }
                        @endphp

                        @if (!empty($items))
                            <ul class="mb-0 ps-3">
                                @foreach ($items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-body">{{ $olahragaNow }}</p>
                        @endif
                    @else
                        <p class="mb-0 text-muted fst-italic">
                            Konten rekomendasi olahraga akan ditampilkan di sini.
                        </p>
                    @endif

                </div>

            </div>
        </div>
    </div>
@endsection
