<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importModalLabel">Import Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" action="{{ route('value.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="download" class="form-label">Download Template Excel</label>
                        <br>
                        <a href="{{ asset('vuexy/assets/Template Excel MCU.xlsx') }}" class="btn btn-success" download
                            name="download" id="download">
                            <i class="icon-base ti tabler-download icon-me me-5"></i> Download Template
                        </a>
                    </div>
                    {{-- Tahun --}}
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select select2" required>
                            @php
                                $currentYear = now()->year;
                                $years = range($currentYear - 20, $currentYear + 1);
                                rsort($years);
                            @endphp
                            @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- File --}}
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload File (xlsx, xls, csv)</label>
                        <input type="file" name="file" id="file" class="form-control"
                            accept=".xlsx,.xls,.csv" required>
                        <span class="form-text text-sm">Maximum file size : 5MB</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
