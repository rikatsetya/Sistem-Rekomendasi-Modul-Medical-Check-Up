@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti tabler-eye me-1"></i>
                    Detail Recommendation Rule
                </h5>
                <a href="{{ route('recommendation-rules.index') }}" class="btn btn-sm btn-secondary">
                    Back
                </a>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Group Risiko</div>
                    <div class="col-md-8 fw-semibold">
                        {{ ucwords(str_replace('_', ' ', $rule->group_code)) }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Severity Level</div>
                    <div class="col-md-8">
                        <span class="badge bg-label-danger">
                            {{ ucfirst($rule->severity_level) }}
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Category</div>
                    <div class="col-md-8">
                        {{ ucfirst($rule->category) }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Score Range</div>
                    <div class="col-md-8">
                        {{ $rule->min_score }} – {{ $rule->max_score }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Recommendation</div>
                    <div class="col-md-8">
                        <div class="border rounded p-3 bg-light">
                            {{ $rule->recommendation_text }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Status</div>
                    <div class="col-md-8">
                        @if ($rule->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Created By</div>
                    <div class="col-md-8">
                        {{ optional($rule->creator)->name ?? '-' }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 text-muted">Created At</div>
                    <div class="col-md-8">
                        {{ $rule->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('recommendation-rules.edit', $rule->id) }}" class="btn btn-warning me-2">
                    Edit
                </a>
                <a href="{{ route('recommendation-rules.index') }}" class="btn btn-secondary">
                    Close
                </a>
            </div>
        </div>
    </div>
@endsection
