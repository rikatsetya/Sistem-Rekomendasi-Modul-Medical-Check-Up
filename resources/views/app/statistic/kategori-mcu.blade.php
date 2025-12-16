<div class="modal fade" id="modalKategoriMCU" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Kategori MCU</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Kode MCU</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $mcuMap = [
                                'M1A' => ['rank' => 1, 'desc' => 'Fit', 'color' => 'success'],
                                'M1B' => ['rank' => 2, 'desc' => 'Fit dengan problem tidak serius', 'color' => 'success'],
                                'M2'  => ['rank' => 3, 'desc' => 'Fit dengan resiko Rendah', 'color' => 'success'],
                                'M3A' => ['rank' => 4, 'desc' => 'Fit dengan resiko Sedang', 'color' => 'warning'],
                                'M3B' => ['rank' => 5, 'desc' => 'Fit dengan Resiko Tinggi', 'color' => 'warning'],
                                'M4'  => ['rank' => 6, 'desc' => 'Temporary Unfit', 'color' => 'danger'],
                                'M5'  => ['rank' => 7, 'desc' => 'Unfit', 'color' => 'danger'],
                            ];

                            $mcuValues = $mcu->values->sortByDesc('tahun')->values();
                        @endphp

                        @foreach ($mcuValues as $val)
                            @php
                                $kode = $val->nilai;
                                $tahun = $val->tahun;
                                $current = $kode ? $mcuMap[$kode] : null;
                            @endphp
                            <tr>
                                <td>{{ $tahun }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $current['color'] ?? 'secondary' }}">
                                        {{ $kode ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $current['desc'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
