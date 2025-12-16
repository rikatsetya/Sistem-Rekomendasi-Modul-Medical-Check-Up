@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-6">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-title">Annual Urine Lengkap</h4>
                </div>
            </div>
        </div>

        {{-- Tabel 1 --}}
        <div class="col-xl-6">
            <div class="card-datatable">
                <table id="urin-rutin-1" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Warna</th>
                            <th class="text-center">pH</th>
                            <th class="text-center">Nitrit</th>
                            <th class="text-center">Albumin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['warna'] }}</td>
                                <td class="text-center">{{ $item['ph'] }}</td>
                                <td class="text-center">{{ $item['nitrit'] }}</td>
                                <td class="text-center">{{ $item['albumin'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel 1 --}}
        <div class="col-xl-6">
            <div class="card-datatable">
                <table id="urin-rutin-11" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Glukosa</th>
                            <th class="text-center">Keton</th>
                            <th class="text-center">Kejernihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['glukosa'] }}</td>
                                <td class="text-center">{{ $item['keton'] }}</td>
                                <td class="text-center">{{ $item['kejernihan'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel 2 --}}
        <div class="col-xl-6">
            <div class="card-datatable">
                <table id="urin-rutin-2" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Berat Jenis</th>
                            <th class="text-center">Urobilinogen</th>
                            <th class="text-center">Bilirubin</th>
                            <th class="text-center">Darah (Blood)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['berat_jenis'] }}</td>
                                <td class="text-center">{{ $item['urobilinogen'] }}</td>
                                <td class="text-center">{{ $item['bilirubin'] }}</td>
                                <td class="text-center">{{ $item['darah'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel 3 --}}
        <div class="col-xl-6">
            <div class="card-datatable">
                <table id="urin-rutin-3" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Leukosit (Urine)</th>
                            <th class="text-center">Silinder Hyalin</th>
                            <th class="text-center">Bakteri</th>
                            <th class="text-center">Kristal Abnormal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['leukosit_urine'] }}</td>
                                <td class="text-center">{{ $item['silinder_hyalin'] }}</td>
                                <td class="text-center">{{ $item['bakteri'] }}</td>
                                <td class="text-center">{{ $item['kristal_abnormal'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel 3 --}}
        <div class="col-xl-6">
            <div class="card-datatable">
                <table id="urin-rutin-4" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Silinder Lain-lain</th>
                            <th class="text-center">Epithel Gepeng</th>
                            <th class="text-center">Epithel Transitional</th>
                            <th class="text-center">Epithel Tubulus Ginjal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['silinder_lain'] }}</td>
                                <td class="text-center">{{ $item['epithel_gepeng'] }}</td>
                                <td class="text-center">{{ $item['epithel_trans'] }}</td>
                                <td class="text-center">{{ $item['epithel_tubulus'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel 3 --}}
        <div class="col-xl-6">
            <div class="card-datatable">
                <table id="urin-rutin-5" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Kristal Normal</th>
                            <th class="text-center">Lain-lain</th>
                            <th class="text-center">Leukosit Esterase</th>
                            <th class="text-center">Eritrosit (Urine)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['kristal_normal'] }}</td>
                                <td class="text-center">{{ $item['lain_lain'] }}</td>
                                <td class="text-center">{{ $item['leukosit_esterase'] }}</td>
                                <td class="text-center">{{ $item['eritrosit_urine'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
