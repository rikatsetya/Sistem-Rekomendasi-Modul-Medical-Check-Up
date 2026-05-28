<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recommendation;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id;

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

        $user = User::where('kopeg', Auth::user()->kopeg)->pluck('name', 'id');

        $selectedYear = $request->get('tahun', now()->year);

        // Ambil recommendation FINAL (published) untuk user & tahun
        $recommendations = Recommendation::where('user_id', $userId)
            ->where('status', 'published')
            ->where('tahun', $selectedYear)
            ->first(); // <-- penting: first(), bukan get()

        $notesNow    = $recommendations?->rec_notes;
        $makananNow  = $recommendations?->rec_diet;
        $olahragaNow = $recommendations?->rec_exercise;

        // return response()->json([$subCategory]);
        return view('home', compact(
            'subCategory',
            'user',
            'userId',
            'recommendations',
            'notesNow',
            'makananNow',
            'olahragaNow'
        ));
    }
}
