@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti tabler-plus me-1"></i>
                    Tambah Recommendation Rule
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('recommendation-rules.store') }}" method="POST">
                    @csrf

                    {{-- GROUP --}}
                    <div class="mb-3">
                        <label class="form-label">Group Risiko</label>
                        <select name="group_code" class="form-select select2" required>
                            @foreach ($groups as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SEVERITY --}}
                    <div class="mb-3">
                        <label class="form-label">Severity Level</label>
                        <select name="severity_level" class="form-select select2" required>
                            @foreach ($severities as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CATEGORY --}}
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select select2" required>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SCORE RANGE --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min Score</label>
                            <input type="number" name="min_score" class="form-control" min="0" max="100"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Score</label>
                            <input type="number" name="max_score" class="form-control" min="0" max="100"
                                required>
                        </div>
                    </div>

                    {{-- TEXT --}}
                    <div class="mb-3">
                        <label class="form-label">Recommendation Text</label>
                        <textarea name="recommendation_text" rows="4" class="form-control" required></textarea>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select select2" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('recommendation-rules.index') }}" class="btn btn-secondary me-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Save Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
