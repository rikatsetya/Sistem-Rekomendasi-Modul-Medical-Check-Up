<?php

namespace App\Services;

use App\Models\RecommendationRule;

/**
 * ============================================================================
 * FuzzyMamdaniService — Fuzzy Mamdani Hierarkis (23 Parameter, 7 Kelompok)
 * ============================================================================
 *
 * Implementasi sistem inferensi Fuzzy Mamdani Hierarkis (Modular Fuzzy) untuk
 * menilai risiko kesehatan karyawan berdasarkan data Medical Check-Up (MCU).
 *
 * Arsitektur Fuzzy Hierarki:
 *  Alih-alih menggabungkan 23 input dalam satu rule base, sistem ini menghitung
 *  skor fuzzy secara TERPISAH untuk tiap kelompok, menghasilkan 7 skor (0–100).
 *
 * Pipeline per kelompok:
 *  1. fuzzify*()       — Konversi nilai crisp → derajat keanggotaan (MF)
 *  2. applyRule*()     — Evaluasi aturan IF-THEN (AND=MIN, OR=MAX)
 *  3. defuzzifyGroup() — Defuzzifikasi Centroid (CoA) → skor 0–100
 *
 * Pipeline utama (process()):
 *  1. Jalankan 7 pipeline kelompok → 7 skor
 *  2. Hitung rata-rata skor global
 *  3. generateRecommendation() → label risiko + teks dari DB
 *
 * ── 7 Kelompok Parameter ────────────────────────────────────────────────────
 *  Kel.1  fungsi_hati    : GOT, GPT
 *  Kel.2  diabetes       : Glukosa Puasa, Glukosa 2j PP, HbA1c
 *  Kel.3  profil_lipid   : Chol.Total, LDL, HDL, Trigliserida, Apo-B
 *  Kel.4  fungsi_ginjal  : Urea N, Ureum, Kreatinin, eLFG
 *  Kel.5  asam_urat      : Asam Urat (gender-aware)
 *  Kel.6  kardiovaskular : Nadi, Pernafasan, Sistolik, Diastolik
 *  Kel.7  antropometri   : Tinggi Badan, Berat Badan, IMT, Lingkar Perut
 *
 * Metode:
 *  AND (anteseden) : MIN
 *  Agregasi output : MAX
 *  Defuzzifikasi   : Centroid (CoA) — sampling 200 titik pada [0, 100]
 * ============================================================================
 */
class FuzzyMamdaniService
{
    // =========================================================================
    // STEP 1 — FUZZIFICATION PER KELOMPOK
    // =========================================================================

    // ─────────────────────────────────────────────────────────────────────────
    // Kelompok 1: Fungsi Hati
    // Referensi: AST/GOT dan ALT/GPT normal < 40 U/L (dewasa)
    //   Normal      : < 35 U/L
    //   Sedang      : 35–80 U/L
    //   Tinggi      : > 80 U/L
    // ─────────────────────────────────────────────────────────────────────────

    /** Fuzzifikasi GOT (AST) dalam satuan U/L */
    private function fuzzifyGOT(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 30.0, 45.0),
            'sedang'  => $this->triangle($x, 35.0, 60.0, 90.0),
            'tinggi'  => $this->trapezoidRight($x, 75.0, 100.0),
        ];
    }

    /** Fuzzifikasi GPT (ALT) dalam satuan U/L */
    private function fuzzifyGPT(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 30.0, 45.0),
            'sedang'  => $this->triangle($x, 35.0, 60.0, 90.0),
            'tinggi'  => $this->trapezoidRight($x, 75.0, 100.0),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kelompok 2: Diabetes / Gula Darah
    // Referensi ADA:
    //   Glukosa Puasa — Normal: < 100 | Pra-DM: 100–125 | DM: ≥ 126 mg/dL
    //   Glukosa 2j PP — Normal: < 140 | Pra-DM: 140–199 | DM: ≥ 200 mg/dL
    //   HbA1c (NGSP)  — Normal: < 5.7 | Pra-DM: 5.7–6.4 | DM: ≥ 6.5 %
    // ─────────────────────────────────────────────────────────────────────────

    /** Fuzzifikasi Glukosa Puasa (mg/dL) */
    private function fuzzifyGlukosaPuasa(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 90.0, 110.0),
            'pra_dm'  => $this->triangle($x, 90.0, 112.0, 135.0),
            'dm'      => $this->trapezoidRight($x, 120.0, 140.0),
        ];
    }

    /** Fuzzifikasi Glukosa 2 Jam PP (mg/dL) */
    private function fuzzifyGlukosa2jPP(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 120.0, 150.0),
            'pra_dm'  => $this->triangle($x, 130.0, 170.0, 210.0),
            'dm'      => $this->trapezoidRight($x, 190.0, 220.0),
        ];
    }

    /** Fuzzifikasi HbA1c NGSP (%) */
    private function fuzzifyHbA1c(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 5.0, 5.8),
            'pra_dm'  => $this->triangle($x, 5.5, 6.0, 6.6),
            'dm'      => $this->trapezoidRight($x, 6.3, 6.8),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kelompok 3: Profil Lipid
    // Referensi NCEP ATP III / AHA:
    //   Chol.Total — Optimal: < 200 | Batas: 200–239 | Tinggi: ≥ 240 mg/dL
    //   LDL Direk  — Optimal: < 100 | Batas: 100–129 | Tinggi: 130–159 | Sangat Tinggi: ≥ 160 mg/dL
    //   HDL        — Rendah: < 40 | Kurang: 40–59 | Optimal: ≥ 60 mg/dL (inverse!)
    //   Trigliserid— Normal: < 150 | Batas: 150–199 | Tinggi: 200–499 | Sangat Tinggi: ≥ 500 mg/dL
    //   Apo-B      — Normal: < 100 | Batas: 100–130 | Tinggi: > 130 mg/dL
    // ─────────────────────────────────────────────────────────────────────────

    /** Fuzzifikasi Kolesterol Total (mg/dL) */
    private function fuzzifyCholTotal(float $x): array
    {
        return [
            'optimal' => $this->trapezoidLeft($x, 170.0, 210.0),
            'batas_t' => $this->triangle($x, 185.0, 220.0, 255.0),
            'tinggi'  => $this->trapezoidRight($x, 230.0, 260.0),
        ];
    }

    /** Fuzzifikasi LDL Direk (mg/dL) */
    private function fuzzifyCholLDL(float $x): array
    {
        return [
            'optimal'   => $this->trapezoidLeft($x, 80.0, 110.0),
            'batas_t'   => $this->triangle($x, 95.0, 115.0, 140.0),
            'tinggi'    => $this->triangle($x, 125.0, 145.0, 170.0),
            'sangat_t'  => $this->trapezoidRight($x, 155.0, 175.0),
        ];
    }

    /**
     * Fuzzifikasi HDL (mg/dL) — nilai RENDAH adalah RISIKO TINGGI (inverse).
     * Derajat keanggotaan "rendah" tinggi berarti HDL pasien kurang optimal.
     */
    private function fuzzifyCholHDL(float $x): array
    {
        return [
            // HDL rendah (< 40): bahaya lipid
            'rendah'   => $this->trapezoidLeft($x, 35.0, 50.0),
            // HDL kurang (40–60)
            'kurang'   => $this->triangle($x, 38.0, 52.0, 66.0),
            // HDL optimal (≥ 60): pelindung kardiovaskular
            'optimal'  => $this->trapezoidRight($x, 55.0, 65.0),
        ];
    }

    /** Fuzzifikasi Trigliserida (mg/dL) */
    private function fuzzifyTrigliserida(float $x): array
    {
        return [
            'normal'   => $this->trapezoidLeft($x, 120.0, 165.0),
            'batas_t'  => $this->triangle($x, 135.0, 175.0, 215.0),
            'tinggi'   => $this->triangle($x, 185.0, 320.0, 510.0),
            'sangat_t' => $this->trapezoidRight($x, 460.0, 520.0),
        ];
    }

    /** Fuzzifikasi Apo-B (mg/dL) */
    private function fuzzifyApoB(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 80.0, 110.0),
            'batas_t' => $this->triangle($x, 95.0, 115.0, 140.0),
            'tinggi'  => $this->trapezoidRight($x, 125.0, 145.0),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kelompok 4: Fungsi Ginjal
    // Referensi:
    //   Urea N (BUN) — Normal: 7–20 mg/dL | Batas: 20–40 | Tinggi: > 40
    //   Ureum        — Normal: < 50 mg/dL | Batas: 50–100 | Tinggi: > 100
    //   Kreatinin    — L Normal: < 1.2, P: < 1.1 mg/dL
    //   eLFG         — Normal: ≥ 90 | Batas: 60–89 | Rendah: 30–59 | Kritis: < 30 mL/min
    // ─────────────────────────────────────────────────────────────────────────

    /** Fuzzifikasi Urea N / BUN (mg/dL) */
    private function fuzzifyUreaN(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 15.0, 25.0),
            'batas_t' => $this->triangle($x, 18.0, 30.0, 45.0),
            'tinggi'  => $this->trapezoidRight($x, 38.0, 55.0),
        ];
    }

    /** Fuzzifikasi Ureum (mg/dL) */
    private function fuzzifyUreum(float $x): array
    {
        return [
            'normal'  => $this->trapezoidLeft($x, 35.0, 55.0),
            'batas_t' => $this->triangle($x, 45.0, 75.0, 110.0),
            'tinggi'  => $this->trapezoidRight($x, 90.0, 120.0),
        ];
    }

    /**
     * Fuzzifikasi Kreatinin (mg/dL) — threshold berbeda per gender.
     *   L: Normal < 1.2, P: Normal < 1.1
     */
    private function fuzzifyKreatinin(float $x, string $gender): array
    {
        $normalTop  = ($gender === 'P') ? 1.1  : 1.2;
        $batasTop   = $normalTop + 0.4;
        $tinggiStart = $normalTop + 0.3;
        $tinggiTop  = $normalTop + 1.0;

        return [
            'normal'  => $this->trapezoidLeft($x, $normalTop - 0.2, $normalTop + 0.15),
            'batas_t' => $this->triangle($x, $normalTop, $normalTop + 0.25, $batasTop),
            'tinggi'  => $this->trapezoidRight($x, $tinggiStart, $tinggiTop),
        ];
    }

    /**
     * Fuzzifikasi eLFG / CKD-EPI (mL/min/1.73m²) — nilai RENDAH = RISIKO TINGGI (inverse).
     * Stage CKD: G1 ≥ 90 | G2 60–89 | G3 30–59 | G4-G5 < 30
     */
    private function fuzzifyELFG(float $x): array
    {
        return [
            // eLFG normal / G1 (≥ 90): fungsi ginjal baik
            'normal'   => $this->trapezoidRight($x, 80.0, 95.0),
            // eLFG G2 (60–89): penurunan ringan
            'batas_t'  => $this->triangle($x, 50.0, 72.0, 94.0),
            // eLFG G3 (30–59): penurunan sedang
            'rendah'   => $this->triangle($x, 20.0, 42.0, 68.0),
            // eLFG G4-G5 (< 30): gagal ginjal
            'kritis'   => $this->trapezoidLeft($x, 20.0, 35.0),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kelompok 5: Asam Urat
    // Referensi:
    //   L: Normal ≤ 7.0 mg/dL | Pra-Tinggi: 7.0–8.0 | Tinggi: > 8.0
    //   P: Normal ≤ 6.0 mg/dL | Pra-Tinggi: 6.0–7.0 | Tinggi: > 7.0
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fuzzifikasi Asam Urat (mg/dL) — gender-aware.
     */
    private function fuzzifyAsamUrat(float $x, string $gender): array
    {
        $normalTop   = ($gender === 'P') ? 6.0 : 7.0;
        $batasTop    = $normalTop + 1.0;
        $tinggiStart = $normalTop + 0.5;
        $tinggiTop   = $normalTop + 2.0;

        return [
            'normal'   => $this->trapezoidLeft($x, $normalTop - 1.0, $normalTop + 0.3),
            'pra_t'    => $this->triangle($x, $normalTop - 0.5, $normalTop + 0.5, $batasTop + 0.5),
            'tinggi'   => $this->trapezoidRight($x, $tinggiStart, $tinggiTop),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kelompok 6: Kardiovaskular & Tanda Vital
    // Referensi:
    //   Nadi        — Normal: 60–100 bpm | Abnormal: < 60 atau > 100 | Kritis: > 120
    //   Pernafasan  — Normal: 12–20 /mnt | Sedang: 20–30 | Tinggi: > 30
    //   Sistolik    — Normal: < 120 | Pra: 120–139 | HiperG1: 140–159 | HiperG2: ≥ 160 mmHg
    //   Diastolik   — Normal: < 80 | Pra: 80–89 | HiperG1: 90–99 | HiperG2: ≥ 100 mmHg
    // ─────────────────────────────────────────────────────────────────────────

    /** Fuzzifikasi Nadi (kali/menit) */
    private function fuzzifyNadi(float $x): array
    {
        return [
            // Normal: 60–100 bpm (MF segitiga dengan puncak di 80)
            'normal'    => $this->triangle($x, 55.0, 80.0, 105.0),
            // Bradikardi: < 60
            'bradikardi' => $this->trapezoidLeft($x, 45.0, 65.0),
            // Takikardi: > 100
            'takikardi'  => $this->trapezoidRight($x, 95.0, 120.0),
            // Sangat cepat: > 120 (kritis)
            'kritis'     => $this->trapezoidRight($x, 115.0, 140.0),
        ];
    }

    /** Fuzzifikasi Pernafasan (kali/menit) */
    private function fuzzifyPernafasan(float $x): array
    {
        return [
            'normal'  => $this->triangle($x, 10.0, 16.0, 22.0),
            'sedang'  => $this->triangle($x, 18.0, 25.0, 34.0),
            'tinggi'  => $this->trapezoidRight($x, 28.0, 38.0),
        ];
    }

    /** Fuzzifikasi Tekanan Darah Sistolik (mmHg) */
    private function fuzzifySistolik(float $x): array
    {
        return [
            'normal'    => $this->trapezoidLeft($x, 110.0, 130.0),
            'pra_hiper' => $this->triangle($x, 110.0, 130.0, 150.0),
            'hiper_g1'  => $this->triangle($x, 130.0, 150.0, 170.0),
            'hiper_g2'  => $this->trapezoidRight($x, 155.0, 175.0),
        ];
    }

    /** Fuzzifikasi Tekanan Darah Diastolik (mmHg) */
    private function fuzzifyDiastolik(float $x): array
    {
        return [
            'normal'    => $this->trapezoidLeft($x, 70.0, 85.0),
            'pra_hiper' => $this->triangle($x, 75.0, 84.0, 95.0),
            'hiper_g1'  => $this->triangle($x, 85.0, 94.0, 105.0),
            'hiper_g2'  => $this->trapezoidRight($x, 98.0, 110.0),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kelompok 7: Antropometri & Obesitas
    // Referensi WHO Asia Pasifik:
    //   IMT — Kurus: < 18.5 | Normal: 18.5–22.9 | Gemuk: 23–27.4 | Obesitas: ≥ 27.5
    //   Lingkar Perut — L: Normal < 90 | Risiko ≥ 90 | Tinggi ≥ 102 cm
    //                   P: Normal < 80 | Risiko ≥ 80 | Tinggi ≥ 88 cm
    // (Tinggi badan & berat badan dipakai untuk validasi IMT, tidak langsung di-fuzz)
    // ─────────────────────────────────────────────────────────────────────────

    /** Fuzzifikasi IMT / BMI (kg/m²) — klasifikasi Asia Pasifik */
    private function fuzzifyIMT(float $x): array
    {
        return [
            'kurus'    => $this->trapezoidLeft($x, 16.0, 18.5),
            'normal'   => $this->triangle($x, 18.5, 20.7, 23.0),
            'gemuk'    => $this->triangle($x, 22.0, 25.0, 28.0),
            'obesitas' => $this->trapezoidRight($x, 26.5, 30.0),
        ];
    }

    /**
     * Fuzzifikasi Lingkar Perut (cm) — gender-aware.
     *   L: Normal < 90 | Risiko 90–102 | Tinggi ≥ 102
     *   P: Normal < 80 | Risiko 80–88  | Tinggi ≥ 88
     */
    private function fuzzifyLingkarPerut(float $x, string $gender): array
    {
        if ($gender === 'P') {
            return [
                'normal'  => $this->trapezoidLeft($x, 72.0, 83.0),
                'risiko'  => $this->triangle($x, 78.0, 84.0, 93.0),
                'tinggi'  => $this->trapezoidRight($x, 87.0, 100.0),
            ];
        }

        // Default Laki-laki
        return [
            'normal'  => $this->trapezoidLeft($x, 80.0, 93.0),
            'risiko'  => $this->triangle($x, 88.0, 96.0, 106.0),
            'tinggi'  => $this->trapezoidRight($x, 100.0, 115.0),
        ];
    }

    // =========================================================================
    // STEP 2 — INFERENCE ENGINE (7 Method Terpisah per Kelompok)
    //
    // Operator AND (anteseden majemuk) : MIN
    // Agregasi output                   : MAX
    // Output set per kelompok:
    //   sehat         → area [0 – 35]
    //   risiko_sedang → area [25 – 70]
    //   risiko_tinggi → area [55 – 100]
    // =========================================================================

    /**
     * ── Kelompok 1: Aturan Fungsi Hati ──────────────────────────────────────
     *
     * R01: IF GOT Normal   AND GPT Normal   → Sehat
     * R02: IF GOT Sedang   OR  GPT Sedang   → Risiko Sedang
     * R03: IF GOT Tinggi   OR  GPT Tinggi   → Risiko Tinggi
     * R04: IF GOT Sedang   AND GPT Sedang   → Risiko Tinggi
     */
    private function applyRuleFungsiHati(array $mGOT, array $mGPT): array
    {
        $sehat   = 0.0;
        $sedang  = 0.0;
        $tinggi  = 0.0;

        // R01: Keduanya normal → sehat
        $sehat = max($sehat, min($mGOT['normal'], $mGPT['normal']));

        // R02: Salah satu sedang → risiko sedang
        $sedang = max($sedang, $mGOT['sedang']);
        $sedang = max($sedang, $mGPT['sedang']);

        // R03: Salah satu tinggi → risiko tinggi
        $tinggi = max($tinggi, $mGOT['tinggi']);
        $tinggi = max($tinggi, $mGPT['tinggi']);

        // R04: Keduanya sedang → risiko tinggi (saling memperburuk)
        $tinggi = max($tinggi, min($mGOT['sedang'], $mGPT['sedang']));

        return ['sehat' => $sehat, 'risiko_sedang' => $sedang, 'risiko_tinggi' => $tinggi];
    }

    /**
     * ── Kelompok 2: Aturan Diabetes / Gula Darah ────────────────────────────
     *
     * R01: IF GPS Normal AND G2PP Normal AND HbA1c Normal → Sehat
     * R02: IF GPS Pra-DM OR  G2PP Pra-DM OR  HbA1c Pra-DM → Risiko Sedang
     * R03: IF GPS DM                                         → Risiko Tinggi
     * R04: IF G2PP DM                                        → Risiko Tinggi
     * R05: IF HbA1c DM                                       → Risiko Tinggi
     * R06: IF GPS Pra-DM  AND G2PP Pra-DM                   → Risiko Tinggi
     * R07: IF GPS Pra-DM  AND HbA1c Pra-DM                  → Risiko Tinggi
     */
    private function applyRuleDiabetes(
        array $mGlukPuasa,
        array $mGluk2jPP,
        array $mHbA1c
    ): array {
        $sehat  = 0.0;
        $sedang = 0.0;
        $tinggi = 0.0;

        // R01
        $sehat = max($sehat, min($mGlukPuasa['normal'], $mGluk2jPP['normal'], $mHbA1c['normal']));

        // R02: Salah satu pra-DM
        $sedang = max($sedang, $mGlukPuasa['pra_dm']);
        $sedang = max($sedang, $mGluk2jPP['pra_dm']);
        $sedang = max($sedang, $mHbA1c['pra_dm']);

        // R03–R05: Salah satu sudah DM → langsung risiko tinggi
        $tinggi = max($tinggi, $mGlukPuasa['dm']);
        $tinggi = max($tinggi, $mGluk2jPP['dm']);
        $tinggi = max($tinggi, $mHbA1c['dm']);

        // R06: Dua parameter pra-DM → tinggi
        $tinggi = max($tinggi, min($mGlukPuasa['pra_dm'], $mGluk2jPP['pra_dm']));

        // R07: GPS + HbA1c pra-DM → tinggi
        $tinggi = max($tinggi, min($mGlukPuasa['pra_dm'], $mHbA1c['pra_dm']));

        return ['sehat' => $sehat, 'risiko_sedang' => $sedang, 'risiko_tinggi' => $tinggi];
    }

    /**
     * ── Kelompok 3: Aturan Profil Lipid ─────────────────────────────────────
     *
     * R01: IF CholTotal Optimal AND LDL Optimal AND HDL Optimal AND TG Normal     → Sehat
     * R02: IF CholTotal Batas   OR  LDL Batas                                     → Sedang
     * R03: IF TG Batas          AND CholTotal Batas                               → Sedang
     * R04: IF HDL Kurang        AND CholTotal Batas                               → Sedang
     * R05: IF CholTotal Tinggi  OR  LDL Tinggi    OR  LDL Sangat Tinggi          → Tinggi
     * R06: IF TG Tinggi         AND CholTotal Tinggi                              → Tinggi
     * R07: IF TG Sangat Tinggi                                                    → Tinggi
     * R08: IF HDL Rendah        AND LDL Tinggi                                    → Tinggi
     * R09: IF ApoB Tinggi       AND LDL Tinggi                                    → Tinggi
     * R10: IF ApoB Batas        AND CholTotal Batas                               → Sedang
     */
    private function applyRuleProfilLipid(
        array $mCholTotal,
        array $mLDL,
        array $mHDL,
        array $mTG,
        array $mApoB
    ): array {
        $sehat  = 0.0;
        $sedang = 0.0;
        $tinggi = 0.0;

        // R01
        $sehat = max($sehat, min(
            $mCholTotal['optimal'],
            $mLDL['optimal'],
            $mHDL['optimal'],
            $mTG['normal']
        ));

        // R02
        $sedang = max($sedang, $mCholTotal['batas_t']);
        $sedang = max($sedang, $mLDL['batas_t']);

        // R03
        $sedang = max($sedang, min($mTG['batas_t'], $mCholTotal['batas_t']));

        // R04
        $sedang = max($sedang, min($mHDL['kurang'], $mCholTotal['batas_t']));

        // R05
        $tinggi = max($tinggi, $mCholTotal['tinggi']);
        $tinggi = max($tinggi, $mLDL['tinggi']);
        $tinggi = max($tinggi, $mLDL['sangat_t']);

        // R06
        $tinggi = max($tinggi, min($mTG['tinggi'], $mCholTotal['tinggi']));

        // R07
        $tinggi = max($tinggi, $mTG['sangat_t']);

        // R08
        $tinggi = max($tinggi, min($mHDL['rendah'], $mLDL['tinggi']));

        // R09
        $tinggi = max($tinggi, min($mApoB['tinggi'], $mLDL['tinggi']));

        // R10
        $sedang = max($sedang, min($mApoB['batas_t'], $mCholTotal['batas_t']));

        return ['sehat' => $sehat, 'risiko_sedang' => $sedang, 'risiko_tinggi' => $tinggi];
    }

    /**
     * ── Kelompok 4: Aturan Fungsi Ginjal ────────────────────────────────────
     *
     * R01: IF UreaN Normal AND Ureum Normal AND Kreatinin Normal AND eLFG Normal → Sehat
     * R02: IF UreaN Batas  OR  Ureum Batas  OR  Kreatinin Batas                 → Sedang
     * R03: IF eLFG Batas                                                         → Sedang
     * R04: IF UreaN Tinggi OR  Ureum Tinggi OR  Kreatinin Tinggi                → Tinggi
     * R05: IF eLFG Rendah                                                        → Tinggi
     * R06: IF eLFG Kritis                                                        → Tinggi
     * R07: IF UreaN Tinggi AND Kreatinin Tinggi                                  → Tinggi
     * R08: IF Ureum Batas  AND eLFG Batas                                        → Sedang
     */
    private function applyRuleFungsiGinjal(
        array $mUreaN,
        array $mUreum,
        array $mKreatinin,
        array $mELFG
    ): array {
        $sehat  = 0.0;
        $sedang = 0.0;
        $tinggi = 0.0;

        // R01
        $sehat = max($sehat, min(
            $mUreaN['normal'],
            $mUreum['normal'],
            $mKreatinin['normal'],
            $mELFG['normal']
        ));

        // R02
        $sedang = max($sedang, $mUreaN['batas_t']);
        $sedang = max($sedang, $mUreum['batas_t']);
        $sedang = max($sedang, $mKreatinin['batas_t']);

        // R03
        $sedang = max($sedang, $mELFG['batas_t']);

        // R04
        $tinggi = max($tinggi, $mUreaN['tinggi']);
        $tinggi = max($tinggi, $mUreum['tinggi']);
        $tinggi = max($tinggi, $mKreatinin['tinggi']);

        // R05–R06: eLFG rendah atau kritis
        $tinggi = max($tinggi, $mELFG['rendah']);
        $tinggi = max($tinggi, $mELFG['kritis']);

        // R07
        $tinggi = max($tinggi, min($mUreaN['tinggi'], $mKreatinin['tinggi']));

        // R08
        $sedang = max($sedang, min($mUreum['batas_t'], $mELFG['batas_t']));

        return ['sehat' => $sehat, 'risiko_sedang' => $sedang, 'risiko_tinggi' => $tinggi];
    }

    /**
     * ── Kelompok 5: Aturan Asam Urat ────────────────────────────────────────
     *
     * R01: IF AsamUrat Normal   → Sehat
     * R02: IF AsamUrat Pra-T    → Risiko Sedang
     * R03: IF AsamUrat Tinggi   → Risiko Tinggi
     */
    private function applyRuleAsamUrat(array $mAsamUrat): array
    {
        return [
            'sehat'         => $mAsamUrat['normal'],
            'risiko_sedang' => $mAsamUrat['pra_t'],
            'risiko_tinggi' => $mAsamUrat['tinggi'],
        ];
    }

    /**
     * ── Kelompok 6: Aturan Kardiovaskular & Tanda Vital ─────────────────────
     *
     * R01: IF Sis Normal AND Dia Normal AND Nadi Normal AND Naf Normal  → Sehat
     * R02: IF Sis Pra    OR  Dia Pra                                    → Sedang
     * R03: IF Nadi Takikardi                                             → Sedang
     * R04: IF Naf Sedang                                                 → Sedang
     * R05: IF Sis HiperG1 OR Dia HiperG1                                → Tinggi
     * R06: IF Sis HiperG2 OR Dia HiperG2                                → Tinggi
     * R07: IF Nadi Kritis                                                → Tinggi
     * R08: IF Naf Tinggi                                                 → Tinggi
     * R09: IF Sis HiperG1 AND Dia HiperG1                               → Tinggi
     * R10: IF Nadi Bradikardi AND Sis HiperG1                           → Tinggi
     */
    private function applyRuleKardiovaskular(
        array $mNadi,
        array $mNaf,
        array $mSis,
        array $mDia
    ): array {
        $sehat  = 0.0;
        $sedang = 0.0;
        $tinggi = 0.0;

        // R01
        $sehat = max($sehat, min(
            $mSis['normal'],
            $mDia['normal'],
            $mNadi['normal'],
            $mNaf['normal']
        ));

        // R02
        $sedang = max($sedang, $mSis['pra_hiper']);
        $sedang = max($sedang, $mDia['pra_hiper']);

        // R03
        $sedang = max($sedang, $mNadi['takikardi']);

        // R04
        $sedang = max($sedang, $mNaf['sedang']);

        // R05
        $tinggi = max($tinggi, $mSis['hiper_g1']);
        $tinggi = max($tinggi, $mDia['hiper_g1']);

        // R06
        $tinggi = max($tinggi, $mSis['hiper_g2']);
        $tinggi = max($tinggi, $mDia['hiper_g2']);

        // R07
        $tinggi = max($tinggi, $mNadi['kritis']);

        // R08
        $tinggi = max($tinggi, $mNaf['tinggi']);

        // R09: Keduanya hipertensi G1 → agresif
        $tinggi = max($tinggi, min($mSis['hiper_g1'], $mDia['hiper_g1']));

        // R10: Bradikardia + hipertensi G1
        $tinggi = max($tinggi, min($mNadi['bradikardi'], $mSis['hiper_g1']));

        return ['sehat' => $sehat, 'risiko_sedang' => $sedang, 'risiko_tinggi' => $tinggi];
    }

    /**
     * ── Kelompok 7: Aturan Antropometri & Obesitas ──────────────────────────
     *
     * R01: IF IMT Normal  AND LP Normal  → Sehat
     * R02: IF IMT Kurus   AND LP Normal  → Sehat (underweight tapi normal LP)
     * R03: IF IMT Gemuk   OR  LP Risiko  → Sedang
     * R04: IF IMT Gemuk   AND LP Risiko  → Tinggi
     * R05: IF IMT Obesitas               → Tinggi
     * R06: IF LP Tinggi                  → Tinggi
     * R07: IF IMT Obesitas AND LP Tinggi  → Tinggi (kombinasi terburuk)
     */
    private function applyRuleAntropometri(array $mIMT, array $mLP): array
    {
        $sehat  = 0.0;
        $sedang = 0.0;
        $tinggi = 0.0;

        // R01
        $sehat = max($sehat, min($mIMT['normal'], $mLP['normal']));

        // R02
        $sehat = max($sehat, min($mIMT['kurus'], $mLP['normal']));

        // R03
        $sedang = max($sedang, $mIMT['gemuk']);
        $sedang = max($sedang, $mLP['risiko']);

        // R04
        $tinggi = max($tinggi, min($mIMT['gemuk'], $mLP['risiko']));

        // R05
        $tinggi = max($tinggi, $mIMT['obesitas']);

        // R06
        $tinggi = max($tinggi, $mLP['tinggi']);

        // R07
        $tinggi = max($tinggi, min($mIMT['obesitas'], $mLP['tinggi']));

        return ['sehat' => $sehat, 'risiko_sedang' => $sedang, 'risiko_tinggi' => $tinggi];
    }

    // =========================================================================
    // STEP 3 — DEFUZZIFICATION (Centroid / Center of Area)
    //
    // Universe output : [0, 100]
    // MF output sets  :
    //   Sehat         : trapesium kiri  (0 – 40)   → puncak di [0, 20]
    //   Risiko Sedang : segitiga        (20 – 75)  → puncak di 47
    //   Risiko Tinggi : trapesium kanan (55 – 100) → puncak di [80, 100]
    //
    // z* = Σ(z · μ_agregat(z)) / Σ(μ_agregat(z))
    // =========================================================================

    /**
     * Defuzzifikasi Centroid (CoA) untuk satu kelompok.
     *
     * @param  array  $ruleOutputs  ['sehat'=>float, 'risiko_sedang'=>float, 'risiko_tinggi'=>float]
     * @return float  Skor crisp [0, 100]
     */
    private function defuzzifyGroup(array $ruleOutputs): float
    {
        $alphaSehat  = $ruleOutputs['sehat']         ?? 0.0;
        $alphaSedang = $ruleOutputs['risiko_sedang'] ?? 0.0;
        $alphaTinggi = $ruleOutputs['risiko_tinggi'] ?? 0.0;

        $steps       = 200;
        $start       = 0.0;
        $end         = 100.0;
        $step        = ($end - $start) / $steps;
        $numerator   = 0.0;
        $denominator = 0.0;

        for ($i = 0; $i <= $steps; $i++) {
            $z = $start + ($i * $step);

            // MF output per set (sebelum dipotong alpha)
            $muSehat  = $this->trapezoidLeft($z, 20.0, 40.0);
            $muSedang = $this->triangle($z, 20.0, 47.0, 75.0);
            $muTinggi = $this->trapezoidRight($z, 55.0, 80.0);

            // Pemotongan (clipping) dengan kekuatan aturan
            $muSehat  = min($alphaSehat,  $muSehat);
            $muSedang = min($alphaSedang, $muSedang);
            $muTinggi = min($alphaTinggi, $muTinggi);

            // Agregasi MAX
            $muAgregat = max($muSehat, $muSedang, $muTinggi);

            $numerator   += $z * $muAgregat;
            $denominator += $muAgregat;
        }

        // Jika tidak ada aturan yang aktif → default ke 10 (sehat ringan)
        if ($denominator == 0) {
            return 10.0;
        }

        return round($numerator / $denominator, 4);
    }

    // =========================================================================
    // STEP 4 — GENERATE RECOMMENDATION
    // =========================================================================

    /**
     * Hasilkan label risiko global dan teks rekomendasi berdasarkan 7 skor kelompok.
     *
     * Batas severity (sesuai seeder):
     *   Ringan : 0  – 29
     *   Sedang : 30 – 49
     *   Tinggi : 50 – 69
     *   Kritis : 70 – 100
     *
     * @param  float  $averageScore  Rata-rata dari 7 skor kelompok
     * @param  array  $groupScores   ['fungsi_hati'=>float, 'diabetes'=>float, ...]
     * @return array  ['risk_label', 'duration', 'rec_diet', 'rec_exercise', 'rec_notes']
     */
    public function generateRecommendation(float $averageScore, array $groupScores): array
    {
        $recDiet     = [];
        $recExercise = [];
        $recNotes    = [];

        // ── Tentukan label & durasi global berdasarkan rata-rata skor ────────
        if ($averageScore <= 29) {
            $globalLevel = 'ringan';
            $label       = 'Ringan';
            $duration    = '6 bulan';
        } elseif ($averageScore <= 49) {
            $globalLevel = 'sedang';
            $label       = 'Sedang';
            $duration    = '6–12 bulan';
        } elseif ($averageScore <= 69) {
            $globalLevel = 'tinggi';
            $label       = 'Tinggi';
            $duration    = '12 bulan';
        } else {
            $globalLevel = 'kritis';
            $label       = 'Kritis';
            $duration    = '12–24 bulan';
        }

        // ── Ambil rekomendasi per kelompok berdasarkan skor masing-masing ───
        foreach ($groupScores as $groupCode => $score) {

            // Tentukan severity untuk kelompok ini
            if ($score <= 29) {
                $severity = 'ringan';
            } elseif ($score <= 49) {
                $severity = 'sedang';
            } elseif ($score <= 69) {
                $severity = 'tinggi';
            } else {
                $severity = 'kritis';
            }

            $groupRules = RecommendationRule::query()
                ->where('is_active', true)
                ->where('group_code', $groupCode)
                ->where('severity_level', $severity)
                ->get();

            foreach ($groupRules as $rule) {
                match ($rule->category) {
                    'pola makan'     => $recDiet[]     = $rule->recommendation_text,
                    'olahraga' => $recExercise[] = $rule->recommendation_text,
                    'catatan'     => $recNotes[]    = $rule->recommendation_text,
                    default    => null,
                };
            }
        }

        // ── Catatan prioritas untuk kelompok dengan skor kritis (≥ 70) ───────
        $priorityGroups = array_filter($groupScores, fn($s) => $s >= 70);
        arsort($priorityGroups);

        $groupLabels = [
            'fungsi_hati'   => 'Fungsi Hati',
            'diabetes'      => 'Diabetes / Gula Darah',
            'profil_lipid'  => 'Profil Lipid',
            'fungsi_ginjal' => 'Fungsi Ginjal',
            'asam_urat'     => 'Asam Urat',
            'kardiovaskular' => 'Kardiovaskular & Tanda Vital',
            'antropometri'  => 'Antropometri & Obesitas',
        ];

        foreach ($priorityGroups as $group => $score) {
            $groupName  = $groupLabels[$group] ?? ucwords(str_replace('_', ' ', $group));
            $recNotes[] = sprintf(
                '%s (Skor %.1f – Kritis): Disarankan perhatian medis khusus dan konsultasi lebih lanjut dengan dokter terkait.',
                $groupName,
                $score
            );
        }

        return [
            'risk_label'   => $label,
            'duration'     => $duration,
            'rec_diet'     => array_values(array_unique($recDiet)),
            'rec_exercise' => array_values(array_unique($recExercise)),
            'rec_notes'    => array_values(array_unique($recNotes)),
        ];
    }

    // =========================================================================
    // PIPELINE UTAMA — process()
    // Jalankan seluruh Fuzzy Hierarki sekaligus untuk 1 pasien.
    // =========================================================================

    /**
     * Jalankan pipeline Fuzzy Mamdani Hierarkis lengkap.
     *
     * @param  array  $inputs  Nilai crisp 23 parameter + gender:
     *   [
     *     'got'           => float,
     *     'gpt'           => float,
     *     'glukosa_puasa' => float,
     *     'glukosa_2j_pp' => float,
     *     'hba1c'         => float,
     *     'chol_total'    => float,
     *     'chol_ldl'      => float,
     *     'chol_hdl'      => float,
     *     'trigliserida'  => float,
     *     'apo_b'         => float,
     *     'urea_n'        => float,
     *     'ureum'         => float,
     *     'kreatinin'     => float,
     *     'elfg'          => float,
     *     'asam_urat'     => float,
     *     'nadi'          => float,
     *     'pernafasan'    => float,
     *     'sistolik'      => float,
     *     'diastolik'     => float,
     *     'tinggi_badan'  => float,
     *     'berat_badan'   => float,
     *     'imt'           => float,
     *     'lingkar_perut' => float,
     *     'gender'        => 'L'|'P',
     *   ]
     *
     * @return array  [
     *   'group_scores'  => array,  // 7 skor kelompok (float)
     *   'risk_score'    => float,  // rata-rata global
     *   'risk_label'    => string,
     *   'duration'      => string,
     *   'rec_diet'      => array,
     *   'rec_exercise'  => array,
     *   'rec_notes'     => array,
     * ]
     */
    public function process(array $inputs): array
    {
        $gender = strtoupper(trim($inputs['gender'] ?? 'L'));

        // ── Ekstrak nilai input ───────────────────────────────────────────────
        $got          = (float) ($inputs['got']          ?? 0);
        $gpt          = (float) ($inputs['gpt']          ?? 0);
        $glukPuasa    = (float) ($inputs['glukosa_puasa'] ?? 0);
        $gluk2jPP     = (float) ($inputs['glukosa_2j_pp'] ?? 0);
        $hba1c        = (float) ($inputs['hba1c']        ?? 0);
        $cholTotal    = (float) ($inputs['chol_total']   ?? 0);
        $cholLDL      = (float) ($inputs['chol_ldl']     ?? 0);
        $cholHDL      = (float) ($inputs['chol_hdl']     ?? 0);
        $trigliserida = (float) ($inputs['trigliserida'] ?? 0);
        $apoB         = (float) ($inputs['apo_b']        ?? 0);
        $ureaN        = (float) ($inputs['urea_n']       ?? 0);
        $ureum        = (float) ($inputs['ureum']        ?? 0);
        $kreatinin    = (float) ($inputs['kreatinin']    ?? 0);
        $elfg         = (float) ($inputs['elfg']         ?? 0);
        $asamUrat     = (float) ($inputs['asam_urat']    ?? 0);
        $nadi         = (float) ($inputs['nadi']         ?? 0);
        $pernafasan   = (float) ($inputs['pernafasan']   ?? 0);
        $sistolik     = (float) ($inputs['sistolik']     ?? 0);
        $diastolik    = (float) ($inputs['diastolik']    ?? 0);
        $imt          = (float) ($inputs['imt']          ?? 0);
        $lingkarPerut = (float) ($inputs['lingkar_perut'] ?? 0);

        // ── Kelompok 1: Fungsi Hati ───────────────────────────────────────────
        $mGOT = $this->fuzzifyGOT($got);
        $mGPT = $this->fuzzifyGPT($gpt);
        $ruleHati = $this->applyRuleFungsiHati($mGOT, $mGPT);

        // ── Kelompok 2: Diabetes ──────────────────────────────────────────────
        $mGlukPuasa = $this->fuzzifyGlukosaPuasa($glukPuasa);
        $mGluk2jPP  = $this->fuzzifyGlukosa2jPP($gluk2jPP);
        $mHbA1c     = $this->fuzzifyHbA1c($hba1c);
        $ruleDiabetes = $this->applyRuleDiabetes($mGlukPuasa, $mGluk2jPP, $mHbA1c);

        // ── Kelompok 3: Profil Lipid ──────────────────────────────────────────
        $mCholTotal = $this->fuzzifyCholTotal($cholTotal);
        $mLDL       = $this->fuzzifyCholLDL($cholLDL);
        $mHDL       = $this->fuzzifyCholHDL($cholHDL);
        $mTG        = $this->fuzzifyTrigliserida($trigliserida);
        $mApoB      = $this->fuzzifyApoB($apoB);
        $ruleLipid = $this->applyRuleProfilLipid($mCholTotal, $mLDL, $mHDL, $mTG, $mApoB);

        // ── Kelompok 4: Fungsi Ginjal ─────────────────────────────────────────
        $mUreaN    = $this->fuzzifyUreaN($ureaN);
        $mUreum    = $this->fuzzifyUreum($ureum);
        $mKreatinin = $this->fuzzifyKreatinin($kreatinin, $gender);
        $mELFG     = $this->fuzzifyELFG($elfg);
        $ruleGinjal = $this->applyRuleFungsiGinjal($mUreaN, $mUreum, $mKreatinin, $mELFG);

        // ── Kelompok 5: Asam Urat ─────────────────────────────────────────────
        $mAsamUrat = $this->fuzzifyAsamUrat($asamUrat, $gender);
        $ruleAsamUrat = $this->applyRuleAsamUrat($mAsamUrat);

        // ── Kelompok 6: Kardiovaskular ────────────────────────────────────────
        $mNadi    = $this->fuzzifyNadi($nadi);
        $mNaf     = $this->fuzzifyPernafasan($pernafasan);
        $mSis     = $this->fuzzifySistolik($sistolik);
        $mDia     = $this->fuzzifyDiastolik($diastolik);
        $ruleKardio = $this->applyRuleKardiovaskular($mNadi, $mNaf, $mSis, $mDia);

        // ── Kelompok 7: Antropometri ──────────────────────────────────────────
        $mIMT = $this->fuzzifyIMT($imt);
        $mLP  = $this->fuzzifyLingkarPerut($lingkarPerut, $gender);
        $ruleAntro = $this->applyRuleAntropometri($mIMT, $mLP);

        // ── Defuzzifikasi: 7 skor kelompok ────────────────────────────────────
        $groupScores = [
            'fungsi_hati'    => $this->defuzzifyGroup($ruleHati),
            'diabetes'       => $this->defuzzifyGroup($ruleDiabetes),
            'profil_lipid'   => $this->defuzzifyGroup($ruleLipid),
            'fungsi_ginjal'  => $this->defuzzifyGroup($ruleGinjal),
            'asam_urat'      => $this->defuzzifyGroup($ruleAsamUrat),
            'kardiovaskular' => $this->defuzzifyGroup($ruleKardio),
            'antropometri'   => $this->defuzzifyGroup($ruleAntro),
        ];

        // ── Rata-rata skor global ─────────────────────────────────────────────
        $averageScore = round(array_sum($groupScores) / count($groupScores), 2);

        // ── Generate rekomendasi berbasis 7 skor ──────────────────────────────
        $recommendation = $this->generateRecommendation($averageScore, $groupScores);

        return array_merge(
            [
                'group_scores' => $groupScores,
                'risk_score'   => $averageScore,
            ],
            $recommendation
        );
    }

    // =========================================================================
    // MEMBERSHIP FUNCTION PRIMITIVES
    // =========================================================================

    /**
     * Fungsi keanggotaan segitiga (triangular MF).
     *
     *         1|       /\
     *          |      /  \
     *        0 |____/      \____
     *               a   b   c
     */
    private function triangle(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c) {
            return 0.0;
        }
        if ($x === $b) {
            return 1.0;
        }
        if ($x < $b) {
            return ($x - $a) / ($b - $a);
        }
        return ($c - $x) / ($c - $b);
    }

    /**
     * Fungsi keanggotaan trapesium kiri / shoulder kiri.
     *
     *   1|________
     *    |        \
     *   0|         \____
     *               a    b
     */
    private function trapezoidLeft(float $x, float $a, float $b): float
    {
        if ($x <= $a) {
            return 1.0;
        }
        if ($x >= $b) {
            return 0.0;
        }
        return ($b - $x) / ($b - $a);
    }

    /**
     * Fungsi keanggotaan trapesium kanan / shoulder kanan.
     *
     *             ________
     *            /
     *   ______ /
     *          a    b
     */
    private function trapezoidRight(float $x, float $a, float $b): float
    {
        if ($x <= $a) {
            return 0.0;
        }
        if ($x >= $b) {
            return 1.0;
        }
        return ($x - $a) / ($b - $a);
    }
}
