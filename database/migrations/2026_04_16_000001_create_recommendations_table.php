<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabel ini menyimpan hasil rekomendasi yang dibangkitkan oleh
     * sistem inferensi Fuzzy Mamdani, beserta status validasi dokter.
     */
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relasi ke karyawan yang bersangkutan
            $table->unsignedBigInteger('user_id');

            // Tahun MCU yang digunakan sebagai dasar penghitungan
            $table->string('tahun');

            // ----------------------------------------------------------------
            // Snapshot nilai MCU yang dimasukkan ke mesin fuzzy
            // (disimpan agar dapat ditelusuri kembali / audit trail)
            // ----------------------------------------------------------------
            $table->float('bmi')->nullable();            // IMT (kg/m2)
            $table->float('sistolik')->nullable();       // Tekanan darah sistolik (mmHg)
            $table->float('diastolik')->nullable();      // Tekanan darah diastolik (mmHg)
            $table->float('glukosa_puasa')->nullable();  // Glukosa Puasa (mg/dL)
            $table->float('kolesterol')->nullable();     // Chol. Total (mg/dL)
            $table->float('asam_urat')->nullable();      // Asam Urat (mg/dL)
            $table->float('trigliserida')->nullable();   // Trigliserida (mg/dL)

            // ----------------------------------------------------------------
            // Output Fuzzy Mamdani
            // ----------------------------------------------------------------
            // Skor risiko hasil defuzzifikasi Centroid (0–100)
            $table->float('risk_score')->nullable();

            // Label linguistik: 'Sehat' | 'Risiko Sedang' | 'Risiko Tinggi'
            $table->string('risk_label')->nullable();

            // ----------------------------------------------------------------
            // Teks rekomendasi (di-generate otomatis, dapat diedit dokter)
            // ----------------------------------------------------------------
            $table->text('rec_diet')->nullable();        // Rekomendasi pola makan
            $table->text('rec_exercise')->nullable();    // Rekomendasi olahraga
            $table->text('rec_notes')->nullable();       // Catatan tambahan

            // ----------------------------------------------------------------
            // Validasi Dokter
            // ----------------------------------------------------------------
            // Status: pending → approved / rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Dokter yang memvalidasi (nullable sampai divalidasi)
            $table->unsignedBigInteger('doctor_id')->nullable();

            $table->timestamp('validated_at')->nullable();

            // Catatan tambahan dari dokter saat validasi
            $table->text('doctor_notes')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
