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
                    <h4 class="text-title">Komponen Darah Lengkap</h4>
                </div>
            </div>
        </div>

        {{-- Tabel 1 (4 kolom: Tahun + 3 data) --}}
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card-datatable">
                <table id="komponen-darah-1" class="dt-multilingual table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Hemoglobin</th>
                            <th class="text-center">Eritrosit</th>
                            <th class="text-center">Hematokrit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['hemoglobin'] }}</td>
                                <td class="text-center">{{ $item['eritrosit'] }}</td>
                                <td class="text-center">{{ $item['hematokrit'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">@lang('crud.common.no_items_found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel 2 (5 kolom: Tahun + 4 data) --}}
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card-datatable">
                <table id="komponen-darah-2" class="dt-multilingual table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">MCV</th>
                            <th class="text-center">MCH</th>
                            <th class="text-center">MCHC</th>
                            <th class="text-center">Trombosit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['mcv'] }}</td>
                                <td class="text-center">{{ $item['mch'] }}</td>
                                <td class="text-center">{{ $item['mchc'] }}</td>
                                <td class="text-center">{{ $item['trombosit'] }}</td>
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

        {{-- Tabel 3 (5 kolom: Tahun + 4 data) --}}
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card-datatable">
                <table id="komponen-darah-3" class="dt-multilingual table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Leukosit</th>
                            <th class="text-center">Basofil</th>
                            <th class="text-center">Eosinofil</th>
                            <th class="text-center">Neutrofil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['leukosit'] }}</td>
                                <td class="text-center">{{ $item['basofil'] }}</td>
                                <td class="text-center">{{ $item['eosinofil'] }}</td>
                                <td class="text-center">{{ $item['neutrofil'] }}</td>
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

        {{-- Tabel 4 (4 kolom: Tahun + 3 data) --}}
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card-datatable">
                <table id="komponen-darah-4" class="dt-multilingual table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Limfosit</th>
                            <th class="text-center">Monosit</th>
                            <th class="text-center">LED</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['limfosit'] }}</td>
                                <td class="text-center">{{ $item['monosit'] }}</td>
                                <td class="text-center">{{ $item['led'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">@lang('crud.common.no_items_found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
