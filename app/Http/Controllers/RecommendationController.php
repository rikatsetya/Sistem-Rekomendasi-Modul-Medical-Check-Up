<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\User;
use App\Models\Value;
use App\Services\FuzzyMamdaniService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * RecommendationController
 *
 * Alur kerja:
 *  1. index()       — Dokter melihat daftar karyawan + filter tahun MCU
 *  2. generateAll() — Generate rekomendasi Fuzzy Mamdani Hierarkis untuk semua karyawan
 *  3. publishAll()  — Batch publish semua rekomendasi berstatus draft
 *  4. show()        — Detail rekomendasi + navigasi prev/next
 *  5. update()      — Dokter mengedit & menyimpan rekomendasi sebagai draft
 *
 * Sistem menggunakan 23 parameter MCU yang dikelompokkan ke dalam 7 kelompok
 * (Fuzzy Mamdani Hierarkis), menghasilkan 7 skor risiko (0–100) per karyawan.
 */
class RecommendationController extends Controller
{
    // =========================================================================
    // SUB-CATEGORY CONSTANTS
    // Nama harus PERSIS cocok dengan kolom sub_categories.name di database.
    // =========================================================================

    // ── Kelompok 1: Fungsi Hati ───────────────────────────────────────────────
    private const SC_GOT            = 'GOT';
    private const SC_GPT            = 'GPT';

    // ── Kelompok 2: Diabetes / Gula Darah ────────────────────────────────────
    private const SC_GLUKOSA_PUASA  = 'Glukosa Puasa';
    private const SC_GLUKOSA_2J_PP  = 'Glukosa 2 Jam PP';
    private const SC_HBA1C          = 'HbA1c (NGSP)';

    // ── Kelompok 3: Profil Lipid ──────────────────────────────────────────────
    private const SC_CHOL_TOTAL     = 'Chol. Total';
    private const SC_CHOL_LDL       = 'Chol. LDL Direk';
    private const SC_CHOL_HDL       = 'Chol. HDL';
    private const SC_TRIGLISERIDA   = 'Trigliserida';
    private const SC_APO_B          = 'APO-B';

    // ── Kelompok 4: Fungsi Ginjal ─────────────────────────────────────────────
    private const SC_UREA_N         = 'Urea N';
    private const SC_UREUM          = 'Ureum';
    private const SC_KREATININ      = 'Kreatinin';
    private const SC_ELFG           = 'eLFG (CKD-EPI)';

    // ── Kelompok 5: Asam Urat ─────────────────────────────────────────────────
    private const SC_ASAM_URAT      = 'Asam Urat';

    // ── Kelompok 6: Kardiovaskular & Tanda Vital ──────────────────────────────
    private const SC_NADI           = 'Nadi (kali/menit)';
    private const SC_PERNAFASAN     = 'Pernafasan (kali/menit)';
    private const SC_SISTOLIK       = 'Tekanan darah Sistolik (mmHg)';
    private const SC_DIASTOLIK      = 'Tekanan darah Diastolik (mmHg)';

    // ── Kelompok 7: Antropometri & Obesitas ───────────────────────────────────
    private const SC_TINGGI_BADAN   = 'Tinggi Badan (cm)';
    private const SC_BERAT_BADAN    = 'Berat Badan (kg)';
    private const SC_IMT            = 'IMT (kg/m2)';
    private const SC_LINGKAR_PERUT  = 'Lingkar Perut (cm)';

    // =========================================================================

    public function __construct(private FuzzyMamdaniService $fuzzy)
    {
        // FuzzyMamdaniService di-inject otomatis oleh Laravel service container
    }

    // =========================================================================
    // 1. INDEX — Daftar karyawan + filter tahun
    // =========================================================================

    /**
     * Tampilkan daftar semua karyawan dengan status rekomendasi mereka
     * untuk tahun MCU yang dipilih.
     *
     * Route: GET /rekomendasi?tahun=2025
     */
    public function index(Request $request)
    {
        // ── Tahun yang tersedia ───────────────────────────────────────────────
        $availableYears = Value::distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $selectedYear = $request->get(
            'tahun',
            $availableYears->first() ?? date('Y')
        );

        // ── Parameter pencarian ───────────────────────────────────────────────
        $search = trim($request->get('search'));

        // ── Query karyawan ────────────────────────────────────────────────────
        $usersQuery = User::where('peran', 2)->orderBy('name');

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kopeg', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;
        $users   = $usersQuery->paginate($perPage)->withQueryString();

        // ── Rekomendasi berdasarkan tahun ─────────────────────────────────────
        $recsByUser = Recommendation::where('tahun', $selectedYear)
            ->get()
            ->keyBy('user_id');

        // ── Statistik ─────────────────────────────────────────────────────────
        $generatedCount = $recsByUser->where('status', 'draft')->count();
        $publishedCount = $recsByUser->where('status', 'published')->count();
        $rawCount       = $users->total() - $recsByUser->count();

        return view('app.rekomendasi.index', compact(
            'users',
            'recsByUser',
            'availableYears',
            'selectedYear',
            'rawCount',
            'generatedCount',
            'publishedCount',
            'search'
        ));
    }

    // =========================================================================
    // 2. GENERATE ALL — Fuzzy Mamdani Hierarkis untuk semua karyawan
    // =========================================================================

    /**
     * Bangkitkan rekomendasi Fuzzy Mamdani Hierarkis (23 parameter, 7 kelompok)
     * untuk SEMUA karyawan yang mempunyai data MCU pada tahun yang dipilih.
     *
     * Route: POST /rekomendasi/generate-all
     */
    public function generateAll(Request $request)
    {
        $request->validate(['tahun' => 'required|string']);
        $tahun = $request->tahun;

        // Cari user yang punya data MCU untuk tahun ini
        $userIdsWithData = Value::where('tahun', $tahun)
            ->distinct()
            ->pluck('user_id');

        $users = User::whereIn('id', $userIdsWithData)->get();

        if ($users->isEmpty()) {
            return redirect()
                ->route('rekomendasi.index', ['tahun' => $tahun])
                ->with('error', "Tidak ada data MCU yang tersedia untuk tahun {$tahun}.");
        }

        $generated = 0;

        foreach ($users as $user) {
            // ── Ambil semua nilai MCU karyawan ini, di-index per nama sub-kategori ──
            $values = Value::with('subCategory')
                ->where('user_id', $user->id)
                ->where('tahun', $tahun)
                ->get()
                ->keyBy(fn($v) => $v->subCategory->name ?? 'unknown');

            // Helper: ambil nilai numerik, default 0 jika kosong
            $get = fn(string $name, float $default = 0): float =>
                (float) ($values->get($name)?->nilai ?? $default);

            // ── Susun 23 input fuzzy per kelompok ────────────────────────────
            $inputs = [
                // Kelompok 1: Fungsi Hati
                'got'           => $get(self::SC_GOT),
                'gpt'           => $get(self::SC_GPT),

                // Kelompok 2: Diabetes
                'glukosa_puasa' => $get(self::SC_GLUKOSA_PUASA),
                'glukosa_2j_pp' => $get(self::SC_GLUKOSA_2J_PP),
                'hba1c'         => $get(self::SC_HBA1C),

                // Kelompok 3: Profil Lipid
                'chol_total'    => $get(self::SC_CHOL_TOTAL),
                'chol_ldl'      => $get(self::SC_CHOL_LDL),
                'chol_hdl'      => $get(self::SC_CHOL_HDL),
                'trigliserida'  => $get(self::SC_TRIGLISERIDA),
                'apo_b'         => $get(self::SC_APO_B),

                // Kelompok 4: Fungsi Ginjal
                'urea_n'        => $get(self::SC_UREA_N),
                'ureum'         => $get(self::SC_UREUM),
                'kreatinin'     => $get(self::SC_KREATININ),
                'elfg'          => $get(self::SC_ELFG),

                // Kelompok 5: Asam Urat
                'asam_urat'     => $get(self::SC_ASAM_URAT),

                // Kelompok 6: Kardiovaskular
                'nadi'          => $get(self::SC_NADI),
                'pernafasan'    => $get(self::SC_PERNAFASAN),
                'sistolik'      => $get(self::SC_SISTOLIK),
                'diastolik'     => $get(self::SC_DIASTOLIK),

                // Kelompok 7: Antropometri
                'tinggi_badan'  => $get(self::SC_TINGGI_BADAN),
                'berat_badan'   => $get(self::SC_BERAT_BADAN),
                'imt'           => $get(self::SC_IMT),
                'lingkar_perut' => $get(self::SC_LINGKAR_PERUT),

                // Meta
                'gender'        => $user->gender ?? 'L',
            ];

            // ── Jalankan pipeline Fuzzy Mamdani Hierarkis ────────────────────
            try {
                $result = $this->fuzzy->process($inputs);
            } catch (\Throwable $e) {
                Log::warning("FuzzyMamdani: Gagal memproses user {$user->id}: " . $e->getMessage());
                continue;
            }

            // ── Simpan atau timpa rekomendasi ─────────────────────────────────
            Recommendation::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'tahun'   => $tahun,
                ],
                [
                    // ── Snapshot input Kelompok 1: Fungsi Hati ─────────────
                    'got'           => $inputs['got'],
                    'gpt'           => $inputs['gpt'],

                    // ── Snapshot input Kelompok 2: Diabetes ────────────────
                    'glukosa_puasa' => $inputs['glukosa_puasa'],
                    'glukosa_2j_pp' => $inputs['glukosa_2j_pp'],
                    'hba1c'         => $inputs['hba1c'],

                    // ── Snapshot input Kelompok 3: Profil Lipid ────────────
                    'chol_total'    => $inputs['chol_total'],
                    'chol_ldl'      => $inputs['chol_ldl'],
                    'chol_hdl'      => $inputs['chol_hdl'],
                    'trigliserida'  => $inputs['trigliserida'],
                    'apo_b'         => $inputs['apo_b'],

                    // ── Snapshot input Kelompok 4: Fungsi Ginjal ───────────
                    'urea_n'        => $inputs['urea_n'],
                    'ureum'         => $inputs['ureum'],
                    'kreatinin'     => $inputs['kreatinin'],
                    'elfg'          => $inputs['elfg'],

                    // ── Snapshot input Kelompok 5: Asam Urat ───────────────
                    'asam_urat'     => $inputs['asam_urat'],

                    // ── Snapshot input Kelompok 6: Kardiovaskular ──────────
                    'nadi'          => $inputs['nadi'],
                    'pernafasan'    => $inputs['pernafasan'],
                    'sistolik'      => $inputs['sistolik'],
                    'diastolik'     => $inputs['diastolik'],

                    // ── Snapshot input Kelompok 7: Antropometri ────────────
                    'tinggi_badan'  => $inputs['tinggi_badan'],
                    'berat_badan'   => $inputs['berat_badan'],
                    'imt'           => $inputs['imt'],
                    'lingkar_perut' => $inputs['lingkar_perut'],

                    // ── Output Fuzzy Hierarki ───────────────────────────────
                    'group_scores'  => json_encode($result['group_scores'], JSON_UNESCAPED_UNICODE),
                    'risk_score'    => $result['risk_score'],
                    'risk_label'    => $result['risk_label'],
                    'duration'      => $result['duration'],

                    // ── Teks rekomendasi (JSON array) ───────────────────────
                    'rec_diet'      => json_encode($result['rec_diet'],     JSON_UNESCAPED_UNICODE),
                    'rec_exercise'  => json_encode($result['rec_exercise'], JSON_UNESCAPED_UNICODE),
                    'rec_notes'     => json_encode($result['rec_notes'],    JSON_UNESCAPED_UNICODE),

                    // ── Status & validasi ───────────────────────────────────
                    'status'        => 'draft',
                    'doctor_id'     => null,
                    'validated_at'  => null,
                    'doctor_notes'  => null,
                ]
            );

            $generated++;
        }

        Log::info(
            "FuzzyMamdani Hierarkis: {$generated} rekomendasi (draft) dibangkitkan untuk tahun {$tahun} oleh user " . Auth::id()
        );

        return redirect()
            ->route('rekomendasi.index', ['tahun' => $tahun])
            ->with(
                'success',
                "Berhasil membangkitkan {$generated} rekomendasi untuk tahun {$tahun}. Status disimpan sebagai DRAFT."
            );
    }

    // =========================================================================
    // 3. PUBLISH ALL — Batch publish semua draft
    // =========================================================================

    /**
     * Publish semua rekomendasi yang masih berstatus 'draft'
     * untuk tahun yang dipilih.
     *
     * Route: POST /rekomendasi/publish-all
     */
    public function publishAll(Request $request)
    {
        $tahun = $request->get('tahun');

        $query = Recommendation::where('status', 'draft');

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $count = $query->count();

        if ($count === 0) {
            return redirect()
                ->route('rekomendasi.index', $tahun ? ['tahun' => $tahun] : [])
                ->with('error', 'Tidak ada rekomendasi hasil generate yang dapat dipublish.');
        }

        $query->update([
            'status'       => 'published',
            'doctor_id'    => Auth::id(),
            'validated_at' => now(),
        ]);

        return redirect()
            ->route('rekomendasi.index', $tahun ? ['tahun' => $tahun] : [])
            ->with('success', "{$count} rekomendasi berhasil dipublish dan kini dapat dilihat oleh karyawan.");
    }

    // =========================================================================
    // 4. SHOW — Detail rekomendasi + navigasi prev/next
    // =========================================================================

    /**
     * Route: GET /rekomendasi/{id}
     */
    public function show(string $id, Request $request)
    {
        try {
            $decryptedId = decrypt($id);

            $rec   = Recommendation::with('user', 'doctor')->findOrFail($decryptedId);
            $tahun = $request->get('tahun', $rec->tahun);

            $allRecIds = Recommendation::where('tahun', $tahun)
                ->join('users', 'recommendations.user_id', '=', 'users.id')
                ->orderBy('users.name')
                ->pluck('recommendations.id')
                ->values();

            $currentIndex = $allRecIds->search($rec->id);

            $prevId = ($currentIndex !== false && $currentIndex > 0)
                ? encrypt($allRecIds[$currentIndex - 1])
                : null;

            $nextId = ($currentIndex !== false && $currentIndex < $allRecIds->count() - 1)
                ? encrypt($allRecIds[$currentIndex + 1])
                : null;

            $position = $currentIndex !== false ? ($currentIndex + 1) : '?';
            $total    = $allRecIds->count();

            return view('app.rekomendasi.show', compact(
                'rec',
                'prevId',
                'nextId',
                'tahun',
                'position',
                'total'
            ));
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'ID rekomendasi tidak valid.');
        } catch (\Throwable $e) {
            Log::error('Show Recommendation Error', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuka detail rekomendasi.');
        }
    }

    // =========================================================================
    // 5. UPDATE — Dokter mengedit teks rekomendasi
    // =========================================================================

    /**
     * Route: PUT /rekomendasi/{id}
     */
    public function update(Request $request, string $id)
    {
        try {
            $decryptedId = decrypt($id);

            $request->validate([
                'rec_diet'     => 'nullable|string',
                'rec_exercise' => 'nullable|string',
                'rec_notes'    => 'nullable|string',
                'doctor_notes' => 'nullable|string|max:1000',
                'tahun'        => 'required',
            ]);

            $rec   = Recommendation::findOrFail($decryptedId);
            $tahun = $request->get('tahun', $rec->tahun);

            $rec->update([
                'rec_diet'     => $request->rec_diet,
                'rec_exercise' => $request->rec_exercise,
                'rec_notes'    => $request->rec_notes,
                'doctor_notes' => $request->doctor_notes,

                'status'       => 'draft',
                'doctor_id'    => auth()->id(),
                'updated_at'   => now(),
            ]);

            return redirect()
                ->route('rekomendasi.index', ['tahun' => $tahun])
                ->with('success', 'Perubahan rekomendasi berhasil disimpan sebagai draft.');
        } catch (DecryptException $e) {
            return redirect()->back()->withInput()->with('error', 'ID rekomendasi tidak valid.');
        } catch (\Throwable $e) {
            Log::error('Update Recommendation Error', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan rekomendasi. Silakan coba lagi.');
        }
    }
}
