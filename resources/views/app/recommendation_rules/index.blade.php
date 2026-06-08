@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4>Recommendation Rules</h4>
                </div>
                <div>

                    <a href="{{ route('rekomendasi.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-2"></i>
                        back
                    </a>
                    <a href="{{ route('recommendation-rules.create') }}" class="btn btn-primary">
                        <i class="ti tabler-plus me-2"></i>
                        Add Rule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="ti tabler-users me-2"></i>
                Aturan Rekomendasi
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ruleTable" class="table table-bordered table-striped align-middle ">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Group</th>
                            <th>Severity</th>
                            <th>Category</th>
                            <th>Score Range</th>
                            <th>Recommendation</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rules as $rule)
                            <tr>
                                <td>{{ $loop->iteration + ($rules->currentPage() - 1) * $rules->perPage() }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $rule->group_code)) }}</td>
                                <td class="text-center">
                                    @if ($rule)
                                        @php
                                            $riskClass = match ($rule->severity_level) {
                                                'ringan' => 'bg-label-success',
                                                'sedang' => 'bg-label-warning',
                                                default => 'bg-label-danger',
                                            };
                                        @endphp
                                        <span class="badge {{ $riskClass }}">{{ ucfirst($rule->severity_level) }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">Belum generate</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($rule->category) }}</td>
                                <td>{{ $rule->min_score }} – {{ $rule->max_score }}</td>
                                <td>{{ $rule->recommendation_text }}</td>
                                <td class="text-center" style="width: 160px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group demo-inline-spacing">

                                        {{-- Edit --}}
                                        @can('update', $rule)
                                            <a href="{{ route('recommendation-rules.edit', $rule->id) }}">
                                                <span class="badge text-bg-primary">
                                                    <i class="icon-base ti tabler-edit icon-me sm-2"></i>
                                                    Edit
                                                </span>
                                            </a>
                                        @endcan

                                        {{-- Show --}}
                                        @can('view', $rule)
                                            <a href="{{ route('recommendation-rules.show', $rule->id) }}">
                                                <span class="badge text-bg-warning">
                                                    <i class="icon-base ti tabler-eye icon-me sm-2"></i>
                                                    Show
                                                </span>
                                            </a>
                                        @endcan

                                        {{-- Delete --}}
                                        @can('delete', $rule)
                                            <form action="{{ route('recommendation-rules.destroy', $rule->id) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Yakin ingin menghapus rule ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <span class="icon-base ti tabler-trash icon-xs sm-2"></span>
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No recommendation rules found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $rules->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable untuk tabel karyawan
            if (!$.fn.dataTable.isDataTable('#ruleTable')) {
                $('#ruleTable').DataTable({
                    responsive: true
                });
            }
        });
    </script>
@endpush
