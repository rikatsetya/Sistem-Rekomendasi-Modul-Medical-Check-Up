<?php

namespace App\Services;

/**
 * ============================================================================
 * FuzzyMamdaniService
 * ============================================================================
 *
 * Implementasi sistem inferensi Fuzzy Mamdani untuk menilai risiko kesehatan
 * karyawan berdasarkan data Medical Check-Up (MCU) tahunan.
 *
 * Pipeline:
 *  1. fuzzify()              — Konversi nilai crisp → derajat keanggotaan
 *  2. applyRules()           — Evaluasi 15 aturan IF-THEN (metode MIN & MAX)
 *  3. defuzzify()            — Defuzzifikasi Centroid / Center of Area (CoA)
 *  4. generateRecommendation() — Pemetaan skor → label + teks rekomendasi
 *
 * Referensi metode:
 *  - AND   : operator MIN
 *  - OR    : operator MAX
 *  - Agregasi output : MAX
 *  - Defuzzifikasi   : Centroid (CoA) pada universe [0, 100]
 *
 * Input variabel fuzzy yang digunakan:
 *  - BMI           : IMT (kg/m2)
 *  - Sistolik      : Tekanan darah sistolik (mmHg)
 *  - Glukosa Puasa : Glukosa puasa (mg/dL)
 *  - Kolesterol    : Kolesterol total (mg/dL)
 *  - Asam Urat     : Kadar asam urat (mg/dL) — threshold berbeda per gender
 *  - Trigliserida  : Trigliserida (mg/dL)
 *
 * ============================================================================
 */
class FuzzyMamdaniService
{
    // =========================================================================
    // STEP 1 — FUZZIFICATION
    // Mengubah nilai crisp (numerik) menjadi derajat keanggotaan (0.0 – 1.0)
    // untuk setiap variabel linguistik.
    //
    // Fungsi keanggotaan yang digunakan:
    //   - trapezoid()  : tepi kiri / kanan terbuka (shoulder function)
    //   - triangle()   : segitiga simetris / tidak simetris
    // =========================================================================

    /**
     * Hitung derajat keanggotaan untuk semua variabel input.
     *
     * @param  array  $inputs  Asosiatif: ['bmi'=>float, 'sistolik'=>float,
     *                                    'glukosa'=>float, 'kolesterol'=>float,
     *                                    'asam_urat'=>float, 'trigliserida'=>float,
     *                                    'gender'=>'L'|'P']
     * @return array  Array 2-level: ['bmi'=>['kurus'=>0.0,'normal'=>1.0,...], ...]
     */
    public function fuzzify(array $inputs): array
    {
        $bmi         = (float) ($inputs['bmi']         ?? 0);
        $sistolik    = (float) ($inputs['sistolik']    ?? 0);
        $glukosa     = (float) ($inputs['glukosa']     ?? 0);
        $kolesterol  = (float) ($inputs['kolesterol']  ?? 0);
        $asamUrat    = (float) ($inputs['asam_urat']   ?? 0);
        $trigliserida= (float) ($inputs['trigliserida']?? 0);
        $gender      = strtoupper(trim($inputs['gender'] ?? 'L'));

        return [
            'bmi'          => $this->fuzzifyBMI($bmi),
            'sistolik'     => $this->fuzzifySistolik($sistolik),
            'glukosa'      => $this->fuzzifyGlukosa($glukosa),
            'kolesterol'   => $this->fuzzifyKolesterol($kolesterol),
            'asam_urat'    => $this->fuzzifyAsamUrat($asamUrat, $gender),
            'trigliserida' => $this->fuzzifyTrigliserida($trigliserida),
        ];
    }

    // -------------------------------------------------------------------------
    // BMI / IMT  —  Skala WHO untuk Asia Pasifik
    //   Kurus   : < 18.5
    //   Normal  : 18.5 – 22.9
    //   Gemuk   : 23.0 – 24.9  (Overweight)
    //   Obesitas: ≥ 25
    // -------------------------------------------------------------------------
    private function fuzzifyBMI(float $x): array
    {
        return [
            // Kurus: MF trapesium kiri, puncak di ≤16.0, turun sampai 18.5
            'kurus'    => $this->trapezoidLeft($x, 16.0, 18.5),

            // Normal: segitiga dengan puncak di 20.7, rentang 18.5–22.9
            'normal'   => $this->triangle($x, 18.5, 20.7, 22.9),

            // Gemuk: segitiga dengan puncak di 23.95, rentang 22.9–25.0
            'gemuk'    => $this->triangle($x, 22.9, 23.95, 25.0),

            // Obesitas: MF trapesium kanan, mulai naik di 24.0, penuh di ≥25
            'obesitas' => $this->trapezoidRight($x, 24.0, 25.0),
        ];
    }

    // -------------------------------------------------------------------------
    // Tekanan Darah Sistolik  (mmHg)
    //   Normal       : < 120
    //   Pra-Hiper.   : 120 – 139
    //   Hipertensi G1: 140 – 159
    //   Hipertensi G2: ≥ 160
    // -------------------------------------------------------------------------
    private function fuzzifySistolik(float $x): array
    {
        return [
            // Normal: trapesium kiri, puncak ≤110, turun ke 130
            'normal'      => $this->trapezoidLeft($x, 110.0, 130.0),

            // Pra-Hipertensi: segitiga, puncak di 130, rentang 110–150
            'pra_hiper'   => $this->triangle($x, 110.0, 130.0, 150.0),

            // Hipertensi G1: segitiga, puncak di 150, rentang 130–170
            'hiper_g1'    => $this->triangle($x, 130.0, 150.0, 170.0),

            // Hipertensi G2: trapesium kanan, mulai naik di 155, penuh di ≥170
            'hiper_g2'    => $this->trapezoidRight($x, 155.0, 170.0),
        ];
    }

    // -------------------------------------------------------------------------
    // Glukosa Puasa  (mg/dL)
    //   Normal   : < 100
    //   Pra-DM   : 100 – 125
    //   Diabetes : ≥ 126
    // -------------------------------------------------------------------------
    private function fuzzifyGlukosa(float $x): array
    {
        return [
            // Normal: trapesium kiri, puncak ≤90, turun ke 110
            'normal' => $this->trapezoidLeft($x, 90.0, 110.0),

            // Pra-DM: segitiga, puncak di 112, rentang 90–135
            'pra_dm' => $this->triangle($x, 90.0, 112.0, 135.0),

            // Diabetes: trapesium kanan, mulai naik di 120, penuh di ≥140
            'dm'     => $this->trapezoidRight($x, 120.0, 140.0),
        ];
    }

    // -------------------------------------------------------------------------
    // Kolesterol Total  (mg/dL)
    //   Optimal : < 200
    //   Batas T.: 200 – 239
    //   Tinggi  : ≥ 240
    // -------------------------------------------------------------------------
    private function fuzzifyKolesterol(float $x): array
    {
        return [
            'optimal' => $this->trapezoidLeft($x, 170.0, 210.0),
            'batas_t' => $this->triangle($x, 185.0, 220.0, 255.0),
            'tinggi'  => $this->trapezoidRight($x, 230.0, 260.0),
        ];
    }

    // -------------------------------------------------------------------------
    // Asam Urat  (mg/dL)  — threshold berbeda per gender
    //   L (Laki-laki) : Normal ≤ 7.0, Tinggi > 7.0
    //   P (Perempuan) : Normal ≤ 6.0, Tinggi > 6.0
    // -------------------------------------------------------------------------
    private function fuzzifyAsamUrat(float $x, string $gender): array
    {
        // Tentukan batas normal berdasarkan gender
        $normalCap = ($gender === 'P') ? 6.0 : 7.0;  // batas atas normal
        $highStart = $normalCap;                       // mulai naik "tinggi"
        $highFull  = $normalCap + 1.5;                 // MF "tinggi" penuh

        return [
            // Normal: trapesium kiri
            'normal' => $this->trapezoidLeft($x, $normalCap - 1.0, $normalCap + 0.5),

            // Tinggi: trapesium kanan
            'tinggi' => $this->trapezoidRight($x, $highStart, $highFull),
        ];
    }

    // -------------------------------------------------------------------------
    // Trigliserida  (mg/dL)
    //   Normal     : < 150
    //   Batas T.   : 150 – 199
    //   Tinggi     : 200 – 499
    //   Sangat T.  : ≥ 500
    // -------------------------------------------------------------------------
    private function fuzzifyTrigliserida(float $x): array
    {
        return [
            'normal'   => $this->trapezoidLeft($x, 120.0, 165.0),
            'batas_t'  => $this->triangle($x, 135.0, 175.0, 215.0),
            'tinggi'   => $this->triangle($x, 185.0, 320.0, 510.0),
            'sangat_t' => $this->trapezoidRight($x, 460.0, 520.0),
        ];
    }

    // =========================================================================
    // STEP 2 — RULE BASE & INFERENCE ENGINE
    // 15 aturan IF-THEN eksplisit.
    // Operator AND  → MIN
    // Agregasi      → MAX (per output set)
    // =========================================================================

    /**
     * Terapkan 15 aturan fuzzy dan kembalikan kekuatan agregasi per output set.
     *
     * @param  array  $m  Hasil fuzzify() — derajat keanggotaan per variabel
     * @return array  ['sehat'=>float, 'risiko_sedang'=>float, 'risiko_tinggi'=>float]
     *                Nilai 0.0–1.0 (kekuatan pemotongan MF output)
     */
    public function applyRules(array $m): array
    {
        // Inisialisasi kekuatan agregasi output (akan diambil nilai MAX)
        $sehat        = 0.0;
        $risikoSedang = 0.0;
        $risikoTinggi = 0.0;

        // Fungsi pembantu agar kode aturan lebih ringkas
        $bmi   = $m['bmi'];
        $sis   = $m['sistolik'];
        $glu   = $m['glukosa'];
        $kol   = $m['kolesterol'];
        $ua    = $m['asam_urat'];
        $tg    = $m['trigliserida'];

        // ------------------------------------------------------------------
        // R01: IF BMI Normal  AND Sistolik Normal AND Glukosa Normal
        //      THEN Risiko is Sehat
        // Kondisi: semua parameter utama dalam batas normal
        // ------------------------------------------------------------------
        $strength = min($bmi['normal'], $sis['normal'], $glu['normal']);
        $sehat = max($sehat, $strength);

        // ------------------------------------------------------------------
        // R02: IF BMI Gemuk AND Sistolik Normal AND Glukosa Normal
        //      THEN Risiko is Risiko Sedang
        // Kondisi: berat badan berlebih tetapi tekanan darah & gula normal
        // ------------------------------------------------------------------
        $strength = min($bmi['gemuk'], $sis['normal'], $glu['normal']);
        $risikoSedang = max($risikoSedang, $strength);

        // ------------------------------------------------------------------
        // R03: IF BMI Obesitas
        //      THEN Risiko is Risiko Tinggi
        // Kondisi: obesitas merupakan faktor risiko utama kardiovaskuler
        // ------------------------------------------------------------------
        $strength = $bmi['obesitas'];
        $risikoTinggi = max($risikoTinggi, $strength);

        // ------------------------------------------------------------------
        // R04: IF Sistolik is Hipertensi Grade 2
        //      THEN Risiko is Risiko Tinggi
        // Kondisi: hipertensi berat, perlu intervensi segera
        // ------------------------------------------------------------------
        $strength = $sis['hiper_g2'];
        $risikoTinggi = max($risikoTinggi, $strength);

        // ------------------------------------------------------------------
        // R05: IF Sistolik is Hipertensi Grade 1 AND BMI Gemuk
        //      THEN Risiko is Risiko Tinggi
        // Kondisi: kombinasi hipertensi + overweight meningkatkan risiko
        // ------------------------------------------------------------------
        $strength = min($sis['hiper_g1'], $bmi['gemuk']);
        $risikoTinggi = max($risikoTinggi, $strength);

        // ------------------------------------------------------------------
        // R06: IF Glukosa is Diabetes
        //      THEN Risiko is Risiko Tinggi
        // Kondisi: kadar gula puasa dalam rentang diabetes
        // ------------------------------------------------------------------
        $strength = $glu['dm'];
        $risikoTinggi = max($risikoTinggi, $strength);

        // ------------------------------------------------------------------
        // R07: IF Glukosa is Pra-DM AND BMI Gemuk
        //      THEN Risiko is Risiko Sedang
        // Kondisi: pra-diabetes disertai overweight
        // ------------------------------------------------------------------
        $strength = min($glu['pra_dm'], $bmi['gemuk']);
        $risikoSedang = max($risikoSedang, $strength);

        // ------------------------------------------------------------------
        // R08: IF Kolesterol Tinggi AND Trigliserida Tinggi
        //      THEN Risiko is Risiko Tinggi
        // Kondisi: dislipidemia ganda — risiko aterosklerosis tinggi
        // ------------------------------------------------------------------
        $strength = min($kol['tinggi'], $tg['tinggi']);
        $risikoTinggi = max($risikoTinggi, $strength);

        // ------------------------------------------------------------------
        // R09: IF Kolesterol Tinggi AND BMI Normal
        //      THEN Risiko is Risiko Sedang
        // Kondisi: kolesterol tinggi tetapi berat badan normal
        // ------------------------------------------------------------------
        $strength = min($kol['tinggi'], $bmi['normal']);
        $risikoSedang = max($risikoSedang, $strength);

        // ------------------------------------------------------------------
        // R10: IF Asam Urat Tinggi AND Glukosa Pra-DM
        //      THEN Risiko is Risiko Tinggi
        // Kondisi: hiperurisemia + pra-diabetes → risiko sindrom metabolik
        // ------------------------------------------------------------------
        $strength = min($ua['tinggi'], $glu['pra_dm']);
        $risikoTinggi = max($risikoTinggi, $strength);

        // ------------------------------------------------------------------
        // R11: IF Asam Urat Tinggi AND BMI Normal
        //      THEN Risiko is Risiko Sedang
        // Kondisi: hiperurisemia terisolasi, BMI masih normal
        // ------------------------------------------------------------------
        $strength = min($ua['tinggi'], $bmi['normal']);
        $risikoSedang = max($risikoSedang, $strength);

        // ------------------------------------------------------------------
        // R12: IF Trigliserida Sangat Tinggi
        //      THEN Risiko is Risiko Tinggi
        // Kondisi: hipertrigliseridemia berat → risiko pankreatitis
        // ------------------------------------------------------------------
        $strength = $tg['sangat_t'];
        $risikoTinggi = max($risikoTinggi, $strength);

        // ------------------------------------------------------------------
        // R13: IF Trigliserida Batas Tinggi AND Kolesterol Batas Tinggi
        //      THEN Risiko is Risiko Sedang
        // Kondisi: dua parameter di batas atas normal, perlu pemantauan
        // ------------------------------------------------------------------
        $strength = min($tg['batas_t'], $kol['batas_t']);
        $risikoSedang = max($risikoSedang, $strength);

        // ------------------------------------------------------------------
        // R14: IF BMI Kurus AND Glukosa Normal
        //      THEN Risiko is Sehat
        // Kondisi: berat badan kurang tetapi gula darah normal
        // ------------------------------------------------------------------
        $strength = min($bmi['kurus'], $glu['normal']);
        $sehat = max($sehat, $strength);

        // ------------------------------------------------------------------
        // R15: IF Sistolik Pra-Hipertensi AND Kolesterol Batas Tinggi
        //      THEN Risiko is Risiko Sedang
        // Kondisi: dua faktor risiko ringan yang saling menguatkan
        // ------------------------------------------------------------------
        $strength = min($sis['pra_hiper'], $kol['batas_t']);
        $risikoSedang = max($risikoSedang, $strength);

        return [
            'sehat'         => $sehat,
            'risiko_sedang' => $risikoSedang,
            'risiko_tinggi' => $risikoTinggi,
        ];
    }

    // =========================================================================
    // STEP 3 — DEFUZZIFICATION  (Centroid / Center of Area)
    //
    // Universe output : [0, 100]
    // MF output sets  :
    //   Sehat         : trapesium kiri  (0 – 40)    → puncak di [0,20]
    //   Risiko Sedang : segitiga        (20 – 80)   → puncak di 50
    //   Risiko Tinggi : trapesium kanan (60 – 100)  → puncak di [80,100]
    //
    // Pendekatan: sampling diskrit pada 200 titik, hitung CoA.
    //   z* = Σ(z · μ_agregat(z)) / Σ(μ_agregat(z))
    // =========================================================================

    /**
     * Defuzzifikasi Centroid (CoA).
     *
     * @param  array  $ruleOutputs  Hasil applyRules() — kekuatan per output set
     * @return float  Skor crisp [0, 100]
     */
    public function defuzzify(array $ruleOutputs): float
    {
        $alphaSehat  = $ruleOutputs['sehat'];
        $alphaSedang = $ruleOutputs['risiko_sedang'];
        $alphaTinggi = $ruleOutputs['risiko_tinggi'];

        $steps  = 200;          // jumlah titik sampling pada universe
        $start  = 0.0;
        $end    = 100.0;
        $step   = ($end - $start) / $steps;

        $numerator   = 0.0;
        $denominator = 0.0;

        for ($i = 0; $i <= $steps; $i++) {
            $z = $start + ($i * $step);

            // MF output untuk setiap set (sebelum dipotong)
            $muSehat  = $this->trapezoidLeft($z, 20.0, 40.0);
            $muSedang = $this->triangle($z, 20.0, 50.0, 80.0);
            $muTinggi = $this->trapezoidRight($z, 60.0, 80.0);

            // Pemotongan (clipping) menggunakan kekuatan aturan — operator MIN
            $muSehat  = min($alphaSehat,  $muSehat);
            $muSedang = min($alphaSedang, $muSedang);
            $muTinggi = min($alphaTinggi, $muTinggi);

            // Agregasi — operator MAX
            $muAgregat = max($muSehat, $muSedang, $muTinggi);

            $numerator   += $z * $muAgregat;
            $denominator += $muAgregat;
        }

        // Hindari pembagian nol (terjadi jika semua aturan bernilai 0)
        if ($denominator == 0) {
            return 50.0; // default ke tengah universe
        }

        return round($numerator / $denominator, 4);
    }

    // =========================================================================
    // STEP 4 — GENERATE RECOMMENDATION
    // Pemetaan skor crisp → label linguistik + teks rekomendasi
    // =========================================================================

    /**
     * Hasilkan label risiko dan teks rekomendasi berdasarkan skor defuzzifikasi.
     *
     * Batas label (ditentukan berdasarkan titik tengah MF output):
     *   Sehat         : skor < 35
     *   Risiko Sedang : 35 ≤ skor < 65
     *   Risiko Tinggi : skor ≥ 65
     *
     * @param  float  $riskScore  Skor hasil defuzzifikasi [0, 100]
     * @return array  ['risk_label', 'rec_diet', 'rec_exercise', 'rec_notes']
     */
    public function generateRecommendation(float $riskScore): array
    {
        if ($riskScore < 35) {
            // ----------------------------------------------------------------
            // SEHAT — pertahankan pola hidup
            // ----------------------------------------------------------------
            return [
                'risk_label'   => 'Sehat',
                'rec_diet'     =>
                    "✅ Pola Makan yang Dianjurkan:\n" .
                    "• Konsumsi makanan seimbang: nasi/kentang, sayuran, buah, protein tanpa lemak\n" .
                    "• Perbanyak serat: sayuran hijau, buah-buahan, biji-bijian\n" .
                    "• Minum air putih minimal 8 gelas/hari\n" .
                    "• Batasi gula tambahan dan makanan olahan\n\n" .
                    "🚫 Makanan yang Dihindari:\n" .
                    "• Hindari konsumsi berlebih makanan tinggi garam (kerupuk, acar, makanan kaleng)\n" .
                    "• Kurangi minuman manis dan bersoda",

                'rec_exercise' =>
                    "🏃 Rekomendasi Olahraga: Aktivitas Ringan–Sedang\n" .
                    "• Jalan kaki cepat 30 menit, minimal 5x/minggu\n" .
                    "• Bersepeda santai atau berenang 2–3x/minggu\n" .
                    "• Peregangan (stretching) setiap hari untuk fleksibilitas",

                'rec_notes'    =>
                    'Status kesehatan Anda tergolong BAIK. Pertahankan pola makan sehat dan rutinitas olahraga. ' .
                    'Lakukan pemeriksaan MCU tahunan untuk pemantauan berkala.',
            ];
        }

        if ($riskScore < 65) {
            // ----------------------------------------------------------------
            // RISIKO SEDANG — perubahan gaya hidup, pantau lebih ketat
            // ----------------------------------------------------------------
            return [
                'risk_label'   => 'Risiko Sedang',
                'rec_diet'     =>
                    "✅ Pola Makan yang Dianjurkan:\n" .
                    "• Diet rendah karbohidrat sederhana: kurangi nasi putih, pilih nasi merah atau ubi\n" .
                    "• Tingkatkan konsumsi sayuran non-tepung: brokoli, bayam, wortel\n" .
                    "• Pilih protein tanpa lemak: ikan, ayam tanpa kulit, putih telur, tahu/tempe\n" .
                    "• Konsumsi lemak sehat: alpukat, ikan salmon, minyak zaitun (porsi wajar)\n" .
                    "• Makan dalam porsi lebih kecil tetapi lebih sering (4–5x/hari)\n\n" .
                    "🚫 Makanan yang Dihindari:\n" .
                    "• Jeroan, daging berlemak, kulit ayam\n" .
                    "• Gorengan, fast food, makanan berlemak jenuh\n" .
                    "• Minuman manis: jus kemasan, teh manis, minuman soda\n" .
                    "• Garam berlebih: saus instan, keripik, makanan kaleng",

                'rec_exercise' =>
                    "🏃 Rekomendasi Olahraga: Aktivitas Sedang\n" .
                    "• Jalan cepat atau jogging ringan 30–45 menit, 5x/minggu\n" .
                    "• Senam aerobik intensitas sedang 3x/minggu\n" .
                    "• Latihan kekuatan ringan (menggunakan beban tubuh) 2x/minggu\n" .
                    "• Hindari duduk/berbaring lebih dari 2 jam berturut-turut",

                'rec_notes'    =>
                    'Status kesehatan Anda berada pada kategori RISIKO SEDANG. Disarankan untuk segera ' .
                    'mengubah pola makan dan meningkatkan aktivitas fisik. Konsultasikan ke dokter ' .
                    'perusahaan untuk evaluasi lebih lanjut jika diperlukan.',
            ];
        }

        // --------------------------------------------------------------------
        // RISIKO TINGGI — intervensi segera, pantau intensif
        // --------------------------------------------------------------------
        return [
            'risk_label'   => 'Risiko Tinggi',
            'rec_diet'     =>
                "✅ Pola Makan yang Dianjurkan:\n" .
                "• Diet DASH atau diet mediterania: kaya sayuran, buah, biji-bijian, ikan\n" .
                "• Batasi karbohidrat: porsi nasi ≤ ½ piring, prioritaskan sayuran\n" .
                "• Protein tanpa lemak: ikan laut (salmon, tuna), tahu, tempe, kacang-kacangan\n" .
                "• Konsumsi makanan tinggi kalium: pisang, alpukat, kentang rebus\n" .
                "• Kurangi natrium: target < 1.500 mg/hari\n\n" .
                "🚫 Makanan yang Harus Dihindari:\n" .
                "• Semua makanan tinggi gula: kue, es krim, minuman manis\n" .
                "• Daging olahan: sosis, nugget, kornet, bacon\n" .
                "• Lemak trans & jenuh: margarin, minyak kelapa sawit berlebih, gorengan\n" .
                "• Alkohol dan minuman berenergi\n" .
                "• Makanan tinggi purin (jika asam urat tinggi): jeroan, kacang-kacangan berlebih, seafood",

            'rec_exercise' =>
                "🏃 Rekomendasi Olahraga: Aktivitas Ringan Terstruktur\n" .
                "• Mulai dengan jalan kaki 20–30 menit, 3–5x/minggu (intensitas ringan)\n" .
                "• Hindari olahraga berat atau kompetitif tanpa izin dokter\n" .
                "• Senam ringan atau yoga untuk relaksasi dan kontrol tekanan darah\n" .
                "• Lakukan pemanasan dan pendinginan setiap sesi olahraga\n" .
                "⚠️ Konsultasikan program olahraga dengan dokter sebelum memulai",

            'rec_notes'    =>
                'Status kesehatan Anda berada pada kategori RISIKO TINGGI. Segera konsultasikan ke ' .
                'dokter perusahaan atau dokter spesialis untuk penatalaksanaan lebih lanjut. ' .
                'Pemantauan tekanan darah, gula darah, dan kolesterol secara rutin sangat dianjurkan.',
        ];
    }

    // =========================================================================
    // CONVENIENCE METHOD — Jalankan seluruh pipeline sekaligus
    // =========================================================================

    /**
     * Jalankan pipeline lengkap: fuzzify → applyRules → defuzzify → recommend.
     *
     * @param  array   $inputs  Sama dengan parameter fuzzify()
     * @return array   Gabungan hasil semua tahap:
     *                 ['memberships', 'rule_outputs', 'risk_score',
     *                  'risk_label', 'rec_diet', 'rec_exercise', 'rec_notes']
     */
    public function process(array $inputs): array
    {
        // Tahap 1: Fuzzifikasi
        $memberships = $this->fuzzify($inputs);

        // Tahap 2: Evaluasi aturan
        $ruleOutputs = $this->applyRules($memberships);

        // Tahap 3: Defuzzifikasi Centroid
        $riskScore = $this->defuzzify($ruleOutputs);

        // Tahap 4: Pemetaan ke rekomendasi
        $recommendation = $this->generateRecommendation($riskScore);

        return array_merge(
            [
                'memberships'  => $memberships,
                'rule_outputs' => $ruleOutputs,
                'risk_score'   => $riskScore,
            ],
            $recommendation
        );
    }

    // =========================================================================
    // MEMBERSHIP FUNCTION PRIMITIVES
    // Fungsi bantu matematika untuk membangun MF trapesium dan segitiga.
    // =========================================================================

    /**
     * Fungsi keanggotaan segitiga (triangular MF).
     *
     *         1|       /\
     *          |      /  \
     *          |     /    \
     *        0 |____/      \____
     *               a   b   c
     *
     * @param  float  $x  Nilai crisp
     * @param  float  $a  Batas kiri (µ = 0)
     * @param  float  $b  Puncak (µ = 1)
     * @param  float  $c  Batas kanan (µ = 0)
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
     * Fungsi keanggotaan trapesium kiri / shoulder kiri (open-left).
     *
     *   1|________
     *    |        \
     *    |         \
     *   0|          \____
     *               a    b
     *
     * - Untuk x ≤ a: µ = 1.0
     * - Untuk a < x < b: turun linier
     * - Untuk x ≥ b: µ = 0.0
     *
     * @param  float  $a  Titik mulai turun
     * @param  float  $b  Titik MF = 0
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
     * Fungsi keanggotaan trapesium kanan / shoulder kanan (open-right).
     *
     *             ________
     *            /
     *           /
     *   ______ /
     *          a    b
     *
     * - Untuk x ≤ a: µ = 0.0
     * - Untuk a < x < b: naik linier
     * - Untuk x ≥ b: µ = 1.0
     *
     * @param  float  $a  Titik mulai naik
     * @param  float  $b  Titik MF = 1
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
