@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="ti tabler-file-info me-2"></i>
                        Recommendation Details
                    </h4>
                    <small class="text-muted">
                        Showing Recommendation Rule Details.
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('recommendation-rules.index') }}" class="btn btn-label-secondary">
                        <i class="ti tabler-arrow-left me-1"></i> Back
                    </a>
                    <a href="{{ route('recommendation-rules.edit', encrypt($rule->id)) }}" class="btn btn-primary">
                        <i class="ti tabler-edit me-1"></i> Edit Rule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ti tabler-file-description me-2"></i>Informasi Aturan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Group Risiko</label>
                            <p class="fw-medium text-heading">{{ ucwords(str_replace('_', ' ', $rule->group_code)) }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Kategori</label>
                            <p class="fw-medium text-heading">{{ ucfirst($rule->category) }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Recommendation Text</label>
                            <div class="alert alert-secondary mt-1">
                                {{ $rule->recommendation_text }}
                            </div>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted small">Metadata</h6>
                            <small class="text-muted d-block">Created at:</small>
                            <span class="text-heading">{{ $rule->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="col-xl-4 col-lg-5">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="text-muted small">Status & Severity</h6>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Aktifasi</span>
                        <span class="badge {{ $rule->is_active ? 'bg-label-success' : 'bg-label-danger' }}">
                            {{ $rule->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Severity Level</span>
                        <span class="badge bg-label-info">{{ ucfirst($rule->severity_level) }}</span>
                    </div>
                </div>
            </div>

            <!-- Score Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">Rentang Skor</h6>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <span class="text-muted d-block">MIN</span>
                        <div class="bg-label-primary p-2 rounded">
                            <span class="h4 mb-0 text-primary">{{ $rule->min_score }}</span>
                        </div>
                        <i class="ti tabler-arrow-right text-muted"></i>
                        <div class="bg-label-primary p-2 rounded">
                            <span class="h4 mb-0 text-primary">{{ $rule->max_score }}</span>
                        </div>
                        <span class="text-muted d-block">MAX</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
