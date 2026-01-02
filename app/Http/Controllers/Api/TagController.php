<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tag::query();

        // Search by name
        if ($request->has("search")) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    "name",
                    "like",
                    "%" . $request->get("search") . "%",
                )->orWhere("slug", "like", "%" . $request->get("search") . "%");
            });
        }

        // Include counts
        if ($request->boolean("with_counts")) {
            $query->withCount(["tasks"]);
        }

        // Sorting
        $sortBy = $request->get("sort_by", "name");
        $sortOrder = $request->get("sort_order", "asc");

        if (in_array($sortBy, ["name", "slug", "created_at", "updated_at"])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get("per_page", 20);
        $perPage = min(100, max(1, $perPage));

        $tags = $query->paginate($perPage);

        return response()->json([
            "status" => "success",
            "data" => $tags->items(),
            "meta" => [
                "current_page" => $tags->currentPage(),
                "last_page" => $tags->lastPage(),
                "per_page" => $tags->perPage(),
                "total" => $tags->total(),
                "from" => $tags->firstItem(),
                "to" => $tags->lastItem(),
            ],
            "links" => [
                "first" => $tags->url(1),
                "last" => $tags->url($tags->lastPage()),
                "prev" => $tags->previousPageUrl(),
                "next" => $tags->nextPageUrl(),
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
            "name" => "required|string|max:255|unique:tags,name",
            "slug" => "nullable|string|max:255|unique:tags,slug",
            "description" => "nullable|string|max:1000",
            "tag_group_id" => "nullable|integer|exists:tag_groups,id",
            "is_default" => "nullable|boolean",
            "default_for_gender" => "nullable|in:none,male,female,both",
            "min_spice_level" => "required|integer|min:1|max:5",
        ]);

        // Convert to boolean
        $validated["is_default"] = $request->input("is_default", false);

        // Set default_for_gender to 'none' if not provided
        $validated["default_for_gender"] = $request->input(
            "default_for_gender",
            "none",
        );

        $tag = Tag::create($validated);

        return response()->json(
            [
                "status" => "success",
                "message" => "Tag created successfully",
                "data" => $tag,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Tag $tag
     * @return JsonResponse
     */
    public function show(Tag $tag): JsonResponse
    {
        $tag->loadCount(["tasks"]);

        return response()->json([
            "status" => "success",
            "data" => $tag,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Tag $tag
     * @return JsonResponse
     */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        $validated = $request->validate([
            "name" =>
                "sometimes|required|string|max:255|unique:tags,name," .
                $tag->id,
            "slug" =>
                "sometimes|nullable|string|max:255|unique:tags,slug," .
                $tag->id,
            "description" => "sometimes|nullable|string|max:1000",
            "tag_group_id" => "sometimes|nullable|integer|exists:tag_groups,id",
            "is_default" => "sometimes|nullable|boolean",
            "default_for_gender" =>
                "sometimes|nullable|in:none,male,female,both",
            "min_spice_level" => "sometimes|required|integer|min:1|max:5",
        ]);

        // Convert to boolean if present
        if ($request->has("is_default")) {
            $validated["is_default"] = $request->input("is_default", false);
        }

        // Set default_for_gender if present
        if ($request->has("default_for_gender")) {
            $validated["default_for_gender"] = $request->input(
                "default_for_gender",
                "none",
            );
        }

        $tag->update($validated);

        return response()->json([
            "status" => "success",
            "message" => "Tag updated successfully",
            "data" => $tag->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     * @return JsonResponse
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json([
            "status" => "success",
            "message" => "Tag deleted successfully",
        ]);
    }

    /**
     * Get tags grouped by tag groups.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function grouped(Request $request): JsonResponse
    {
        // Get tag groups with their tags
        $tagGroups = TagGroup::with([
            "tags" => function ($query) use ($request) {
                // Apply filters if provided
                if ($request->has("min_spice_level")) {
                    $query->where(
                        "min_spice_level",
                        "<=",
                        $request->get("min_spice_level"),
                    );
                }
                // Eager load tagGroup relationship on each tag
                $query->with("tagGroup");
            },
        ])
            ->orderBy("sort_order")
            ->orderBy("name")
            ->get();

        // Also get ungrouped tags
        $ungroupedTagsQuery = Tag::whereNull("tag_group_id")->with("tagGroup");

        if ($request->has("min_spice_level")) {
            $ungroupedTagsQuery->where(
                "min_spice_level",
                "<=",
                $request->get("min_spice_level"),
            );
        }

        $ungroupedTags = $ungroupedTagsQuery->orderBy("name")->get();

        // Format the response
        $groupedData = $tagGroups->map(function ($group) {
            return [
                "id" => $group->id,
                "name" => $group->name,
                "slug" => $group->slug,
                "description" => $group->description,
                "sort_order" => $group->sort_order,
                "tags" => $group->tags,
            ];
        });

        // Add ungrouped tags if any exist
        if ($ungroupedTags->isNotEmpty()) {
            $groupedData->push([
                "id" => null,
                "name" => "Other",
                "slug" => "other",
                "description" => "Ungrouped tags",
                "sort_order" => 999,
                "tags" => $ungroupedTags,
            ]);
        }

        return response()->json([
            "status" => "success",
            "data" => $groupedData,
        ]);
    }
}
