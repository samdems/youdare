<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::query();

        // Filter by type if provided
        if (
            $request->has("type") &&
            in_array($request->get("type"), ["truth", "dare"])
        ) {
            $query->where("type", $request->get("type"));
        }

        // Filter by draft status
        if ($request->has("draft")) {
            $query->where("draft", $request->boolean("draft"));
        } else {
            // By default, only show published tasks
            $query->where("draft", false);
        }

        // Filter by spice rating
        if ($request->has("min_spice")) {
            $query->where("spice_rating", ">=", $request->get("min_spice"));
        }
        if ($request->has("max_spice")) {
            $query->where("spice_rating", "<=", $request->get("max_spice"));
        }

        // Search by description
        if ($request->has("search")) {
            $query->where(
                "description",
                "like",
                "%" . $request->get("search") . "%",
            );
        }

        // Sorting
        $sortBy = $request->get("sort_by", "created_at");
        $sortOrder = $request->get("sort_order", "desc");

        if (
            in_array($sortBy, [
                "created_at",
                "updated_at",
                "spice_rating",
                "type",
            ])
        ) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get("per_page", 15);
        $perPage = min(100, max(1, $perPage)); // Limit between 1 and 100

        $tasks = $query->paginate($perPage);

        return response()->json([
            "status" => "success",
            "data" => $tasks->items(),
            "meta" => [
                "current_page" => $tasks->currentPage(),
                "last_page" => $tasks->lastPage(),
                "per_page" => $tasks->perPage(),
                "total" => $tasks->total(),
                "from" => $tasks->firstItem(),
                "to" => $tasks->lastItem(),
            ],
            "links" => [
                "first" => $tasks->url(1),
                "last" => $tasks->url($tasks->lastPage()),
                "prev" => $tasks->previousPageUrl(),
                "next" => $tasks->nextPageUrl(),
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
            "type" => "required|in:truth,dare",
            "spice_rating" => "required|integer|min:1|max:5",
            "description" => "required|string|min:10|max:500",
            "draft" => "boolean",
            "tags" => "array",
            "tags.*" => "exists:tags,id",
        ]);

        $validated["draft"] = $request->boolean("draft");

        $task = Task::create($validated);

        // Attach tags if provided
        if ($request->has("tags")) {
            $task->tags()->attach($request->get("tags"));
        }

        return response()->json(
            [
                "status" => "success",
                "message" => "Task created successfully",
                "data" => $task,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data" => $task->load("tags")->append("spice_level"),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            "type" => "sometimes|required|in:truth,dare",
            "spice_rating" => "sometimes|required|integer|min:1|max:5",
            "description" => "sometimes|required|string|min:10|max:500",
            "draft" => "sometimes|boolean",
            "tags" => "sometimes|array",
            "tags.*" => "exists:tags,id",
        ]);

        if ($request->has("draft")) {
            $validated["draft"] = $request->boolean("draft");
        }

        $task->update($validated);

        // Sync tags if provided
        if ($request->has("tags")) {
            $task->tags()->sync($request->get("tags"));
        }

        return response()->json([
            "status" => "success",
            "message" => "Task updated successfully",
            "data" => $task->fresh()->append("spice_level"),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            "status" => "success",
            "message" => "Task deleted successfully",
        ]);
    }

    /**
     * Get a random task based on filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function random(Request $request): JsonResponse
    {
        $query = Task::query()->where("draft", false);

        // Filter by type if provided
        if (
            $request->has("type") &&
            in_array($request->get("type"), ["truth", "dare"])
        ) {
            $query->where("type", $request->get("type"));
        }

        // Filter by max spice rating
        if ($request->has("max_spice")) {
            $query->where("spice_rating", "<=", $request->get("max_spice"));
        }

        // Filter by min spice rating
        if ($request->has("min_spice")) {
            $query->where("spice_rating", ">=", $request->get("min_spice"));
        }

        $task = $query->inRandomOrder()->first();

        if (!$task) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "No tasks found matching your criteria",
                ],
                404,
            );
        }

        return response()->json([
            "status" => "success",
            "data" => $task->load("tags")->append("spice_level"),
        ]);
    }

    /**
     * Toggle the draft status of a task.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function toggleDraft(Task $task): JsonResponse
    {
        $task->draft = !$task->draft;
        $task->save();

        $status = $task->draft ? "draft" : "published";

        return response()->json([
            "status" => "success",
            "message" => "Task marked as {$status}",
            "data" => $task->fresh()->append("spice_level"),
        ]);
    }

    /**
     * Get task statistics.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            "total_tasks" => Task::count(),
            "published_tasks" => Task::where("draft", false)->count(),
            "draft_tasks" => Task::where("draft", true)->count(),
            "by_type" => [
                "truth" => Task::where("type", "truth")->count(),
                "dare" => Task::where("type", "dare")->count(),
            ],
            "by_spice_rating" => [],
            "average_spice_rating" => round(Task::avg("spice_rating"), 2),
        ];

        // Get count by spice rating
        for ($i = 1; $i <= 5; $i++) {
            $stats["by_spice_rating"][$i] = Task::where(
                "spice_rating",
                $i,
            )->count();
        }

        return response()->json([
            "status" => "success",
            "data" => $stats,
        ]);
    }

    /**
     * Bulk update tasks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "task_ids" => "required|array",
            "task_ids.*" => "exists:tasks,id",
            "updates" => "required|array",
            "updates.type" => "sometimes|in:truth,dare",
            "updates.spice_rating" => "sometimes|integer|min:1|max:5",
            "updates.draft" => "sometimes|boolean",
        ]);

        $updateData = $validated["updates"];
        if (isset($updateData["draft"])) {
            $updateData["draft"] = $request->boolean("updates.draft");
        }

        $updated = Task::whereIn("id", $validated["task_ids"])->update(
            $updateData,
        );

        return response()->json([
            "status" => "success",
            "message" => "{$updated} tasks updated successfully",
            "data" => [
                "updated_count" => $updated,
                "task_ids" => $validated["task_ids"],
            ],
        ]);
    }

    /**
     * Bulk delete tasks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "task_ids" => "required|array",
            "task_ids.*" => "exists:tasks,id",
        ]);

        $deleted = Task::whereIn("id", $validated["task_ids"])->delete();

        return response()->json([
            "status" => "success",
            "message" => "{$deleted} tasks deleted successfully",
            "data" => [
                "deleted_count" => $deleted,
            ],
        ]);
    }
}
