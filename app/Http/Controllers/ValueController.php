<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\User;
use App\Models\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class ValueController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-any', Value::class);

        $user = User::pluck('name', 'id');
        $userId = 1;

        if ($request->has('user_id')) {
            try {
                $userId = decrypt($request->user_id);
            } catch (\Exception $e) {
                abort(403, 'Invalid user ID');
            }
        }

        $subCategory = SubCategory::with([
            'category',
            'values' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])->get();

        return view('app.value.index', compact('subCategory', 'user', 'userId'));
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'tahun' => 'required|string'
        ]);

        $file = $request->file('file');

        try {
            DB::beginTransaction();

            //Load
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            $rows = array_slice($rows, 1, null, true);
            // Get Headers
            $headers = array_shift($rows);

            // Map header names to sub_category_id
            $subCategoryIds = [];
            $unmatchedHeaders = [];

            foreach ($headers as $header) {
                if (!$header) continue;

                // Clean and lowercase the header for comparison
                $cleanHeader = strtolower(trim($header));

                // Skip known non-value headers
                if (in_array($cleanHeader, ['no', 'kopeg', 'name', 'gender', 'dob'])) {
                    continue;
                }

                // Find SubCategory by exact name (case-insensitive)
                $subCategory = SubCategory::whereRaw('LOWER(TRIM(name)) = ?', [$cleanHeader])->first();

                if ($subCategory) {
                    $subCategoryIds[$header] = $subCategory->id;
                } else {
                    $unmatchedHeaders[] = $header; // Keep original header for display
                }
            }

            // If there are unmatched headers, throw an exception to rollback
            if (count($unmatchedHeaders) > 0) {
                $msg = count($unmatchedHeaders) . " column header(s) don’t match: " . implode(', ', $unmatchedHeaders) . ". Please check your Excel or your available SubCategory records.";
                throw new \Exception($msg);
            }
            foreach ($rows as $index => $row) {
                // normalisasi user
                $kopeg = trim($row['B'] ?? '');
                $name   = trim($row['C'] ?? '');
                $gender = strtolower(trim($row['D'] ?? ''));

                if (!$name || !$gender || !$kopeg) {
                    continue; // skip incomplete user
                }

                $user = User::firstOrCreate(
                    [
                        'kopeg'  => $kopeg,
                        'name'   => $name,
                        'gender' => $gender,
                    ],
                    [
                        'peran' => 2
                    ]
                );

                foreach ($headers as $col => $header) {
                    $nilai = $row[$col] ?? null;

                    if (in_array($header, ['no', 'kopeg', 'name', 'gender', 'dob'])) {
                        continue;
                    }

                    if (!isset($subCategoryIds[$header])) {
                        continue; // skip kalau gak ketemu id
                    }

                    Value::updateOrCreate(
                        [
                            'user_id'         => $user->id,
                            'sub_category_id' => $subCategoryIds[$header],
                            'tahun'           => $request->tahun,
                        ],
                        [
                            'nilai' => $nilai,
                        ]
                    );
                }
            }

            DB::commit();
            return back()->with('success', 'Data imported successfully!');
        } catch (ReaderException $e) {
            DB::rollBack();
            Log::error('Spreadsheet reader error: ' . $e->getMessage());
            return back()->with('error', 'Error reading Excel file.');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            DB::rollBack();
            Log::error('PhpSpreadsheet error: ' . $e->getMessage());
            return back()->with('error', 'Spreadsheet processing error.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Database query error: ' . $e->getMessage());
            return back()->with('error', 'Database error occurred while importing.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('General error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function annualTandaVital($id)
    {
        $userId = User::find(decrypt($id))->id;
        try {
            $tandaVital = Value::with(['subcategory.category'])
                ->where('user_id', $userId)
                ->whereHas('subcategory.category', function ($q) {
                    $q->where('name', 'Annual Tanda Vital');
                })
                ->get();

            if ($tandaVital->isEmpty()) {
                return back()->with('error', 'Tidak ditemukan riwayat data kesehatan pada server.');
            }

            $grouped = $tandaVital->groupBy('tahun')->map(function ($items) {
                return [
                    'tahun' => $items->first()->tahun,
                    'sistolik' => round(optional($items->firstWhere('subcategory.name', 'Tekanan darah Sistolik (mmHg)'))->nilai, 1),
                    'diastolik' => round(optional($items->firstWhere('subcategory.name', 'Tekanan darah Diastolik (mmHg)'))->nilai, 1),
                    'bmi' => round(optional($items->firstWhere('subcategory.name', 'IMT (kg/m2)'))->nilai, 1),
                    'nadi' => round(optional($items->firstWhere('subcategory.name', 'Nadi (kali/menit)'))->nilai, 1),
                    'pernafasan' => round(optional($items->firstWhere('subcategory.name', 'Pernafasan (kali/menit)'))->nilai, 1),
                    'tinggi' => round(optional($items->firstWhere('subcategory.name', 'Tinggi Badan (cm)'))->nilai, 1),
                    'berat' => round(optional($items->firstWhere('subcategory.name', 'Berat Badan (kg)'))->nilai, 1),
                    'lingkar_perut' => round(optional($items->firstWhere('subcategory.name', 'Lingkar Perut (cm)'))->nilai, 1),
                ];
            })->values();

            return view('app.statistic.annual-tanda-vital', [
                'tandaVital' => $grouped
            ]);

            // return response()->json($tandaVital);
        } catch (\Throwable $th) {
            Log::error('General error: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
    }

    public function komponenDarah($id)
    {
        $userId = User::find(decrypt($id))->id;
        try {
            $tandaVital = Value::with(['subcategory.category'])
                ->where('user_id', $userId)
                ->whereHas('subcategory.category', function ($q) {
                    $q->where('name', 'Komponen Darah Lengkap');
                })
                ->get();

            if ($tandaVital->isEmpty()) {
                return back()->with('error', 'Tidak ditemukan riwayat data kesehatan pada server.');
            }

            $grouped = $tandaVital->groupBy('tahun')->map(function ($items) {
                return [
                    'tahun' => $items->first()->tahun,
                    'hemoglobin'   => round(optional($items->firstWhere('subcategory.name', 'Hemoglobin'))->nilai, 1),
                    'hematokrit'   => round(optional($items->firstWhere('subcategory.name', 'Hematokrit'))->nilai, 1),
                    'eritrosit'    => round(optional($items->firstWhere('subcategory.name', 'Eritrosit (Hematologi)'))->nilai, 1),
                    'mcv'          => round(optional($items->firstWhere('subcategory.name', 'MCV'))->nilai, 1),
                    'mch'          => round(optional($items->firstWhere('subcategory.name', 'MCH'))->nilai, 1),
                    'mchc'         => round(optional($items->firstWhere('subcategory.name', 'MCHC'))->nilai, 1),
                    'trombosit'    => round(optional($items->firstWhere('subcategory.name', 'Trombosit'))->nilai, 1),
                    'leukosit'     => round(optional($items->firstWhere('subcategory.name', 'Leukosit (Hematologi)'))->nilai, 1),
                    'basofil'      => round(optional($items->firstWhere('subcategory.name', 'Basofil'))->nilai, 1),
                    'eosinofil'    => round(optional($items->firstWhere('subcategory.name', 'Eosinofil'))->nilai, 1),
                    'neutrofil'    => round(optional($items->firstWhere('subcategory.name', 'Neutrofil'))->nilai, 1),
                    'limfosit'     => round(optional($items->firstWhere('subcategory.name', 'Limfosit'))->nilai, 1),
                    'monosit'      => round(optional($items->firstWhere('subcategory.name', 'Monosit'))->nilai, 1),
                    'led'          => round(optional($items->firstWhere('subcategory.name', 'LED'))->nilai, 1),
                ];
            })->values();


            return view('app.statistic.komponen-darah', [
                'tandaVital' => $grouped
            ]);
            // return response()->json($tandaVital);
        } catch (\Throwable $th) {
            Log::error('General error: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
    }

    public function kimiaDarah($id)
    {
        $userId = User::find(decrypt($id))->id;
        try {
            $tandaVital = Value::with(['subcategory.category'])
                ->where('user_id', $userId)
                ->whereHas('subcategory.category', function ($q) {
                    $q->where('parent', 'Kimia Darah');
                })
                ->get();

            if ($tandaVital->isEmpty()) {
                return back()->with('error', 'Tidak ditemukan riwayat data kesehatan pada server.');
            }

            $grouped = $tandaVital->groupBy('tahun')->map(function ($items) {
                return [
                    'tahun'        => $items->first()->tahun,
                    'Ureum'         => round(optional($items->firstWhere('subcategory.name', 'Ureum'))->nilai, 1),
                    'Kreatinin'     => round(optional($items->firstWhere('subcategory.name', 'Kreatinin'))->nilai, 1),
                    'Asam Urat'     => round(optional($items->firstWhere('subcategory.name', 'Asam Urat'))->nilai, 1),
                    'eLFG (CKD-EPI)' => round(optional($items->firstWhere('subcategory.name', 'eLFG (CKD-EPI)'))->nilai, 1),
                    'Urea N'        => round(optional($items->firstWhere('subcategory.name', 'Urea N'))->nilai, 1),
                    'GOT'           => round(optional($items->firstWhere('subcategory.name', 'GOT'))->nilai, 1),
                    'GPT'           => round(optional($items->firstWhere('subcategory.name', 'GPT'))->nilai, 1),
                    'Chol. Total'     => round(optional($items->firstWhere('subcategory.name', 'Chol. Total'))->nilai, 1),
                    'Chol. LDL Direk'      => round(optional($items->firstWhere('subcategory.name', 'Chol. LDL Direk'))->nilai, 1),
                    'Chol. HDL'    => round(optional($items->firstWhere('subcategory.name', 'Chol. HDL'))->nilai, 1),
                    'Trigliserida'    => round(optional($items->firstWhere('subcategory.name', 'Trigliserida'))->nilai, 1),
                    'Ratio'     => round(optional($items->firstWhere('subcategory.name', 'Ratio'))->nilai, 1),
                    'Glukosa Puasa'      => round(optional($items->firstWhere('subcategory.name', 'Glukosa Puasa'))->nilai, 1),
                    'Glukosa 2 Jam PP'          => round(optional($items->firstWhere('subcategory.name', 'Glukosa 2 Jam PP'))->nilai, 1),
                    'HbA1c (NGSP)'          => round(optional($items->firstWhere('subcategory.name', 'HbA1c (NGSP)'))->nilai, 1),
                ];
            })->values();

            return view('app.statistic.kimia-darah', [
                'tandaVital' => $grouped
            ]);
            // return response()->json($tandaVital);
        } catch (\Throwable $th) {
            Log::error('General error: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
    }

    public function urinRutin($id)
    {
        $userId = User::find(decrypt($id))->id;

        try {
            $tandaVital = Value::with(['subcategory.category'])
                ->where('user_id', $userId)
                ->whereHas('subcategory.category', function ($q) {
                    $q->where('name', 'Urin Rutin'); // ganti sesuai nama kategori di DB
                })
                ->get();

            if ($tandaVital->isEmpty()) {
                return back()->with('error', 'Tidak ditemukan riwayat data kesehatan pada server.');
            }

            $grouped = $tandaVital->groupBy('tahun')->map(function ($items) {
                return [
                    'tahun'             => $items->first()->tahun,
                    'warna'             => optional($items->firstWhere('subcategory.name', 'Warna'))->nilai,
                    'kejernihan'        => optional($items->firstWhere('subcategory.name', 'Kejernihan'))->nilai,
                    'berat_jenis'       => optional($items->firstWhere('subcategory.name', 'Berat Jenis'))->nilai,
                    'ph'                => optional($items->firstWhere('subcategory.name', 'pH'))->nilai,
                    'nitrit'            => optional($items->firstWhere('subcategory.name', 'Nitrit'))->nilai,
                    'albumin'           => optional($items->firstWhere('subcategory.name', 'Albumin'))->nilai,
                    'glukosa'           => optional($items->firstWhere('subcategory.name', 'Glukosa'))->nilai,
                    'keton'             => optional($items->firstWhere('subcategory.name', 'Keton'))->nilai,
                    'urobilinogen'      => optional($items->firstWhere('subcategory.name', 'Urobilinogen'))->nilai,
                    'bilirubin'         => optional($items->firstWhere('subcategory.name', 'Bilirubin'))->nilai,
                    'darah'             => optional($items->firstWhere('subcategory.name', 'Darah (Blood)'))->nilai,
                    'leukosit_urine'    => optional($items->firstWhere('subcategory.name', 'Leukosit (Urine)'))->nilai,
                    'silinder_hyalin'   => optional($items->firstWhere('subcategory.name', 'Silinder Hyalin'))->nilai,
                    'bakteri'           => optional($items->firstWhere('subcategory.name', 'Bakteri'))->nilai,
                    'kristal_abnormal'  => optional($items->firstWhere('subcategory.name', 'Kristal Abnormal'))->nilai,
                    'silinder_lain'     => optional($items->firstWhere('subcategory.name', 'Silinder Lain-lain'))->nilai,
                    'epithel_gepeng'    => optional($items->firstWhere('subcategory.name', 'Epithel Gepeng'))->nilai,
                    'epithel_trans'     => optional($items->firstWhere('subcategory.name', 'Epithel Transitional'))->nilai,
                    'epithel_tubulus'   => optional($items->firstWhere('subcategory.name', 'Epithel Tubulus Ginjal'))->nilai,
                    'kristal_normal'    => optional($items->firstWhere('subcategory.name', 'Kristal Normal'))->nilai,
                    'lain_lain'         => optional($items->firstWhere('subcategory.name', 'Lain-lain'))->nilai,
                    'leukosit_esterase' => optional($items->firstWhere('subcategory.name', 'Leukosit Esterase'))->nilai,
                    'eritrosit_urine'   => optional($items->firstWhere('subcategory.name', 'Eritrosit (Urine)'))->nilai,
                ];
            })->values();

            return view('app.statistic.urin-rutin', [
                'tandaVital' => $grouped
            ]);
            // return response()->json($tandaVital);
        } catch (\Throwable $th) {
            Log::error('General error: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
    }

    public function kesimpulanSaran($id)
    {
        $userId = User::find(decrypt($id))->id;
        try {
            $tandaVital = Value::with(['subcategory.category'])
                ->where('user_id', $userId)
                ->whereHas('subcategory.category', function ($q) {
                    $q->where('name', 'Kesimpulan & Saran');
                })
                ->get();

            if ($tandaVital->isEmpty()) {
                return back()->with('error', 'Tidak ditemukan riwayat data kesehatan pada server.');
            }

            $grouped = $tandaVital->groupBy('tahun')->map(function ($items) {
                return [
                    'tahun'       => $items->first()->tahun,
                    'Kesimpulan'  => optional($items->firstWhere('subcategory.name', 'Kesimpulan'))->nilai,
                    'Saran'       => optional($items->firstWhere('subcategory.name', 'Saran'))->nilai,
                ];
            })->values();

            return view('app.statistic.kesimpulan-saran', [
                'tandaVital' => $grouped
            ]);
        } catch (\Throwable $th) {
            Log::error('General error: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
    }

    public function pemeriksaanDiagnostik($id)
    {
        $userId = User::find(decrypt($id))->id;
        try {
            $tandaVital = Value::with(['subcategory.category'])
                ->where('user_id', $userId)
                ->whereHas('subcategory.category', function ($q) {
                    $q->where('name', 'Jenis Pemeriksaan Penunjang Diagnostik');
                })
                ->get();

            if ($tandaVital->isEmpty()) {
                return back()->with('error', 'Tidak ditemukan riwayat data kesehatan pada server.');
            }

            $grouped = $tandaVital->groupBy('tahun')->map(function ($items) {
                return [
                    'tahun'       => $items->first()->tahun,
                    'ECG'         => optional($items->firstWhere('subcategory.name', 'ECG'))->nilai,
                    'Treadmill'   => optional($items->firstWhere('subcategory.name', 'Treadmill'))->nilai,
                    'PSA'         => optional($items->firstWhere('subcategory.name', 'PSA'))->nilai,
                    'APO-B'       => optional($items->firstWhere('subcategory.name', 'APO-B'))->nilai,
                    'Spirometri'  => optional($items->firstWhere('subcategory.name', 'Spirometri'))->nilai,
                    'Foto Thorax' => optional($items->firstWhere('subcategory.name', 'Foto Thorax'))->nilai,
                    'Echo'        => optional($items->firstWhere('subcategory.name', 'Echo'))->nilai,
                    'Audiometri'  => optional($items->firstWhere('subcategory.name', 'Audiometri'))->nilai,
                ];
            })->values();

            return view('app.statistic.pemeriksaan-diagnostik', [
                'tandaVital' => $grouped
            ]);
        } catch (\Throwable $th) {
            Log::error('General error: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
    }
}
