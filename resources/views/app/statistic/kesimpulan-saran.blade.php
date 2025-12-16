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
                                <td colspan="3" class="text-center">
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
