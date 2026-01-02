<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TagGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = TagGroup::query();

        // Include counts
        if ($request->boolean('with_counts')) {
            $query->withCount(['tags']);
        }

        // Include tags
        if ($request->boolean('with_tags')) {
            $query->with(['tags' => function ($q) {
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

        $tagGroups = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $tagGroups->items(),
            'meta' => [
                'current_page' => $tagGroups->currentPage(),
                'last_page' => $tagGroups->lastPage(),
                'per_page' => $tagGroups->perPage(),
                'total' => $tagGroups->total(),
                'from' => $tagGroups->firstItem(),
                'to' => $tagGroups->lastItem(),
            ],
            'links' => [
                'first' => $tagGroups->url(1),
                'last' => $tagGroups->url($tagGroups->lastPage()),
                'prev' => $tagGroups->previousPageUrl(),
                'next' => $tagGroups->nextPageUrl(),
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
            'slug' => 'nullable|string|max:255|unique:tag_groups,slug',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Set default sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        $tagGroup = TagGroup::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Tag group created successfully',
            'data' => $tagGroup,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param TagGroup $tagGroup
     * @return JsonResponse
     */
    public function show(TagGroup $tagGroup): JsonResponse
    {
        $tagGroup->loadCount(['tags']);
        $tagGroup->load(['tags' => function ($query) {
            $query->orderBy('name');
        }]);

        return response()->json([
            'status' => 'success',
            'data' => $tagGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param TagGroup $tagGroup
     * @return JsonResponse
     */
    public function update(Request $request, TagGroup $tagGroup): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:tag_groups,slug,' . $tagGroup->id,
            'description' => 'sometimes|nullable|string|max:1000',
            'sort_order' => 'sometimes|nullable|integer|min:0',
        ]);

        $tagGroup->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Tag group updated successfully',
            'data' => $tagGroup->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TagGroup $tagGroup
     * @return JsonResponse
     */
    public function destroy(TagGroup $tagGroup): JsonResponse
    {
        // Set all associated tags' tag_group_id to null before deleting
        $tagGroup->tags()->update(['tag_group_id' => null]);

        $tagGroup->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tag group deleted successfully',
        ]);
    }
}
