<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_rules', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ------------------------------------------------------------
            // Identitas aturan
            // ------------------------------------------------------------
            // Kode kelompok risiko (global, obesitas_metabolik, diabetes, dll)
            $table->string('group_code');

            // Jenis rekomendasi
            // diet | exercise | note
            $table->enum('category', ['diet', 'exercise', 'note']);

            // Tingkat keparahan klinis
            // ringan | sedang | tinggi | kritis
            $table->enum('severity_level', ['ringan', 'sedang', 'tinggi', 'kritis']);

            // ------------------------------------------------------------
            // Kondisi skor (menggantikan if >= 50, >= 70, dll)
            // ------------------------------------------------------------
            $table->unsignedInteger('min_score');
            $table->unsignedInteger('max_score');

            // ------------------------------------------------------------
            // Konten rekomendasi
            // ------------------------------------------------------------
            $table->text('recommendation_text');

            // ------------------------------------------------------------
            // Kontrol & audit
            // ------------------------------------------------------------
            $table->boolean('is_active')->default(true);

            // Dokter / admin pembuat aturan
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // ------------------------------------------------------------
            // Foreign key
            // ------------------------------------------------------------
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // ------------------------------------------------------------
            // Index untuk performa query engine
            // ------------------------------------------------------------
            $table->index(['group_code', 'category']);
            $table->index(['min_score', 'max_score']);
            $table->index('severity_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_rules');
    }
};
