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
                        <h4 class="text-title">Pemeriksaan Penunjang Tahunan</h4>
                    </div>
                </div>
            </div>

            {{-- Tabel 1 (Tahun + 4 item) --}}
            <div class="col-xl-6 col-md-12 col-sm-12">
                <div class="card-datatable table-responsive">
                    <table id="non-lab-1" class="dt-multilingual table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Tahun</th>
                                <th class="text-center">ECG</th>
                                <th class="text-center">Treadmill</th>
                                <th class="text-center">PSA</th>
                                <th class="text-center">Foto Thorax</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tandaVital as $item)
                                <tr>
                                    <td class="text-center">{{ $item['tahun'] }}</td>
                                    <td class="text-center">{{ $item['ECG'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['Treadmill'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['PSA'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['Foto Thorax'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">@lang('crud.common.no_items_found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel 2 (Tahun + 5 item) --}}
            <div class="col-xl-6 col-md-12 col-sm-12">
                <div class="card-datatable table-responsive">
                    <table id="non-lab-2" class="dt-multilingual table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Tahun</th>
                                <th class="text-center">APO-B</th>
                                <th class="text-center">Spirometri</th>
                                <th class="text-center">Echo</th>
                                <th class="text-center">Audiometri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tandaVital as $item)
                                <tr>
                                    <td class="text-center">{{ $item['tahun'] }}</td>
                                    <td class="text-center">{{ $item['APO-B'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['Spirometri'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['Echo'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['Audiometri'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">@lang('crud.common.no_items_found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
</div>
@endsection
