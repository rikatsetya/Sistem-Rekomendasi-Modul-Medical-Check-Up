@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">
                    <i class="ti tabler-edit me-1"></i>
                    Edit Recommendation Rule
                </h5>
                <a href="{{ route('recommendation-rules.show', $rule->id) }}" class="btn btn-sm btn-secondary">
                    View
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('recommendation-rules.update', $rule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- GROUP --}}
                    <div class="mb-3">
                        <label class="form-label">Group Risiko</label>
                        <select name="group_code" class="form-select select2">
                            @foreach ($groups as $key => $label)
                                <option value="{{ $key }}" @selected($rule->group_code === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SEVERITY --}}
                    <div class="mb-3">
                        <label class="form-label">Severity Level</label>
                        <select name="severity_level" class="form-select select2">
                            @foreach ($severities as $key => $label)
                                <option value="{{ $key }}" @selected($rule->severity_level === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CATEGORY --}}
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select select2">
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}" @selected($rule->category === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SCORE RANGE --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min Score</label>
                            <input type="number" name="min_score" class="form-control" value="{{ $rule->min_score }}"
                                min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Score</label>
                            <input type="number" name="max_score" class="form-control" value="{{ $rule->max_score }}"
                                min="0" max="100">
                        </div>
                    </div>

                    {{-- TEXT --}}
                    <div class="mb-3">
                        <label class="form-label">Recommendation Text</label>
                        <textarea name="recommendation_text" rows="4" class="form-control">{{ $rule->recommendation_text }}</textarea>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select select2">
                            <option value="1" @selected($rule->is_active)>
                                Active
                            </option>
                            <option value="0" @selected(!$rule->is_active)>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('recommendation-rules.index') }}" class="btn btn-secondary me-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
