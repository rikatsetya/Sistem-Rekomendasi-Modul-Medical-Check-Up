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
                    <h4 class="text-title">Kimia Darah</h4>
                </div>
            </div>
        </div>

        {{-- Tabel 1 (4 kolom: Tahun + 3 data) --}}
        <div class="col-xl-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-title">Faal Ginjal</h5>

                    <div class="card-datatable">
                        <table id="kimia-darah-1" class="dt-multilingual table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-center">Ureum</th>
                                    <th class="text-center">Kreatinin</th>
                                    <th class="text-center">Asam Urat</th>
                                    <th class="text-center">eLFG (CKD-EPI)</th>
                                    <th class="text-center">Urea N</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tandaVital as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['tahun'] }}</td>
                                        <td class="text-center">{{ $item['Ureum'] }}</td>
                                        <td class="text-center">{{ $item['Kreatinin'] }}</td>
                                        <td class="text-center">{{ $item['Asam Urat'] }}</td>
                                        <td class="text-center">{{ $item['eLFG (CKD-EPI)'] }}</td>
                                        <td class="text-center">{{ $item['Urea N'] }}</td>
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
        </div>

        {{-- Tabel 2 (5 kolom: Tahun + 4 data) --}}
        <div class="col-xl-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-title">Profil Lemak</h5>
                    <div class="card-datatable">
                        <table id="kimia-darah-2" class="dt-multilingual table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-center">Chol. Total</th>
                                    <th class="text-center">Chol. LDL Direk</th>
                                    <th class="text-center">Chol. HDL</th>
                                    <th class="text-center">Trigliserida</th>
                                    <th class="text-center">Ratio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tandaVital as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['tahun'] }}</td>
                                        <td class="text-center">{{ $item['Chol. Total'] }}</td>
                                        <td class="text-center">{{ $item['Chol. LDL Direk'] }}</td>
                                        <td class="text-center">{{ $item['Chol. HDL'] }}</td>
                                        <td class="text-center">{{ $item['Trigliserida'] }}</td>
                                        <td class="text-center">{{ $item['Ratio'] }}</td>
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
        </div>

        {{-- Tabel 3 (5 kolom: Tahun + 4 data) --}}
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-title">Faal Hati</h5>
                    <div class="card-datatable">
                        <table id="kimia-darah-3" class="dt-multilingual table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-center">GOT</th>
                                    <th class="text-center">GPT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tandaVital as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['tahun'] }}</td>
                                        <td class="text-center">{{ $item['GOT'] }}</td>
                                        <td class="text-center">{{ $item['GPT'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">@lang('crud.common.no_items_found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel 4 (4 kolom: Tahun + 3 data) --}}
        <div class="col-xl-6 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-title">Profil Gula</h5>
            <div class="card-datatable">
                <table id="kimia-darah-4" class="dt-multilingual table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Glukosa Puasa</th>
                            <th class="text-center">Glukosa 2 Jam PP</th>
                            <th class="text-center">HbA1c (NGSP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandaVital as $item)
                            <tr>
                                <td class="text-center">{{ $item['tahun'] }}</td>
                                <td class="text-center">{{ $item['Glukosa Puasa'] }}</td>
                                <td class="text-center">{{ $item['Glukosa 2 Jam PP'] }}</td>
                                <td class="text-center">{{ $item['HbA1c (NGSP)'] }}</td>
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
        </div>

    </div>
@endsection
