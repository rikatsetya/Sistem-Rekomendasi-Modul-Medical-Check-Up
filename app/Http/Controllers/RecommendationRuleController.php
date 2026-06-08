<?php

namespace App\Http\Controllers;

use App\Models\RecommendationRule;
use Illuminate\Http\Request;

class RecommendationRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rules = RecommendationRule::query()
            ->orderBy('group_code')
            ->orderBy('severity_level')
            ->orderBy('category')
            ->paginate(15);

        return view('app.recommendation_rules.index', compact('rules'));
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
    }

    /**
     * Display the specified resource.
     */
    public function show(RecommendationRule $recommendationRule)
    {
        return view('app.recommendation_rules.show', [
            'rule' => $recommendationRule
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecommendationRule $recommendationRule)
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

        return view('app.recommendation_rules.edit', [
            'rule' => $recommendationRule,
            'groups' => $groups,
            'severities' => $severities,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RecommendationRule $recommendationRule)
    {
        $validated = $request->validate([
            'group_code' => 'required|string|max:50',
            'category' => 'required|in:diet,exercise,notes',
            'severity_level' => 'required|in:ringan,sedang,tinggi,kritis',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'recommendation_text' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $recommendationRule->update($validated);

        return redirect()
            ->route('recommendation-rules.index')
            ->with('success', 'Recommendation rule berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecommendationRule $recommendationRule)
    {
        $recommendationRule->delete();

        return redirect()
            ->route('recommendation-rules.index')
            ->with('success', 'Recommendation rule berhasil dihapus.');
    }
}
