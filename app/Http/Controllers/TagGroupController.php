<?php

namespace App\Http\Controllers;

use App\Models\TagGroup;
use Illuminate\Http\Request;

class TagGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tagGroups = TagGroup::withCount('tags')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('tag-groups.index', compact('tagGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tag-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tag_groups,slug',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If slug is not provided, it will be auto-generated in the model
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        $tagGroup = TagGroup::create($validated);

        return redirect()
            ->route('tag-groups.show', $tagGroup)
            ->with('success', 'Tag group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TagGroup $tagGroup)
    {
        $tagGroup->loadCount('tags');
        $tagGroup->load(['tags' => function ($query) {
            $query->orderBy('name');
        }]);

        return view('tag-groups.show', compact('tagGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TagGroup $tagGroup)
    {
        return view('tag-groups.edit', compact('tagGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TagGroup $tagGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tag_groups,slug,' . $tagGroup->id,
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        $tagGroup->update($validated);

        return redirect()
            ->route('tag-groups.show', $tagGroup)
            ->with('success', 'Tag group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TagGroup $tagGroup)
    {
        // Set all associated tags' tag_group_id to null before deleting
        $tagGroup->tags()->update(['tag_group_id' => null]);

        $tagGroup->delete();

        return redirect()
            ->route('tag-groups.index')
            ->with('success', 'Tag group deleted successfully.');
    }
}
