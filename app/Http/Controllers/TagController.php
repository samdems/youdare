<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::withCount(["tasks"])
            ->orderBy("name")
            ->paginate(20);

        return view("tags.index", compact("tags"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("tags.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        // Convert checkbox value to boolean
        $validated["is_default"] = $request->has("is_default");

        // Set default_for_gender to 'none' if not provided
        $validated["default_for_gender"] = $request->input(
            "default_for_gender",
            "none",
        );

        $tag = Tag::create($validated);

        // Check if user wants to create another
        if ($request->input("action") === "save_and_new") {
            return redirect()
                ->route("tags.create")
                ->with(
                    "success",
                    "Tag created successfully! Create another one.",
                );
        }

        return redirect()
            ->route("tags.show", $tag)
            ->with("success", "Tag created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        $tag->loadCount(["tasks"]);

        return view("tags.show", compact("tag"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        return view("tags.edit", compact("tag"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255|unique:tags,name," . $tag->id,
            "slug" => "nullable|string|max:255|unique:tags,slug," . $tag->id,
            "description" => "nullable|string|max:1000",
            "tag_group_id" => "nullable|integer|exists:tag_groups,id",
            "is_default" => "nullable|boolean",
            "default_for_gender" => "nullable|in:none,male,female,both",
            "min_spice_level" => "required|integer|min:1|max:5",
        ]);

        // Convert checkbox value to boolean
        $validated["is_default"] = $request->has("is_default");

        // Set default_for_gender to 'none' if not provided
        $validated["default_for_gender"] = $request->input(
            "default_for_gender",
            "none",
        );

        $tag->update($validated);

        return redirect()
            ->route("tags.show", $tag)
            ->with("success", "Tag updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()
            ->route("tags.index")
            ->with("success", "Tag deleted successfully!");
    }
}
