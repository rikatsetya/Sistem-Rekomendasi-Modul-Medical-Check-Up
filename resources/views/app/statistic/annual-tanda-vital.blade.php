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
                    <h4 class="text-title">Annual Tanda Vital</h4>
                    {{-- <span class="text-body"></span> --}}
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card-datatable">
                <table id="annual-tanda-vital-1" class="dt-multilingual table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Sistolik</th>
                            <th class="text-center">Diastolik</th>
                            <th class="text-center">BMI (kg/m2)</th>
                            <th class="text-center">Nadi (kali/ menit)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['sistolik'] }}</td>
                                <td class="text-center">{{ $item['diastolik'] }}</td>
                                <td class="text-center">{{ $item['bmi'] }}</td>
                                <td class="text-center">{{ $item['nadi'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card-datatable">
                <table id="annual-tanda-vital-2" class="dt-multilingual table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Pernafasan (kali/ menit)</th>
                            <th class="text-center">Tinggi Badan (cm)</th>
                            <th class="text-center">Berat Badan (kg)</th>
                            <th class="text-center">Lingkar Perut (cm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['pernafasan'] }}</td>
                                <td class="text-center">{{ $item['tinggi'] }}</td>
                                <td class="text-center">{{ $item['berat'] }}</td>
                                <td class="text-center">{{ $item['lingkar_perut'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
