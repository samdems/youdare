<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = PlayerGroup::query();

        // Include counts
        if ($request->boolean('with_counts')) {
            $query->withCount(['players']);
        }

        // Include players
        if ($request->boolean('with_players')) {
            $query->with(['players' => function ($q) {
                $q->orderBy('name');
            }]);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');

        if (in_array($sortBy, ['name', 'slug', 'sort_order', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $perPage = min(100, max(1, $perPage));

        $playerGroups = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $playerGroups->items(),
            'meta' => [
                'current_page' => $playerGroups->currentPage(),
                'last_page' => $playerGroups->lastPage(),
                'per_page' => $playerGroups->perPage(),
                'total' => $playerGroups->total(),
                'from' => $playerGroups->firstItem(),
                'to' => $playerGroups->lastItem(),
            ],
            'links' => [
                'first' => $playerGroups->url(1),
                'last' => $playerGroups->url($playerGroups->lastPage()),
                'prev' => $playerGroups->previousPageUrl(),
                'next' => $playerGroups->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:player_groups,slug',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Set default values
        if (!isset($validated['color'])) {
            $validated['color'] = '#3b82f6';
        }
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        $playerGroup = PlayerGroup::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Player group created successfully',
            'data' => $playerGroup,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param PlayerGroup $playerGroup
     * @return JsonResponse
     */
    public function show(PlayerGroup $playerGroup): JsonResponse
    {
        $playerGroup->loadCount(['players']);
        $playerGroup->load(['players' => function ($query) {
            $query->orderBy('name');
        }]);

        return response()->json([
            'status' => 'success',
            'data' => $playerGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param PlayerGroup $playerGroup
     * @return JsonResponse
     */
    public function update(Request $request, PlayerGroup $playerGroup): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:player_groups,slug,' . $playerGroup->id,
            'description' => 'sometimes|nullable|string|max:1000',
            'color' => 'sometimes|nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'sometimes|nullable|string|max:10',
            'sort_order' => 'sometimes|nullable|integer|min:0',
        ]);

        $playerGroup->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Player group updated successfully',
            'data' => $playerGroup->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PlayerGroup $playerGroup
     * @return JsonResponse
     */
    public function destroy(PlayerGroup $playerGroup): JsonResponse
    {
        // Set all associated players' player_group_id to null before deleting
        $playerGroup->players()->update(['player_group_id' => null]);

        $playerGroup->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Player group deleted successfully',
        ]);
    }
}
