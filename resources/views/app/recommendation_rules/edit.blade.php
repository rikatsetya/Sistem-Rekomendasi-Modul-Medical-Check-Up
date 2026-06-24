@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti tabler-circle-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti tabler-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    {{-- Error Handling --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="ti tabler-alert-circle me-2"></i>
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header Card --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <a href="{{ route('recommendation-rules.index') }}" class="btn btn-icon btn-label-secondary me-3">
                    <i class="ti tabler-arrow-left"></i>
                </a>
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="ti tabler-edit me-2"></i>
                            Edit Recommendation
                        </h4>
                        <small class="text-muted">Perbarui aturan rekomendasi yang ada.</small>
                    </div>
                    <a href="{{ route('recommendation-rules.show', encrypt($rule->id)) }}" class="btn btn-outline-primary">
                        <i class="ti tabler-eye me-1"></i> View Rule
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ti tabler-file-description me-2"></i>
                Form Edit Recommendation Rule
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('recommendation-rules.update', encrypt($rule->id)) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- Section: Basic Information --}}
                    <div class="col-12">
                        <div class="border rounded p-4">
                            <h6 class="mb-3">
                                <i class="ti tabler-settings me-2"></i>
                                Informasi Klasifikasi
                            </h6>
                            <div class="row">
                                <x-inputs.group class="col-md-4">
                                    <x-inputs.select name="group_code" label="Group Risiko" required
                                        class="form-select select2">
                                        @foreach ($groups as $key => $label)
                                            <option value="{{ $key }}" @selected($rule->group_code == $key)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </x-inputs.select>
                                </x-inputs.group>

                                <x-inputs.group class="col-md-4">
                                    <x-inputs.select name="severity_level" label="Severity Level" required
                                        class="form-select select2">
                                        @foreach ($severities as $key => $label)
                                            <option value="{{ $key }}" @selected($rule->severity_level == $key)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </x-inputs.select>
                                </x-inputs.group>

                                <x-inputs.group class="col-md-4">
                                    <x-inputs.select name="category" label="Category" required class="form-select select2">
                                        @foreach ($categories as $key => $label)
                                            <option value="{{ $key }}" @selected($rule->category == $key)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </x-inputs.select>
                                </x-inputs.group>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Scoring Rules --}}
                    <div class="col-12">
                        <div class="border rounded p-4">
                            <h6 class="mb-3">
                                <i class="ti tabler-chart-bar me-2"></i>
                                Rentang Skor (Fuzzy Range)
                            </h6>
                            <div class="row">
                                <x-inputs.group class="col-md-6">
                                    <x-inputs.text name="min_score" label="Min Score" type="number" :value="$rule->min_score"
                                        required />
                                </x-inputs.group>
                                <x-inputs.group class="col-md-6">
                                    <x-inputs.text name="max_score" label="Max Score" type="number" :value="$rule->max_score"
                                        required />
                                </x-inputs.group>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Content & Status --}}
                    <div class="col-12">
                        <div class="border rounded p-4">
                            <h6 class="mb-3">
                                <i class="ti tabler-file-text me-2"></i>
                                Konten Rekomendasi
                            </h6>
                            <x-inputs.group>
                                <x-inputs.textarea name="recommendation_text" label="Recommendation Text" rows="3"
                                    required>
                                    {{ $rule->recommendation_text }}
                                </x-inputs.textarea>
                            </x-inputs.group>

                            <div class="mt-4">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="statusSwitch" @checked($rule->is_active)>
                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('recommendation-rules.index') }}" class="btn btn-label-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Rule</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
