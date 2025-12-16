<?php

namespace App\Http\Controllers\Api;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ValueResource;
use App\Http\Resources\ValueCollection;

class SubCategoryValuesController extends Controller
{
    public function index(
        Request $request,
        SubCategory $subCategory
    ): ValueCollection {
        $this->authorize('view', $subCategory);

        $search = $request->get('search', '');

        $values = $subCategory
            ->values()
            ->search($search)
            ->latest()
            ->paginate();

        return new ValueCollection($values);
    }

    public function store(
        Request $request,
        SubCategory $subCategory
    ): ValueResource {
        $this->authorize('create', Value::class);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'nilai' => ['required', 'max:255', 'string'],
            'tahun' => ['required', 'max:255', 'string'],
        ]);

        $value = $subCategory->values()->create($validated);

        return new ValueResource($value);
    }
}
