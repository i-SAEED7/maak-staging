<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\IepGoalBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IepGoalBankController extends Controller
{
    /**
     * List goal bank entries with optional filters.
     *
     * Query parameters:
     *  - disability_category_id (int)
     *  - domain (string)
     *  - grade_level (int)
     *  - search (string)
     */
    public function index(Request $request): JsonResponse
    {
        $query = IepGoalBank::query()->active()->orderBy('sort_order');

        if ($request->filled('disability_category_id')) {
            $query->forDisability((int) $request->input('disability_category_id'));
        }

        if ($request->filled('domain')) {
            $query->where('domain', $request->input('domain'));
        }

        if ($request->filled('grade_level')) {
            $query->forGradeLevel((int) $request->input('grade_level'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('goal_text', 'ILIKE', "%{$search}%");
        }

        $goals = $query->paginate($request->integer('per_page', 25));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب بنك الأهداف بنجاح',
            'data' => $goals->items(),
            'meta' => [
                'page' => $goals->currentPage(),
                'per_page' => $goals->perPage(),
                'total' => $goals->total(),
                'last_page' => $goals->lastPage(),
            ],
        ]);
    }

    /**
     * Get distinct domains for dropdown.
     */
    public function domains(): JsonResponse
    {
        $domains = IepGoalBank::query()
            ->active()
            ->distinct()
            ->pluck('domain')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المجالات بنجاح',
            'data' => $domains,
        ]);
    }

    /**
     * Store a new goal bank entry (Super Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'disability_category_id' => 'required|exists:disability_categories,id',
            'domain' => 'required|string|max:100',
            'goal_text' => 'required|string',
            'strategies' => 'nullable|array',
            'suggested_criteria' => 'nullable|array',
            'grade_level_min' => 'nullable|integer|min:1|max:12',
            'grade_level_max' => 'nullable|integer|min:1|max:12',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $goal = IepGoalBank::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة الهدف بنجاح',
            'data' => $goal,
        ], 201);
    }

    /**
     * Update a goal bank entry.
     */
    public function update(Request $request, IepGoalBank $iepGoalBank): JsonResponse
    {
        $validated = $request->validate([
            'disability_category_id' => 'sometimes|exists:disability_categories,id',
            'domain' => 'sometimes|string|max:100',
            'goal_text' => 'sometimes|string',
            'strategies' => 'nullable|array',
            'suggested_criteria' => 'nullable|array',
            'grade_level_min' => 'nullable|integer|min:1|max:12',
            'grade_level_max' => 'nullable|integer|min:1|max:12',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $iepGoalBank->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الهدف بنجاح',
            'data' => $iepGoalBank->fresh(),
        ]);
    }

    /**
     * Soft-delete a goal bank entry.
     */
    public function destroy(IepGoalBank $iepGoalBank): JsonResponse
    {
        $iepGoalBank->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الهدف بنجاح',
        ]);
    }
}
