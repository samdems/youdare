@extends('layouts.app')

@section('title', 'Edit Tag Group')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Edit Tag Group</h1>
        <p class="text-base-content/70 mt-2">Update the tag group details</p>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <form action="{{ route('tag-groups.update', $tagGroup) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="form-control mb-4">
                    <label class="label" for="name">
                        <span class="label-text font-semibold">Name <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="input input-bordered @error('name') input-error @enderror"
                        value="{{ old('name', $tagGroup->name) }}"
                        required
                        placeholder="e.g., Content Type, Activity Style"
                    >
                    @error('name')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">The display name for this tag group</span>
                    </label>
                </div>

                <!-- Slug -->
                <div class="form-control mb-4">
                    <label class="label" for="slug">
                        <span class="label-text font-semibold">Slug</span>
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="input input-bordered @error('slug') input-error @enderror"
                        value="{{ old('slug', $tagGroup->slug) }}"
                        placeholder="e.g., content-type"
                    >
                    @error('slug')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">URL-friendly identifier</span>
                    </label>
                </div>

                <!-- Description -->
                <div class="form-control mb-4">
                    <label class="label" for="description">
                        <span class="label-text font-semibold">Description</span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        class="textarea textarea-bordered h-24 @error('description') textarea-error @enderror"
                        placeholder="Describe the purpose of this tag group..."
                    >{{ old('description', $tagGroup->description) }}</textarea>
                    @error('description')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Help users understand what tags belong in this group</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div class="form-control mb-6">
                    <label class="label" for="sort_order">
                        <span class="label-text font-semibold">Sort Order</span>
                    </label>
                    <input
                        type="number"
                        name="sort_order"
                        id="sort_order"
                        class="input input-bordered @error('sort_order') input-error @enderror"
                        value="{{ old('sort_order', $tagGroup->sort_order) }}"
                        min="0"
                        placeholder="0"
                    >
                    @error('sort_order')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Controls the display order (lower numbers appear first)</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 justify-end pt-6 border-t border-base-300">
                    <a href="{{ route('tag-groups.show', $tagGroup) }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Tag Group
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card bg-base-100 shadow-xl mt-6 border-2 border-error">
        <div class="card-body">
            <h3 class="card-title text-error">Danger Zone</h3>
            <p class="text-base-content/70 text-sm mb-4">
                Deleting this tag group will unassign all tags from it, but the tags themselves will not be deleted.
            </p>
            <form action="{{ route('tag-groups.destroy', $tagGroup) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tag group? All tags in this group will be unassigned but not deleted.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Tag Group
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
