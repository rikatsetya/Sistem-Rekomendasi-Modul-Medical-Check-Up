<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Recommendation
 *
 * Menyimpan hasil rekomendasi Fuzzy Mamdani Hierarkis beserta status validasi dokter.
 *
 * Kolom snapshot input (23 parameter MCU):
 *  - Fungsi Hati    : got, gpt
 *  - Diabetes       : glukosa_puasa, glukosa_2j_pp, hba1c
 *  - Profil Lipid   : chol_total, chol_ldl, chol_hdl, trigliserida, apo_b
 *  - Fungsi Ginjal  : urea_n, ureum, kreatinin, elfg
 *  - Asam Urat      : asam_urat
 *  - Kardiovaskular : nadi, pernafasan, sistolik, diastolik
 *  - Antropometri   : tinggi_badan, berat_badan, imt, lingkar_perut
 *
 * Kolom output fuzzy:
 *  - group_scores   : JSON — 7 skor kelompok (0–100)
 *  - risk_score     : Rata-rata skor global (0–100)
 *  - risk_label     : 'Ringan' | 'Sedang' | 'Tinggi' | 'Kritis'
 *  - duration       : Durasi program rekomendasi
 */
class Recommendation extends Model
{
    use HasFactory;

    protected $table = 'recommendations';

    protected $fillable = [
        'user_id',
        'tahun',

        // ── Kelompok 1: Fungsi Hati ──────────────────────────────────────
        'got',
        'gpt',

        // ── Kelompok 2: Diabetes ─────────────────────────────────────────
        'glukosa_puasa',
        'glukosa_2j_pp',
        'hba1c',

        // ── Kelompok 3: Profil Lipid ─────────────────────────────────────
        'chol_total',
        'chol_ldl',
        'chol_hdl',
        'trigliserida',
        'apo_b',

        // ── Kelompok 4: Fungsi Ginjal ────────────────────────────────────
        'urea_n',
        'ureum',
        'kreatinin',
        'elfg',

        // ── Kelompok 5: Asam Urat ────────────────────────────────────────
        'asam_urat',

        // ── Kelompok 6: Kardiovaskular & Tanda Vital ─────────────────────
        'nadi',
        'pernafasan',
        'sistolik',
        'diastolik',

        // ── Kelompok 7: Antropometri & Obesitas ──────────────────────────
        'tinggi_badan',
        'berat_badan',
        'imt',
        'lingkar_perut',

        // ── Output Fuzzy Hierarki ─────────────────────────────────────────
        'group_scores',
        'risk_score',
        'risk_label',
        'duration',

        // ── Teks Rekomendasi ─────────────────────────────────────────────
        'rec_diet',
        'rec_exercise',
        'rec_notes',

        // ── Validasi Dokter ───────────────────────────────────────────────
        'status',
        'doctor_id',
        'validated_at',
        'doctor_notes',
    ];

    protected $casts = [
        'validated_at'  => 'datetime',

        // Snapshot input
        'got'           => 'float',
        'gpt'           => 'float',
        'glukosa_puasa' => 'float',
        'glukosa_2j_pp' => 'float',
        'hba1c'         => 'float',
        'chol_total'    => 'float',
        'chol_ldl'      => 'float',
        'chol_hdl'      => 'float',
        'trigliserida'  => 'float',
        'apo_b'         => 'float',
        'urea_n'        => 'float',
        'ureum'         => 'float',
        'kreatinin'     => 'float',
        'elfg'          => 'float',
        'asam_urat'     => 'float',
        'nadi'          => 'float',
        'pernafasan'    => 'float',
        'sistolik'      => 'float',
        'diastolik'     => 'float',
        'tinggi_badan'  => 'float',
        'berat_badan'   => 'float',
        'imt'           => 'float',
        'lingkar_perut' => 'float',

        // Output
        'group_scores'  => 'array',
        'risk_score'    => 'float',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /** Karyawan pemilik rekomendasi */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Dokter yang memvalidasi */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Apakah rekomendasi sudah final (dipublish)?
     */
    public function isValidated(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Badge CSS class berdasarkan status rekomendasi.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'raw'       => 'bg-label-secondary',
            'draft'     => 'bg-label-warning',
            'published' => 'bg-label-success',
            default     => 'bg-label-secondary',
        };
    }

    /**
     * Badge CSS class berdasarkan risk_label.
     * Menyesuaikan label baru: Ringan | Sedang | Tinggi | Kritis
     */
    public function riskBadgeClass(): string
    {
        return match ($this->risk_label) {
            'Ringan'  => 'bg-label-success',
            'Sedang'  => 'bg-label-warning',
            'Tinggi'  => 'bg-label-danger',
            'Kritis'  => 'bg-label-dark',
            default   => 'bg-label-secondary',
        };
    }

    /**
     * Icon warna berdasarkan risk_label untuk tampilan UI.
     */
    public function riskIconColor(): string
    {
        return match ($this->risk_label) {
            'Ringan'  => 'text-success',
            'Sedang'  => 'text-warning',
            'Tinggi'  => 'text-danger',
            'Kritis'  => 'text-dark',
            default   => 'text-secondary',
        };
    }
}
