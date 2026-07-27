<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================================
 * Recreate Recommendations Table — Fuzzy Hierarki 23 Parameter
 * ============================================================================
 *
 * Perubahan dari versi sebelumnya:
 *  - Drop kolom snapshot lama (bmi, sistolik, etc.)
 *  - Tambah 23 kolom snapshot input sesuai parameter medis MCU
 *  - Tambah kolom group_scores (JSON) untuk menyimpan 7 skor kelompok fuzzy
 *  - Tambah kolom duration untuk durasi program rekomendasi
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop tabel lama agar dapat dibuat ulang dengan skema baru
        Schema::dropIfExists('recommendations');

        Schema::create('recommendations', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relasi ke karyawan yang bersangkutan
            $table->unsignedBigInteger('user_id');

            // Tahun MCU yang digunakan sebagai dasar penghitungan
            $table->string('tahun');

            // ----------------------------------------------------------------
            // Snapshot nilai MCU — Kelompok 1: Fungsi Hati
            // ----------------------------------------------------------------
            $table->float('got')->nullable();           // GOT / AST (U/L)
            $table->float('gpt')->nullable();           // GPT / ALT (U/L)

            // ----------------------------------------------------------------
            // Snapshot nilai MCU — Kelompok 2: Diabetes / Gula Darah
            // ----------------------------------------------------------------
            $table->float('glukosa_puasa')->nullable();  // Glukosa Puasa (mg/dL)
            $table->float('glukosa_2j_pp')->nullable();  // Glukosa 2 Jam PP (mg/dL)
            $table->float('hba1c')->nullable();          // HbA1c NGSP (%)

            // ----------------------------------------------------------------
            // Snapshot nilai MCU — Kelompok 3: Profil Lipid
            // ----------------------------------------------------------------
            $table->float('chol_total')->nullable();     // Kolesterol Total (mg/dL)
            $table->float('chol_ldl')->nullable();       // LDL Direk (mg/dL)
            $table->float('chol_hdl')->nullable();       // HDL (mg/dL)
            $table->float('trigliserida')->nullable();   // Trigliserida (mg/dL)
            $table->float('apo_b')->nullable();          // Apo-B (mg/dL)

            // ----------------------------------------------------------------
            // Snapshot nilai MCU — Kelompok 4: Fungsi Ginjal
            // ----------------------------------------------------------------
            $table->float('urea_n')->nullable();         // Urea N / BUN (mg/dL)
            $table->float('ureum')->nullable();          // Ureum (mg/dL)
            $table->float('kreatinin')->nullable();      // Kreatinin (mg/dL)
            $table->float('elfg')->nullable();           // eLFG CKD-EPI (mL/min/1.73m²)

            // ----------------------------------------------------------------
            // Snapshot nilai MCU — Kelompok 5: Asam Urat
            // ----------------------------------------------------------------
            $table->float('asam_urat')->nullable();      // Asam Urat (mg/dL)

            // ----------------------------------------------------------------
            // Snapshot nilai MCU — Kelompok 6: Kardiovaskular & Tanda Vital
            // ----------------------------------------------------------------
            $table->float('nadi')->nullable();           // Nadi (kali/menit)
            $table->float('pernafasan')->nullable();     // Pernafasan (kali/menit)
            $table->float('sistolik')->nullable();       // Tekanan Darah Sistolik (mmHg)
            $table->float('diastolik')->nullable();      // Tekanan Darah Diastolik (mmHg)

            // ----------------------------------------------------------------
            // Snapshot nilai MCU — Kelompok 7: Antropometri & Obesitas
            // ----------------------------------------------------------------
            $table->float('tinggi_badan')->nullable();   // Tinggi Badan (cm)
            $table->float('berat_badan')->nullable();    // Berat Badan (kg)
            $table->float('imt')->nullable();            // IMT / BMI (kg/m²)
            $table->float('lingkar_perut')->nullable();  // Lingkar Perut (cm)

            // ----------------------------------------------------------------
            // Output Fuzzy Hierarki
            // ----------------------------------------------------------------
            // 7 skor kelompok (masing-masing 0–100), disimpan sebagai JSON
            // Contoh: {"fungsi_hati": 35.2, "diabetes": 12.1, ...}
            $table->json('group_scores')->nullable();

            // Skor risiko rata-rata hasil defuzzifikasi Centroid (0–100)
            $table->float('risk_score')->nullable();

            // Label linguistik: 'Ringan' | 'Sedang' | 'Tinggi' | 'Kritis'
            $table->string('risk_label')->nullable();

            // Durasi program rekomendasi
            $table->string('duration')->nullable();

            // ----------------------------------------------------------------
            // Teks rekomendasi (JSON array, editable dokter)
            // ----------------------------------------------------------------
            $table->text('rec_diet')->nullable();        // Rekomendasi pola makan
            $table->text('rec_exercise')->nullable();    // Rekomendasi olahraga
            $table->text('rec_notes')->nullable();       // Catatan tambahan

            // ----------------------------------------------------------------
            // Validasi Dokter
            // ----------------------------------------------------------------
            // Status alur: raw → draft → published
            $table->enum('status', ['raw', 'draft', 'published'])->default('raw');

            // Dokter yang memvalidasi
            $table->unsignedBigInteger('doctor_id')->nullable();

            $table->timestamp('validated_at')->nullable();

            // Catatan tambahan dari dokter saat validasi
            $table->text('doctor_notes')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');

            // Index untuk performa
            $table->index(['user_id', 'tahun']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        // Kembalikan ke skema lama (7 parameter)
        Schema::dropIfExists('recommendations');

        Schema::create('recommendations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('tahun');
            $table->float('bmi')->nullable();
            $table->float('sistolik')->nullable();
            $table->float('diastolik')->nullable();
            $table->float('glukosa_puasa')->nullable();
            $table->float('kolesterol')->nullable();
            $table->float('asam_urat')->nullable();
            $table->float('trigliserida')->nullable();
            $table->float('risk_score')->nullable();
            $table->string('risk_label')->nullable();
            $table->text('rec_diet')->nullable();
            $table->text('rec_exercise')->nullable();
            $table->text('rec_notes')->nullable();
            $table->enum('status', ['raw', 'draft', 'published'])->default('raw');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
