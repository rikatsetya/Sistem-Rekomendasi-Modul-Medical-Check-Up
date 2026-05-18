<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Recommendation
 *
 * Menyimpan hasil rekomendasi Fuzzy Mamdani beserta status validasi dokter.
 *
 * Kolom utama:
 *  - user_id       : karyawan pemilik data MCU
 *  - tahun         : tahun MCU yang digunakan
 *  - bmi … trigliserida : snapshot input fuzzy
 *  - risk_score    : skor defuzzifikasi Centroid (0-100)
 *  - risk_label    : label linguistik ('Sehat' / 'Risiko Sedang' / 'Risiko Tinggi')
 *  - rec_diet      : teks rekomendasi diet (auto-generate, editable dokter)
 *  - rec_exercise  : teks rekomendasi olahraga
 *  - rec_notes     : catatan tambahan
 *  - status        : 'pending' | 'approved' | 'rejected'
 *  - doctor_id     : dokter yang memvalidasi
 *  - validated_at  : waktu validasi
 *  - doctor_notes  : catatan dokter saat validasi
 */
class Recommendation extends Model
{
    use HasFactory;

    protected $table = 'recommendations';

    protected $fillable = [
        'user_id',
        'tahun',
        // Inputs
        'bmi',
        'sistolik',
        'diastolik',
        'glukosa_puasa',
        'kolesterol',
        'asam_urat',
        'trigliserida',
        // Fuzzy output
        'risk_score',
        'risk_label',
        // Recommendation text
        'rec_diet',
        'rec_exercise',
        'rec_notes',
        // Validation
        'status',
        'doctor_id',
        'validated_at',
        'doctor_notes',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'bmi'          => 'float',
        'sistolik'     => 'float',
        'diastolik'    => 'float',
        'glukosa_puasa'=> 'float',
        'kolesterol'   => 'float',
        'asam_urat'    => 'float',
        'trigliserida' => 'float',
        'risk_score'   => 'float',
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

    /** Apakah rekomendasi sudah divalidasi dokter (approved atau rejected)? */
    public function isValidated(): bool
    {
        return in_array($this->status, ['approved', 'rejected']);
    }

    /** Badge CSS class berdasarkan status */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'approved' => 'bg-label-success',
            'rejected' => 'bg-label-danger',
            default    => 'bg-label-warning',
        };
    }

    /** Badge CSS class berdasarkan risk_label */
    public function riskBadgeClass(): string
    {
        return match ($this->risk_label) {
            'Sehat'        => 'bg-label-success',
            'Risiko Sedang'=> 'bg-label-warning',
            'Risiko Tinggi'=> 'bg-label-danger',
            default        => 'bg-label-secondary',
        };
    }
}
