<?php

namespace App\Http\Controllers;

use App\Models\RecommendationRule;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RecommendationRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // rows per page
            $perPage = (int) $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

            // search keyword (safe)
            $search = trim((string) $request->get('search'));

            $rules = RecommendationRule::query()
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('group_code', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('severity_level', 'like', "%{$search}%")
                            ->orWhere('recommendation_text', 'like', "%{$search}%");
                    });
                })
                ->orderBy('group_code')
                ->orderBy('severity_level')
                ->orderBy('category')
                ->paginate($perPage)
                ->withQueryString(); // 🔥 keep search & per_page

            return view('app.recommendation_rules.index', compact('rules'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat data recommendation rule.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = [
            'global' => 'Global',
            'obesitas_metabolik' => 'Obesitas Metabolik',
            'diabetes' => 'Diabetes',
            'kardiovaskular' => 'Kardiovaskular',
            'ginjal' => 'Ginjal',
            'hati' => 'Hati',
            'hiperurisemia' => 'Hiperurisemia',
            'hemodinamik' => 'Hemodinamik',
        ];

        $severities = [
            'ringan' => 'Ringan',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'kritis' => 'Kritis',
        ];

        $categories = [
            'diet' => 'Diet',
            'exercise' => 'Exercise',
            'notes' => 'Notes',
        ];

        return view('app.recommendation_rules.create', compact(
            'groups',
            'severities',
            'categories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'group_code' => 'required|string|max:50',
                'category' => 'required|in:diet,exercise,notes',
                'severity_level' => 'required|in:ringan,sedang,tinggi,kritis',
                'min_score' => 'required|numeric|min:0|max:100',
                'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
                'recommendation_text' => 'required|string',
                'is_active' => 'required|boolean',
            ]);

            $validated['created_by'] = auth()->id();

            RecommendationRule::create($validated);

            return redirect()
                ->route('recommendation-rules.index')
                ->with('success', 'Recommendation rule berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan recommendation rule.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $id = decrypt($id);

            $rule = RecommendationRule::findOrFail($id);

            return view('app.recommendation_rules.show', compact('rule'));
        } catch (DecryptException | ModelNotFoundException $e) {
            return back()->with('error', 'Data recommendation rule tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat membuka data.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $id = decrypt($id);
            $rule = RecommendationRule::findOrFail($id);

            $groups = [
                'global' => 'Global',
                'obesitas_metabolik' => 'Obesitas Metabolik',
                'diabetes' => 'Diabetes',
                'kardiovaskular' => 'Kardiovaskular',
                'ginjal' => 'Ginjal',
                'hati' => 'Hati',
                'hiperurisemia' => 'Hiperurisemia',
                'hemodinamik' => 'Hemodinamik',
            ];

            $severities = [
                'ringan' => 'Ringan',
                'sedang' => 'Sedang',
                'tinggi' => 'Tinggi',
                'kritis' => 'Kritis',
            ];

            $categories = [
                'diet' => 'Diet',
                'exercise' => 'Exercise',
                'notes' => 'Notes',
            ];

            return view('app.recommendation_rules.edit', compact(
                'rule',
                'groups',
                'severities',
                'categories'
            ));
        } catch (DecryptException | ModelNotFoundException $e) {
            return back()->with('error', 'Data recommendation rule tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuka halaman edit.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $id = decrypt($id);
            $rule = RecommendationRule::findOrFail($id);

            $validated = $request->validate([
                'group_code' => 'required|string|max:50',
                'category' => 'required|in:diet,exercise,notes',
                'severity_level' => 'required|in:ringan,sedang,tinggi,kritis',
                'min_score' => 'required|numeric|min:0|max:100',
                'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
                'recommendation_text' => 'required|string',
                'is_active' => 'required|boolean',
            ]);

            $rule->update($validated);

            return redirect()
                ->route('recommendation-rules.index')
                ->with('success', 'Recommendation rule berhasil diperbarui.');
        } catch (DecryptException | ModelNotFoundException $e) {
            return back()->with('error', 'Data recommendation rule tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui recommendation rule.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $id = decrypt($id);
            $rule = RecommendationRule::findOrFail($id);

            $rule->delete();

            return redirect()
                ->route('recommendation-rules.index')
                ->with('success', 'Recommendation rule berhasil dihapus.');
        } catch (DecryptException | ModelNotFoundException $e) {
            return back()->with('error', 'Data recommendation rule tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus recommendation rule.');
        }
    }
}
