<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\User;
use App\Models\Value;
use App\Services\FuzzyMamdaniService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * RecommendationController
 *
 * Alur kerja:
 *  1. index()        — Dokter melihat daftar karyawan + filter tahun MCU
 *  2. generateAll()  — Satu tombol: generate rekomendasi SEMUA karyawan untuk tahun dipilih
 *  3. acceptAll()    — Batch approve semua rekomendasi pending untuk tahun dipilih
 *  4. show()         — Detail + form validasi (dengan navigasi prev/next karyawan)
 *  5. validateRec()  — approve | reject | update (edit teks saja tanpa ubah status)
 *  6. employeeView() — Karyawan lihat rekomendasi yang sudah divalidasi (read-only)
 */
class RecommendationController extends Controller
{
    // Sub-category names — harus persis cocok dengan sub_categories.name di DB
    private const SC_BMI          = 'IMT (kg/m2)';
    private const SC_SISTOLIK     = 'Tekanan darah Sistolik (mmHg)';
    private const SC_DIASTOLIK    = 'Tekanan darah Diastolik (mmHg)';
    private const SC_GLUKOSA      = 'Glukosa Puasa';
    private const SC_KOLESTEROL   = 'Chol. Total';
    private const SC_ASAM_URAT    = 'Asam Urat';
    private const SC_TRIGLISERIDA = 'Trigliserida';

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
        // Ambil semua tahun yang memiliki data MCU (dari tabel values)
        $availableYears = Value::distinct()->orderByDesc('tahun')->pluck('tahun');

        // Tahun yang dipilih — default ke tahun terbaru
        $selectedYear = $request->get('tahun', $availableYears->first() ?? date('Y'));

        // Semua karyawan (peran = 2), diurutkan berdasarkan nama
        $users = User::where('peran', 2)->orderBy('name')->get();

        // Rekomendasi untuk tahun yang dipilih, di-index berdasarkan user_id
        $recsByUser = Recommendation::where('tahun', $selectedYear)
            ->get()
            ->keyBy('user_id');

        // Statistik ringkas untuk tahun terpilih
        $pendingCount  = $recsByUser->where('status', 'pending')->count();
        $approvedCount = $recsByUser->where('status', 'approved')->count();
        $rejectedCount = $recsByUser->where('status', 'rejected')->count();

        return view('app.rekomendasi.index', compact(
            'users',
            'recsByUser',
            'availableYears',
            'selectedYear',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    // =========================================================================
    // 2. GENERATE ALL — Satu tombol untuk semua karyawan
    // =========================================================================

    /**
     * Bangkitkan rekomendasi Fuzzy Mamdani untuk SEMUA karyawan
     * yang mempunyai data MCU pada tahun yang dipilih.
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
            // Ambil semua nilai MCU karyawan ini untuk tahun terpilih
            $values = Value::with('subCategory')
                ->where('user_id', $user->id)
                ->where('tahun', $tahun)
                ->get()
                ->keyBy(fn($v) => $v->subCategory->name ?? 'unknown');

            // Helper: ambil nilai numerik, default 0 jika kosong
            $get = fn(string $name, float $default = 0): float =>
                (float) ($values->get($name)?->nilai ?? $default);

            // Susun array input untuk FuzzyMamdaniService
            $inputs = [
                'bmi'          => $get(self::SC_BMI),
                'sistolik'     => $get(self::SC_SISTOLIK),
                'diastolik'    => $get(self::SC_DIASTOLIK),
                'glukosa'      => $get(self::SC_GLUKOSA),
                'kolesterol'   => $get(self::SC_KOLESTEROL),
                'asam_urat'    => $get(self::SC_ASAM_URAT),
                'trigliserida' => $get(self::SC_TRIGLISERIDA),
                'gender'       => $user->gender ?? 'L',  // 'L' atau 'P'
            ];

            // Jalankan pipeline: fuzzify → applyRules → defuzzify → recommend
            $result = $this->fuzzy->process($inputs);

            // Simpan atau timpa record — selalu reset ke status 'pending'
            // agar dokter melakukan validasi ulang
            Recommendation::updateOrCreate(
                ['user_id' => $user->id, 'tahun' => $tahun],
                [
                    // Snapshot input
                    'bmi'           => $inputs['bmi'],
                    'sistolik'      => $inputs['sistolik'],
                    'diastolik'     => $inputs['diastolik'],
                    'glukosa_puasa' => $inputs['glukosa'],
                    'kolesterol'    => $inputs['kolesterol'],
                    'asam_urat'     => $inputs['asam_urat'],
                    'trigliserida'  => $inputs['trigliserida'],
                    // Output fuzzy
                    'risk_score'    => $result['risk_score'],
                    'risk_label'    => $result['risk_label'],
                    // Teks rekomendasi auto-generate
                    'rec_diet'      => $result['rec_diet'],
                    'rec_exercise'  => $result['rec_exercise'],
                    'rec_notes'     => $result['rec_notes'],
                    // Reset status validasi
                    'status'        => 'pending',
                    'doctor_id'     => null,
                    'validated_at'  => null,
                    'doctor_notes'  => null,
                ]
            );

            $generated++;
        }

        Log::info("FuzzyMamdani: {$generated} rekomendasi dibangkitkan untuk tahun {$tahun} oleh user " . Auth::id());

        return redirect()
            ->route('rekomendasi.index', ['tahun' => $tahun])
            ->with('success', "Berhasil membangkitkan {$generated} rekomendasi untuk tahun {$tahun}. Status semua direset ke Pending.");
    }

    // =========================================================================
    // 3. ACCEPT ALL — Batch approve semua pending
    // =========================================================================

    /**
     * Setujui semua rekomendasi yang masih berstatus 'pending'
     * untuk tahun yang dipilih.
     *
     * Route: POST /rekomendasi/accept-all
     */
    public function acceptAll(Request $request)
    {
        $tahun = $request->get('tahun');

        $query = Recommendation::where('status', 'pending');
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $count = $query->count();

        if ($count === 0) {
            return redirect()
                ->route('rekomendasi.index', $tahun ? ['tahun' => $tahun] : [])
                ->with('error', 'Tidak ada rekomendasi pending yang dapat disetujui.');
        }

        $query->update([
            'status'       => 'approved',
            'doctor_id'    => Auth::id(),
            'validated_at' => now(),
        ]);

        return redirect()
            ->route('rekomendasi.index', $tahun ? ['tahun' => $tahun] : [])
            ->with('success', "{$count} rekomendasi berhasil disetujui sekaligus.");
    }

    // =========================================================================
    // 4. SHOW — Detail + form validasi + navigasi prev/next
    // =========================================================================

    /**
     * Tampilkan detail rekomendasi satu karyawan beserta form validasi.
     * Menyertakan ID rekomendasi sebelumnya dan berikutnya (dalam tahun yang sama,
     * diurutkan berdasarkan nama karyawan) untuk navigasi kiri/kanan.
     *
     * Route: GET /rekomendasi/{id}?tahun=2025
     */
    public function show(int $id, Request $request)
    {
        $rec   = Recommendation::with('user', 'doctor')->findOrFail($id);
        $tahun = $request->get('tahun', $rec->tahun);

        // Ambil daftar ID rekomendasi untuk tahun yang sama,
        // diurutkan berdasarkan nama karyawan → konsisten untuk navigasi
        $allRecIds = Recommendation::where('tahun', $tahun)
            ->join('users', 'recommendations.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->pluck('recommendations.id')
            ->values();  // re-index dari 0

        $currentIndex = $allRecIds->search($id);

        $prevId = ($currentIndex !== false && $currentIndex > 0)
            ? $allRecIds[$currentIndex - 1]
            : null;

        $nextId = ($currentIndex !== false && $currentIndex < $allRecIds->count() - 1)
            ? $allRecIds[$currentIndex + 1]
            : null;

        // Posisi untuk label navigasi (misal: "3 / 10")
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
    }

    // =========================================================================
    // 5. VALIDATE — approve | reject | update
    // =========================================================================

    /**
     * Simpan keputusan validasi dokter.
     *
     * action = 'approved' → ubah status ke approved, catat dokter & waktu
     * action = 'rejected' → ubah status ke rejected, catat dokter & waktu
     * action = 'update'   → simpan perubahan teks, PERTAHANKAN status yang ada
     *                        (digunakan setelah rekomendasi sudah di-approve)
     *
     * Route: PUT /rekomendasi/{id}/validate
     */
    public function validateRec(Request $request, int $id)
    {
        $request->validate([
            'action'       => 'required|in:approved,rejected,update',
            'rec_diet'     => 'nullable|string',
            'rec_exercise' => 'nullable|string',
            'rec_notes'    => 'nullable|string',
            'doctor_notes' => 'nullable|string|max:1000',
        ]);

        $rec   = Recommendation::findOrFail($id);
        $tahun = $request->get('tahun', $rec->tahun);

        $updateData = [
            // Teks rekomendasi — gunakan nilai baru jika ada, jika tidak pakai yang lama
            'rec_diet'     => $request->filled('rec_diet')     ? $request->rec_diet     : $rec->rec_diet,
            'rec_exercise' => $request->filled('rec_exercise') ? $request->rec_exercise : $rec->rec_exercise,
            'rec_notes'    => $request->filled('rec_notes')    ? $request->rec_notes    : $rec->rec_notes,
            'doctor_notes' => $request->doctor_notes,
            'doctor_id'    => Auth::id(),
            'validated_at' => now(),
        ];

        // Jika action = 'update': hanya simpan teks, jangan ubah status
        if ($request->action !== 'update') {
            $updateData['status'] = $request->action;
        }

        $rec->update($updateData);

        $actionLabel = match($request->action) {
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            default    => 'diperbarui',
        };

        return redirect()
            ->route('rekomendasi.index', ['tahun' => $tahun])
            ->with('success', "Rekomendasi untuk {$rec->user->name} berhasil {$actionLabel}.");
    }

    // =========================================================================
    // 6. EMPLOYEE VIEW — Read-only untuk karyawan
    // =========================================================================

    /**
     * Tampilkan rekomendasi yang sudah di-approve dokter ke karyawan (read-only).
     *
     * Route: GET /rekomendasi-saya/{id}
     */
    public function employeeView(string $id)
    {
        try {
            $userId = decrypt($id);
        } catch (\Exception $e) {
            return back()->with('error', 'ID tidak valid.');
        }

        // Karyawan hanya boleh melihat data milik sendiri
        $authUser = Auth::user();
        if (!$authUser->hasRole(['super-admin', 'Dokter']) && $authUser->id !== $userId) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $user = User::findOrFail($userId);

        // Hanya tampilkan yang sudah di-approve
        $recommendations = Recommendation::where('user_id', $userId)
            ->where('status', 'approved')
            ->with('doctor')
            ->orderByDesc('tahun')
            ->get();

        return view('app.rekomendasi.employee', compact('user', 'recommendations'));
    }
}
