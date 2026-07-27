<?php

namespace Database\Seeders;

use App\Models\RecommendationRule;
use Illuminate\Database\Seeder;

/**
 * RecommendationRuleSeeder
 *
 * Meng-seed tabel recommendation_rules dengan aturan rekomendasi klinis
 * untuk sistem Fuzzy Mamdani Hierarkis 7 Kelompok.
 *
 * Setiap kelompok memiliki 4 severity level yang dipetakan dari skor (0–100):
 *   ringan : 0  – 29
 *   sedang : 30 – 49
 *   tinggi : 50 – 69
 *   kritis : 70 – 100
 *
 * 7 Group Code:
 *   1. fungsi_hati    — GOT, GPT
 *   2. diabetes       — Glukosa Puasa, Glukosa 2j PP, HbA1c
 *   3. profil_lipid   — Chol.Total, LDL, HDL, Trigliserida, Apo-B
 *   4. fungsi_ginjal  — Urea N, Ureum, Kreatinin, eLFG
 *   5. asam_urat      — Asam Urat
 *   6. kardiovaskular — Nadi, Pernafasan, Sistolik, Diastolik
 *   7. antropometri   — Tinggi Badan, Berat Badan, IMT, Lingkar Perut
 */
class RecommendationRuleSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama sebelum seeding ulang
        RecommendationRule::truncate();

        $now = now();

        // Rentang skor per severity level
        $ranges = [
            'ringan' => [0,  29],
            'sedang' => [30, 49],
            'tinggi' => [50, 69],
            'kritis' => [70, 100],
        ];

        // =====================================================================
        // DEFINISI REKOMENDASI — 7 KELOMPOK × 4 SEVERITY × 3 KATEGORI
        // =====================================================================
        $definitions = [

            // =================================================================
            // KELOMPOK 1: FUNGSI HATI (GOT / GPT)
            // =================================================================
            'fungsi_hati' => [

                'ringan' => [
                    'pola makan' => [
                        'Kurangi gorengan, makanan berlemak tinggi, dan alkohol; perbanyak sayur, buah, serta protein tanpa lemak.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik ringan rutin seperti jalan kaki ±30 menit per hari; hindari olahraga berlebihan.',
                    ],
                    'catatan' => [
                        'Pantau GOT dan GPT setiap ±6 bulan dan hindari obat atau suplemen tanpa anjuran dokter.',
                    ],
                ],

                'sedang' => [
                    'pola makan' => [
                        'Hentikan alkohol dan batasi lemak jenuh, gula tambahan, serta makanan olahan; tingkatkan sayur dan biji-bijian utuh.',
                    ],
                    'olahraga' => [
                        'Aerobik intensitas ringan–sedang 3–4 kali per minggu; hindari latihan berat hingga enzim hati membaik.',
                    ],
                    'catatan' => [
                        'Evaluasi GOT/GPT setiap 3 bulan dan waspadai gejala klinis seperti ikterus atau nyeri perut kanan atas.',
                    ],
                ],

                'tinggi' => [
                    'pola makan' => [
                        'Ikuti diet rendah lemak dan rendah garam khusus fungsi hati sesuai anjuran medis.',
                        'Hindari total alkohol, obat hepatotoksik, dan makanan cepat saji.',
                    ],
                    'olahraga' => [
                        'Batasi aktivitas fisik berat; pilih latihan ringan sesuai toleransi dan persetujuan dokter.',
                    ],
                    'catatan' => [
                        'Disarankan konsultasi spesialis dan pemeriksaan lanjutan seperti USG abdomen dan profil hepatitis.',
                    ],
                ],

                'kritis' => [
                    'pola makan' => [
                        'Diet terapeutik ketat di bawah supervisi dokter dan dietisien; pembatasan protein mungkin diperlukan.',
                        'Hindari seluruh makanan hepatotoksik dan suplemen tanpa rekomendasi medis.',
                    ],
                    'olahraga' => [
                        'Istirahat atau aktivitas sangat ringan hanya sesuai instruksi dokter.',
                    ],
                    'catatan' => [
                        'Kondisi darurat medis dengan risiko gagal hati akut; rawat inap dan monitoring ketat diperlukan.',
                        'Evaluasi segera oleh dokter spesialis hepatologi atau penyakit dalam.',
                    ],
                ],

            ],

            // =================================================================
            // KELOMPOK 2: DIABETES / GULA DARAH
            // =================================================================
            'diabetes' => [

                'ringan' => [
                    'pola makan' => [
                        'Batasi gula sederhana dan pilih karbohidrat kompleks berserat tinggi dengan indeks glikemik rendah.',
                    ],
                    'olahraga' => [
                        'Aktivitas aerobik ringan–sedang seperti jalan kaki 30 menit setelah makan atau bersepeda rutin.',
                    ],
                    'catatan' => [
                        'Pantau gula darah puasa secara berkala dan jaga berat badan ideal untuk mencegah progresi.',
                    ],
                ],

                'sedang' => [
                    'pola makan' => [
                        'Atur distribusi karbohidrat harian secara merata, batasi gula tersembunyi, dan pilih sumber kompleks.',
                    ],
                    'olahraga' => [
                        'Aerobik intensitas sedang ±150 menit per minggu, ditambah latihan resistensi 2–3 kali per minggu.',
                    ],
                    'catatan' => [
                        'Evaluasi HbA1c setiap 3–6 bulan dan diskusikan terapi lanjutan bila kontrol glikemik belum tercapai.',
                    ],
                ],

                'tinggi' => [
                    'pola makan' => [
                        'Terapkan diet diabetes terstruktur dengan pembatasan karbohidrat ketat di bawah pengawasan ahli gizi.',
                        'Hindari seluruh makanan dan minuman manis secara konsisten.',
                    ],
                    'olahraga' => [
                        'Latihan fisik terstruktur dengan pemantauan gula darah sebelum dan sesudah olahraga.',
                    ],
                    'catatan' => [
                        'Konsultasi dokter untuk evaluasi terapi antidiabetik dan skrining faktor risiko komplikasi.',
                    ],
                ],

                'kritis' => [
                    'pola makan' => [
                        'Diet ketat harus disupervisi medis; waspadai dan cegah hipoglikemia terutama pada terapi insulin.',
                        'Sediakan selalu sumber gula cepat untuk kondisi darurat.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik hanya boleh dilakukan setelah kadar gula stabil dan atas izin dokter.',
                    ],
                    'catatan' => [
                        'Kondisi darurat dengan risiko komplikasi akut; diperlukan pemantauan gula darah intensif dan evaluasi komplikasi menyeluruh.',
                        'Penanganan medis segera sangat dianjurkan.',
                    ],
                ],

            ],

            // =================================================================
            // KELOMPOK 3: PROFIL LIPID
            // =================================================================
            'profil_lipid' => [

                'ringan' => [
                    'pola makan' => [
                        'Batasi lemak jenuh dan lemak trans, serta perbanyak lemak tak jenuh dan serat larut untuk menurunkan LDL.',
                    ],
                    'olahraga' => [
                        'Aerobik ringan–sedang seperti jalan cepat atau bersepeda minimal 30 menit, 5 hari per minggu.',
                    ],
                    'catatan' => [
                        'Pantau profil lipid lengkap secara berkala dan hindari merokok karena menurunkan HDL.',
                    ],
                ],

                'sedang' => [
                    'pola makan' => [
                        'Terapkan pola diet Mediterania dengan pembatasan lemak trans, daging merah, karbohidrat rafinasi, dan alkohol.',
                    ],
                    'olahraga' => [
                        'Aerobik intensitas sedang 150–300 menit per minggu disertai latihan kekuatan 2 kali per minggu.',
                    ],
                    'catatan' => [
                        'Evaluasi profil lipid setiap 3 bulan dan pertimbangkan terapi obat bila target tidak tercapai.',
                    ],
                ],

                'tinggi' => [
                    'pola makan' => [
                        'Diet rendah lemak jenuh dan kolesterol harus dijalankan secara ketat di bawah pengawasan ahli gizi.',
                        'Hindari total lemak trans dan batasi sangat ketat asupan lemak jenuh.',
                    ],
                    'olahraga' => [
                        'Program olahraga terstruktur dan terpantau secara medis untuk menurunkan risiko kardiovaskular.',
                    ],
                    'catatan' => [
                        'Diperlukan evaluasi risiko kardiovaskular menyeluruh dan kemungkinan terapi farmakologis.',
                    ],
                ],

                'kritis' => [
                    'pola makan' => [
                        'Diet terapeutik ketat wajib disusun dan diawasi oleh dokter serta dietisien klinis.',
                        'Respons diet harus dipantau ketat melalui pemeriksaan profil lipid berkala.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik hanya boleh dilakukan atas rekomendasi dan pemantauan dokter.',
                    ],
                    'catatan' => [
                        'Risiko kejadian kardiovaskular sangat tinggi sehingga diperlukan penanganan medis segera dan intensif.',
                    ],
                ],

            ],

            // =================================================================
            // KELOMPOK 4: FUNGSI GINJAL
            // =================================================================
            'fungsi_ginjal' => [

                'ringan' => [
                    'pola makan' => [
                        'Cukupi asupan cairan dan batasi garam untuk menjaga fungsi filtrasi dan tekanan darah.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik ringan–sedang secara teratur dengan menjaga hidrasi yang cukup.',
                    ],
                    'catatan' => [
                        'Pantau ureum, kreatinin, dan eLFG secara berkala serta hindari penggunaan NSAID jangka panjang.',
                    ],
                ],

                'sedang' => [
                    'pola makan' => [
                        'Kontrol asupan protein serta batasi kalium dan fosfor sesuai kondisi fungsi ginjal.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik berdampak rendah seperti jalan santai atau yoga, hindari dehidrasi.',
                    ],
                    'catatan' => [
                        'Evaluasi fungsi ginjal dan tekanan darah setiap 3 bulan untuk mencegah progresivitas CKD.',
                    ],
                ],

                'tinggi' => [
                    'pola makan' => [
                        'Diet ginjal terstruktur dengan pembatasan protein, natrium, kalium, dan fosfor secara ketat.',
                        'Pemantauan asupan cairan diperlukan bila muncul edema atau penurunan urin.',
                    ],
                    'olahraga' => [
                        'Aktivitas sangat ringan dan hanya sesuai rekomendasi dokter.',
                    ],
                    'catatan' => [
                        'Diperlukan konsultasi spesialis nefrologi dan pemeriksaan lanjutan untuk penanganan komprehensif.',
                    ],
                ],

                'kritis' => [
                    'pola makan' => [
                        'Diet terapeutik ginjal ketat wajib berada di bawah supervisi dokter dan dietisien klinis.',
                        'Pembatasan cairan harus mengikuti instruksi medis secara ketat.',
                    ],
                    'olahraga' => [
                        'Istirahat dominan; aktivitas fisik hanya jika diizinkan dokter.',
                    ],
                    'catatan' => [
                        'Kondisi kritis — kemungkinan memerlukan terapi pengganti ginjal dan monitoring intensif.',
                    ],
                ],

            ],

            // =================================================================
            // KELOMPOK 5: ASAM URAT
            // =================================================================
            'asam_urat' => [

                'ringan' => [
                    'pola makan' => [
                        'Batasi makanan tinggi purin dan perbanyak konsumsi air putih untuk membantu ekskresi asam urat.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik ringan dan rutin dengan menjaga berat badan ideal.',
                    ],
                    'catatan' => [
                        'Pantau kadar asam urat secara berkala dan perhatikan munculnya nyeri sendi.',
                    ],
                ],

                'sedang' => [
                    'pola makan' => [
                        'Kurangi konsumsi jeroan, daging merah, seafood tinggi purin, dan alkohol; pilih sumber protein nabati.',
                    ],
                    'olahraga' => [
                        'Aktivitas aerobik ringan–sedang dan peregangan untuk menjaga mobilitas sendi.',
                    ],
                    'catatan' => [
                        'Evaluasi kadar asam urat setiap 3 bulan dan waspadai tanda serangan gout akut.',
                    ],
                ],

                'tinggi' => [
                    'pola makan' => [
                        'Terapkan diet rendah purin ketat dan hindari alkohol secara total.',
                        'Konsumsi produk susu rendah lemak dan perbanyak sayur-buah.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik berdampak rendah; istirahatkan sendi saat terjadi serangan gout.',
                    ],
                    'catatan' => [
                        'Diperlukan evaluasi dokter untuk terapi farmakologis dan pemeriksaan komplikasi.',
                    ],
                ],

                'kritis' => [
                    'pola makan' => [
                        'Diet rendah purin eksklusif harus berada di bawah supervisi dokter dan dietisien klinis.',
                        'Hindari seluruh sumber purin tinggi selama fase kritis.',
                    ],
                    'olahraga' => [
                        'Istirahat total pada sendi yang terkena; aktivitas hanya jika diizinkan dokter.',
                    ],
                    'catatan' => [
                        'Kondisi kritis — risiko komplikasi gout kronis tinggi dan memerlukan penanganan spesialis.',
                    ],
                ],

            ],

            // =================================================================
            // KELOMPOK 6: KARDIOVASKULAR & TANDA VITAL
            // =================================================================
            'kardiovaskular' => [

                'ringan' => [
                    'pola makan' => [
                        'Batasi asupan natrium dan terapkan pola makan DASH untuk membantu mengontrol tekanan darah.',
                    ],
                    'olahraga' => [
                        'Lakukan aerobik ringan secara rutin seperti jalan kaki atau bersepeda 30 menit per hari.',
                    ],
                    'catatan' => [
                        'Pantau tekanan darah dan denyut nadi secara berkala serta hindari merokok dan kafein berlebih.',
                    ],
                ],

                'sedang' => [
                    'pola makan' => [
                        'Terapkan diet DASH rendah natrium dan lemak jenuh secara konsisten.',
                    ],
                    'olahraga' => [
                        'Aerobik intensitas sedang ±150 menit per minggu dengan pemantauan tekanan darah.',
                    ],
                    'catatan' => [
                        'Target tekanan darah < 130/80 mmHg dan pertimbangkan evaluasi medis bila tidak tercapai.',
                    ],
                ],

                'tinggi' => [
                    'pola makan' => [
                        'Diet kardioprotektif ketat rendah natrium dan lemak jenuh di bawah pengawasan ahli gizi.',
                        'Konsumsi ikan berlemak sumber omega-3 secara rutin.',
                    ],
                    'olahraga' => [
                        'Olahraga aerobik bertahap dengan monitoring denyut nadi dan tekanan darah.',
                    ],
                    'catatan' => [
                        'Disarankan evaluasi dokter spesialis jantung dan kemungkinan terapi antihipertensi.',
                    ],
                ],

                'kritis' => [
                    'pola makan' => [
                        'Diet kardiovaskular terapeutik eksklusif dengan pembatasan natrium sangat ketat.',
                        'Seluruh pola makan harus mengikuti panduan tim medis.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik hanya boleh dilakukan setelah mendapat izin dan pengawasan dokter.',
                    ],
                    'catatan' => [
                        'Kondisi kritis dengan risiko tinggi — diperlukan evaluasi dan monitoring intensif oleh kardiolog.',
                    ],
                ],

            ],

            // =================================================================
            // KELOMPOK 7: ANTROPOMETRI & OBESITAS
            // =================================================================
            'antropometri' => [

                'ringan' => [
                    'pola makan' => [
                        'Terapkan pola makan gizi seimbang dengan pengendalian porsi untuk menjaga berat badan dan lingkar perut ideal.',
                    ],
                    'olahraga' => [
                        'Tetap aktif secara fisik minimal 30 menit per hari melalui aktivitas ringan seperti jalan kaki.',
                    ],
                    'catatan' => [
                        'Pantau IMT dan lingkar perut secara berkala untuk mencegah kenaikan berat badan.',
                    ],
                ],

                'sedang' => [
                    'pola makan' => [
                        'Lakukan defisit kalori bertahap dengan memilih makanan tinggi serat dan rendah energi.',
                    ],
                    'olahraga' => [
                        'Aerobik intensitas sedang disertai latihan kekuatan ringan secara rutin.',
                    ],
                    'catatan' => [
                        'Evaluasi progres penurunan berat badan secara berkala dan sesuaikan target secara realistis.',
                    ],
                ],

                'tinggi' => [
                    'pola makan' => [
                        'Program penurunan berat badan terstruktur dengan defisit kalori yang diawasi ahli gizi.',
                        'Batasi ketat makanan ultra-proses dan minuman berkalori.',
                    ],
                    'olahraga' => [
                        'Program olahraga terstruktur kombinasi kardio dan kekuatan secara bertahap.',
                    ],
                    'catatan' => [
                        'Evaluasi faktor risiko metabolik dan pertimbangkan terapi tambahan bila diperlukan.',
                    ],
                ],

                'kritis' => [
                    'pola makan' => [
                        'Program diet ketat hanya boleh dilakukan di bawah supervisi dokter spesialis.',
                        'Pertimbangkan meal replacement atau VLCD secara medis.',
                    ],
                    'olahraga' => [
                        'Aktivitas fisik hanya dilakukan dengan pengawasan tenaga medis atau fisioterapis.',
                    ],
                    'catatan' => [
                        'Obesitas berat dengan risiko tinggi — diperlukan penanganan multidisiplin dan evaluasi lanjutan.',
                    ],
                ],

            ],
        ];

        // =====================================================================
        // BUILD ROWS & INSERT
        // =====================================================================
        $rows = [];

        foreach ($definitions as $groupCode => $levels) {
            foreach ($levels as $severity => $categories) {
                [$minScore, $maxScore] = $ranges[$severity];

                foreach ($categories as $category => $texts) {
                    foreach ($texts as $text) {
                        $rows[] = [
                            'group_code'         => $groupCode,
                            'category'           => $category,
                            'severity_level'     => $severity,
                            'min_score'          => $minScore,
                            'max_score'          => $maxScore,
                            'recommendation_text' => $text,
                            'is_active'          => true,
                            'created_by'         => null,
                            'created_at'         => $now,
                            'updated_at'         => $now,
                        ];
                    }
                }
            }
        }

        // Insert dalam chunks untuk menghindari batas query
        foreach (array_chunk($rows, 500) as $chunk) {
            RecommendationRule::insert($chunk);
        }
    }
}
