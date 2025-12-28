<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Tag;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        // Filter by type if provided
        if ($request->filled("type")) {
            $query->where("type", $request->get("type"));
        }

        // Filter by draft status
        if ($request->filled("draft")) {
            $query->where("draft", $request->boolean("draft"));
        }

        // Filter by spice rating
        if ($request->filled("min_spice")) {
            $query->where("spice_rating", ">=", $request->get("min_spice"));
        }
        if ($request->filled("max_spice")) {
            $query->where("spice_rating", "<=", $request->get("max_spice"));
        }

        // Filter by tags if provided
        if ($request->filled("tags")) {
            $tagIds = $request->get("tags");
            if (!is_array($tagIds)) {
                $tagIds = [$tagIds];
            }
            $query->whereHas("tags", function ($q) use ($tagIds) {
                $q->whereIn("tags.id", $tagIds);
            });
        }

        // Order by creation date by default
        $tasks = $query->orderBy("created_at", "desc")->paginate(15);

        // Get all tags for the filter dropdown
        $allTags = Tag::orderBy("name")->get();

        return view("tasks.index", compact("tasks", "allTags"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("tasks.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "type" => "required|in:truth,dare",
            "spice_rating" => "required|integer|min:1|max:5",
            "description" => "required|string|min:10|max:500",
            "draft" => "boolean",
            "tags" => "array",
            "tags.*" => "exists:tags,id",
            "tags_to_remove" => "array",
            "tags_to_remove.*" => "exists:tags,id",
            "cant_have_tags" => "array",
            "cant_have_tags.*" => "exists:tags,id",
            "tags_to_add" => "array",
            "tags_to_add.*" => "exists:tags,id",
            "someone_tags" => "array",
            "someone_tags.*" => "exists:tags,id",
            "someone_cant_have_tags" => "array",
            "someone_cant_have_tags.*" => "exists:tags,id",
            "someone_gender" => "nullable|in:any,same,other",
        ]);

        $validated["draft"] = $request->boolean("draft");

        // Ensure array fields are set to empty arrays if not present in request
        $validated["tags_to_remove"] = $request->input("tags_to_remove", []);
        $validated["cant_have_tags"] = $request->input("cant_have_tags", []);
        $validated["tags_to_add"] = $request->input("tags_to_add", []);
        $validated["someone_tags"] = $request->input("someone_tags", []);
        $validated["someone_cant_have_tags"] = $request->input(
            "someone_cant_have_tags",
            [],
        );

        $task = Task::create($validated);

        // Sync tags (including empty array to allow clearing all tags)
        $task->tags()->sync($request->input("tags", []));

        // Check if user wants to create another
        if ($request->input("action") === "save_and_new") {
            return redirect()
                ->route("tasks.create")
                ->with(
                    "success",
                    "Task created successfully! Create another one.",
                );
        }

        return redirect()
            ->route("tasks.show", $task)
            ->with("success", "Task created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Refresh the task to ensure we have the latest data
        $task = $task->fresh();

        // Find the next task (older in creation date)
        $nextTask = Task::where("created_at", "<", $task->created_at)
            ->orderBy("created_at", "desc")
            ->first();

        return view("tasks.show", compact("task", "nextTask"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return view("tasks.edit", compact("task"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            "type" => "required|in:truth,dare",
            "spice_rating" => "required|integer|min:1|max:5",
            "description" => "required|string|min:10|max:500",
            "draft" => "boolean",
            "tags" => "array",
            "tags.*" => "exists:tags,id",
            "tags_to_remove" => "array",
            "tags_to_remove.*" => "exists:tags,id",
            "cant_have_tags" => "array",
            "cant_have_tags.*" => "exists:tags,id",
            "tags_to_add" => "array",
            "tags_to_add.*" => "exists:tags,id",
            "someone_tags" => "array",
            "someone_tags.*" => "exists:tags,id",
            "someone_cant_have_tags" => "array",
            "someone_cant_have_tags.*" => "exists:tags,id",
            "someone_gender" => "nullable|in:any,same,other",
        ]);

        $validated["draft"] = $request->boolean("draft");

        // Ensure array fields are set to empty arrays if not present in request
        $validated["tags_to_remove"] = $request->input("tags_to_remove", []);
        $validated["cant_have_tags"] = $request->input("cant_have_tags", []);
        $validated["tags_to_add"] = $request->input("tags_to_add", []);
        $validated["someone_tags"] = $request->input("someone_tags", []);
        $validated["someone_cant_have_tags"] = $request->input(
            "someone_cant_have_tags",
            [],
        );

        $task->update($validated);

        // Sync tags (including empty array to allow clearing all tags)
        $task->tags()->sync($request->input("tags", []));

        return redirect()
            ->route("tasks.show", $task)
            ->with("success", "Task updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route("tasks.index")
            ->with("success", "Task deleted successfully!");
    }

    /**
     * Get a random task based on filters.
     */
    public function random(Request $request)
    {
        $query = Task::query()->where("draft", false);

        // Filter by type if provided
        if ($request->filled("type")) {
            $query->where("type", $request->get("type"));
        }

        // Filter by max spice rating
        if ($request->filled("max_spice")) {
            $query->where("spice_rating", "<=", $request->get("max_spice"));
        }

        $task = $query->inRandomOrder()->first();

        if (!$task) {
            return redirect()
                ->route("tasks.index")
                ->with("error", "No tasks found matching your criteria.");
        }

        return view("tasks.show", compact("task"));
    }

    /**
     * Toggle the draft status of a task.
     */
    public function toggleDraft(Task $task)
    {
        $task->draft = !$task->draft;
        $task->save();

        $status = $task->draft ? "draft" : "published";

        return redirect()
            ->back()
            ->with("success", "Task marked as {$status}!");
    }
}
