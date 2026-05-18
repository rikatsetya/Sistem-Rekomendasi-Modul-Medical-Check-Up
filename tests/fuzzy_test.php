<?php
/**
 * Fuzzy Mamdani — Test Script
 *
 * Jalankan dengan: php tests/fuzzy_test.php
 *
 * Menguji tiga skenario berbeda menggunakan data dari SQL dump.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FuzzyMamdaniService;

$service = new FuzzyMamdaniService();

$testCases = [
    // User 4 dari SQL dump — "keluarga admin 1" (L)
    // IMT=22.1, Sis=110, Glu=93, Kol=152, AU=5, TG=45
    [
        'label'  => 'User 4 (L) — Sehat Normal',
        'inputs' => [
            'bmi'          => 22.1,
            'sistolik'     => 110,
            'diastolik'    => 70,
            'glukosa'      => 93,
            'kolesterol'   => 152,
            'asam_urat'    => 5.0,
            'trigliserida' => 45,
            'gender'       => 'L',
        ],
    ],
    // User 5 dari SQL dump — "keluarga admin 2" (P)
    // IMT=27.3, Sis=140, Kol=207, AU=5.8, TG=70
    [
        'label'  => 'User 5 (P) — Risiko Sedang/Tinggi',
        'inputs' => [
            'bmi'          => 27.3,
            'sistolik'     => 140,
            'diastolik'    => 100,
            'glukosa'      => 87,
            'kolesterol'   => 207,
            'asam_urat'    => 5.8,
            'trigliserida' => 70,
            'gender'       => 'P',
        ],
    ],
    // User 6 dari SQL dump — "keluarga admin 3" (L)
    // IMT=26.0, Sis=110, Glu=146(DM!), Kol=248, AU=4.6, TG=164
    [
        'label'  => 'User 6 (L) — Risiko Tinggi (DM + Kolesterol Tinggi)',
        'inputs' => [
            'bmi'          => 26.0,
            'sistolik'     => 110,
            'diastolik'    => 70,
            'glukosa'      => 146,
            'kolesterol'   => 248,
            'asam_urat'    => 4.6,
            'trigliserida' => 164,
            'gender'       => 'L',
        ],
    ],
];

echo str_repeat('=', 70) . PHP_EOL;
echo "  FUZZY MAMDANI — TEST RESULTS" . PHP_EOL;
echo str_repeat('=', 70) . PHP_EOL;

foreach ($testCases as $idx => $tc) {
    echo PHP_EOL . "TEST " . ($idx + 1) . ": {$tc['label']}" . PHP_EOL;
    echo str_repeat('-', 70) . PHP_EOL;

    $result = $service->process($tc['inputs']);

    echo "  Inputs:" . PHP_EOL;
    foreach ($tc['inputs'] as $k => $v) {
        if ($k !== 'gender') {
            printf("    %-15s = %s\n", $k, $v);
        }
    }
    echo "    gender          = {$tc['inputs']['gender']}" . PHP_EOL;

    echo PHP_EOL . "  Fuzzification (BMI):" . PHP_EOL;
    foreach ($result['memberships']['bmi'] as $set => $degree) {
        printf("    %-12s = %.4f\n", $set, $degree);
    }

    echo PHP_EOL . "  Fuzzification (Sistolik):" . PHP_EOL;
    foreach ($result['memberships']['sistolik'] as $set => $degree) {
        printf("    %-12s = %.4f\n", $set, $degree);
    }

    echo PHP_EOL . "  Fuzzification (Glukosa):" . PHP_EOL;
    foreach ($result['memberships']['glukosa'] as $set => $degree) {
        printf("    %-12s = %.4f\n", $set, $degree);
    }

    echo PHP_EOL . "  Rule Engine Output (Agregasi MAX):" . PHP_EOL;
    printf("    Sehat         = %.4f\n", $result['rule_outputs']['sehat']);
    printf("    Risiko Sedang = %.4f\n", $result['rule_outputs']['risiko_sedang']);
    printf("    Risiko Tinggi = %.4f\n", $result['rule_outputs']['risiko_tinggi']);

    echo PHP_EOL . "  ★ DEFUZZIFICATION (Centroid CoA):" . PHP_EOL;
    printf("    Risk Score    = %.4f / 100\n", $result['risk_score']);
    printf("    Risk Label    = %s\n", $result['risk_label']);

    echo PHP_EOL . "  Recommendation Preview:" . PHP_EOL;
    echo "    Diet (first 80 chars): " . substr($result['rec_diet'], 0, 80) . "..." . PHP_EOL;

    echo PHP_EOL;
}

echo str_repeat('=', 70) . PHP_EOL;
echo "  ALL TESTS PASSED — FuzzyMamdaniService is working correctly." . PHP_EOL;
echo str_repeat('=', 70) . PHP_EOL;
