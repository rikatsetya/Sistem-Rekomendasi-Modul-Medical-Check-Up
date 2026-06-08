<?php

namespace Database\Seeders;

use App\Models\RecommendationRule;
use Illuminate\Database\Seeder;

class RecommendationRuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $ranges = [
            'ringan' => [0, 29],
            'sedang' => [30, 49],
            'tinggi' => [50, 69],
            'kritis' => [70, 100],
        ];

        $definitions = [

            'global' => [
                'ringan' => [
                    'diet' => [
                        'Edukasi gizi seimbang dan pola makan sehat.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik ringan seperti jalan santai minimal 30 menit/hari.',
                    ],
                    'note' => [
                        'Pantau pola makan dan aktivitas fisik secara berkala.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Defisit kalori 400–500 kkal/hari dari kebutuhan basal.',
                        'Komposisi makronutrien: karbohidrat kompleks 50–60%, protein 15–20%, lemak sehat 25–30%.',
                        'Asupan serat minimal 25 gram/hari dari sayur dan buah utuh.',
                    ],
                    'exercise' => [
                        'Aerobik intensitas sedang ±150 menit/minggu (jalan cepat, bersepeda ringan).',
                        'Latihan kekuatan 2x/minggu (squat, push-up, bodyweight).',
                    ],
                    'note' => [
                        'Pertimbangkan evaluasi progres setelah 1–3 bulan.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Pengaturan pola makan ketat dan terstruktur dengan pengawasan tenaga kesehatan.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik terkontrol dan disesuaikan kondisi medis.',
                    ],
                    'note' => [
                        'Disarankan pemantauan tenaga kesehatan dan evaluasi lanjutan.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Pengaturan pola makan sangat ketat dan terstruktur dengan pengawasan tenaga kesehatan.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik sangat terkontrol dan hanya sesuai anjuran medis.',
                    ],
                    'note' => [
                        'Risiko kesehatan tinggi. Disarankan evaluasi dan penanganan medis lanjutan.',
                    ],
                ],
            ],

            'obesitas_metabolik' => [
                'ringan' => [
                    'diet' => [
                        'Kurangi porsi makan berlebih dan batasi minuman manis.',
                    ],
                    'exercise' => [
                        'Jaga aktivitas harian dan lakukan jalan santai secara rutin.',
                    ],
                    'note' => [
                        'Pantau IMT dan lingkar perut secara berkala.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Fokus pada defisit kalori bertahap dan kontrol porsi makan.',
                        'Perbanyak konsumsi sayur, buah, dan sumber protein tanpa lemak.',
                    ],
                    'exercise' => [
                        'Aerobik rutin dan peningkatan aktivitas harian (NEAT).',
                    ],
                    'note' => [
                        'Pertimbangkan konsultasi ahli gizi untuk perencanaan makan.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Fokus pada defisit kalori bertahap dan kontrol porsi makan.',
                        'Batasi makanan tinggi kalori, lemak jenuh, dan gula sederhana.',
                    ],
                    'exercise' => [
                        'Aerobik rutin dan peningkatan aktivitas harian (NEAT).',
                    ],
                    'note' => [
                        'Evaluasi faktor risiko metabolik dan komorbid yang menyertai.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Pengaturan diet ketat dengan pemantauan tenaga kesehatan.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik ringan–sedang sesuai kondisi medis.',
                    ],
                    'note' => [
                        'Disarankan evaluasi medis lanjutan dan monitoring ketat.',
                    ],
                ],
            ],

            'diabetes' => [
                'ringan' => [
                    'diet' => [
                        'Batasi gula sederhana dan pilih karbohidrat kompleks.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik ringan seperti jalan kaki teratur.',
                    ],
                    'note' => [
                        'Monitoring glukosa darah secara berkala dianjurkan.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Batasi gula sederhana dan indeks glikemik tinggi.',
                        'Distribusi karbohidrat merata sepanjang hari.',
                    ],
                    'exercise' => [
                        'Aktivitas aerobik teratur untuk meningkatkan sensitivitas insulin.',
                    ],
                    'note' => [
                        'Pertimbangkan pemeriksaan HbA1c dan evaluasi pola makan.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Batasi gula sederhana dan indeks glikemik tinggi.',
                        'Distribusi karbohidrat merata sepanjang hari.',
                    ],
                    'exercise' => [
                        'Aktivitas aerobik teratur untuk meningkatkan sensitivitas insulin.',
                    ],
                    'note' => [
                        'Kontrol glukosa lebih sering dan evaluasi terapi lanjutan.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Pengaturan asupan karbohidrat secara ketat dan terstruktur.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik ringan sesuai anjuran medis.',
                    ],
                    'note' => [
                        'Rujukan dan pengawasan medis lanjutan diperlukan.',
                    ],
                ],
            ],

            'kardiovaskular' => [
                'ringan' => [
                    'diet' => [
                        'Batasi garam berlebih dan lemak jenuh.',
                    ],
                    'exercise' => [
                        'Jalan kaki atau aktivitas aerobik ringan secara rutin.',
                    ],
                    'note' => [
                        'Pantau tekanan darah dan profil lipid secara berkala.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Batasi lemak jenuh dan kolesterol.',
                        'Konsumsi lemak sehat seperti ikan, kacang-kacangan, dan minyak zaitun.',
                    ],
                    'exercise' => [
                        'Aerobik intensitas sedang secara teratur.',
                    ],
                    'note' => [
                        'Evaluasi faktor risiko kardiometabolik secara berkala.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Batasi lemak jenuh dan kolesterol.',
                        'Konsumsi lemak sehat (ikan, kacang-kacangan, minyak zaitun).',
                    ],
                    'exercise' => [
                        'Olahraga aerobik intensitas sedang dengan monitoring denyut nadi.',
                    ],
                    'note' => [
                        'Disarankan evaluasi tekanan darah dan fungsi kardiovaskular lebih lanjut.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Diet rendah garam dan rendah lemak jenuh di bawah pengawasan medis.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik sangat terkontrol dan disesuaikan kondisi klinis.',
                    ],
                    'note' => [
                        'Perlu evaluasi dokter segera dan penanganan lanjutan.',
                    ],
                ],
            ],

            'ginjal' => [
                'ringan' => [
                    'diet' => [
                        'Cukupi cairan dan hindari garam berlebih.',
                    ],
                    'exercise' => [
                        'Aktivitas ringan dan teratur.',
                    ],
                    'note' => [
                        'Pantau fungsi ginjal secara berkala.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Kontrol asupan protein dan natrium.',
                        'Hindari makanan olahan dan tinggi garam.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik ringan–sedang, hindari dehidrasi.',
                    ],
                    'note' => [
                        'Pertimbangkan pemeriksaan ureum, kreatinin, dan eGFR.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Perhatikan asupan protein agar tidak berlebihan.',
                        'Hindari konsumsi garam berlebih dan makanan olahan.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik ringan–sedang, hindari dehidrasi.',
                    ],
                    'note' => [
                        'Disarankan konsultasi lanjutan bila fungsi ginjal menurun.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Pengaturan diet ketat sesuai anjuran medis.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik sangat ringan dan terkontrol.',
                    ],
                    'note' => [
                        'Perlu penanganan lanjutan oleh tenaga kesehatan.',
                    ],
                ],
            ],

            'hati' => [
                'ringan' => [
                    'diet' => [
                        'Kurangi gorengan, makanan tinggi lemak, dan alkohol.',
                    ],
                    'exercise' => [
                        'Aktivitas ringan secara rutin.',
                    ],
                    'note' => [
                        'Pantau fungsi hati secara berkala.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Perbanyak sayur, buah, dan makanan tinggi antioksidan.',
                        'Kurangi konsumsi makanan tinggi lemak jenuh dan gula berlebih.',
                    ],
                    'exercise' => [
                        'Aktivitas aerobik ringan–sedang secara teratur.',
                    ],
                    'note' => [
                        'Evaluasi pola konsumsi dan fungsi hati secara berkala.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Hindari lemak jenuh, gorengan, dan alkohol.',
                        'Perbanyak sayur, buah, dan makanan tinggi antioksidan.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik terkontrol sesuai kondisi tubuh.',
                    ],
                    'note' => [
                        'Disarankan konsultasi lanjutan bila keluhan menetap.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Diet ketat dan terstruktur dengan pengawasan tenaga kesehatan.',
                    ],
                    'exercise' => [
                        'Aktivitas ringan sesuai toleransi tubuh.',
                    ],
                    'note' => [
                        'Perlu evaluasi dan penanganan spesialis.',
                    ],
                ],
            ],

            'hiperurisemia' => [
                'ringan' => [
                    'diet' => [
                        'Batasi makanan tinggi purin dan cukupkan air putih.',
                    ],
                    'exercise' => [
                        'Aktivitas ringan dan rutin.',
                    ],
                    'note' => [
                        'Pantau kadar asam urat secara berkala.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Kurangi jeroan, seafood tertentu, dan minuman tinggi fruktosa.',
                        'Perbanyak konsumsi air putih.',
                    ],
                    'exercise' => [
                        'Aktivitas aerobik ringan–sedang.',
                    ],
                    'note' => [
                        'Pertimbangkan evaluasi bila nyeri sendi muncul berulang.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Batasi makanan tinggi purin (jeroan, seafood tertentu).',
                        'Perbanyak konsumsi air putih.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik terkontrol dan hindari dehidrasi.',
                    ],
                    'note' => [
                        'Konsultasi medis disarankan bila keluhan nyeri sendi berulang.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Diet rendah purin yang ketat dan terstruktur.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik ringan sesuai kondisi.',
                    ],
                    'note' => [
                        'Perlu pengawasan dan tindak lanjut medis.',
                    ],
                ],
            ],

            'hemodinamik' => [
                'ringan' => [
                    'diet' => [
                        'Batasi garam berlebih dan pertahankan hidrasi.',
                    ],
                    'exercise' => [
                        'Aktivitas ringan dengan pemantauan keluhan.',
                    ],
                    'note' => [
                        'Pantau tekanan darah dan denyut nadi secara berkala.',
                    ],
                ],
                'sedang' => [
                    'diet' => [
                        'Konsumsi makanan seimbang dan cukup cairan.',
                    ],
                    'exercise' => [
                        'Aktivitas fisik stabil dan terkontrol.',
                    ],
                    'note' => [
                        'Hindari aktivitas mendadak yang memicu gejala.',
                    ],
                ],
                'tinggi' => [
                    'diet' => [
                        'Batasi garam, stimulan, dan pola makan tidak teratur.',
                    ],
                    'exercise' => [
                        'Hindari olahraga intensitas tinggi tanpa pengawasan.',
                        'Pilih aktivitas fisik stabil dan terkontrol.',
                    ],
                    'note' => [
                        'Monitoring parameter hemodinamik diperlukan.',
                    ],
                ],
                'kritis' => [
                    'diet' => [
                        'Diet sesuai instruksi medis dan monitoring ketat.',
                    ],
                    'exercise' => [
                        'Hindari olahraga intensif tanpa pengawasan.',
                        'Pilih aktivitas fisik stabil dan terkontrol.',
                    ],
                    'note' => [
                        'Evaluasi khusus dan pengawasan tenaga kesehatan wajib dilakukan.',
                    ],
                ],
            ],
        ];

        $rows = [];

        foreach ($definitions as $groupCode => $levels) {
            foreach ($levels as $severity => $categories) {
                [$minScore, $maxScore] = $ranges[$severity];

                foreach ($categories as $category => $texts) {
                    foreach ($texts as $text) {
                        $rows[] = [
                            'group_code' => $groupCode,
                            'category' => $category,
                            'severity_level' => $severity,
                            'min_score' => $minScore,
                            'max_score' => $maxScore,
                            'recommendation_text' => $text,
                            'is_active' => true,
                            'created_by' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            RecommendationRule::insert($chunk);
        }
    }
}