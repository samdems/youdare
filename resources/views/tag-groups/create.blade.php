@extends('layouts.app')

@section('title', 'Create Tag Group')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Create New Tag Group</h1>
        <p class="text-base-content/70 mt-2">Add a new tag group to organize your tags</p>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <form action="{{ route('tag-groups.store') }}" method="POST">
                @csrf

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
                        value="{{ old('name') }}"
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

                <!-- Slug (optional) -->
                <div class="form-control mb-4">
                    <label class="label" for="slug">
                        <span class="label-text font-semibold">Slug</span>
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="input input-bordered @error('slug') input-error @enderror"
                        value="{{ old('slug') }}"
                        placeholder="e.g., content-type (auto-generated if left empty)"
                    >
                    @error('slug')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">URL-friendly identifier (auto-generated from name if empty)</span>
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
                    >{{ old('description') }}</textarea>
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
                        value="{{ old('sort_order', 0) }}"
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
                    <a href="{{ route('tag-groups.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Create Tag Group
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="alert alert-info mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="font-bold">Tips for Creating Tag Groups</h3>
            <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li>Use clear, descriptive names that indicate the category (e.g., "Content Type", "Gender")</li>
                <li>Write helpful descriptions to guide users on which tags belong in this group</li>
                <li>Use sort_order to control how groups appear in the game setup (lower = appears first)</li>
                <li>After creating the group, you can assign tags to it by editing individual tags</li>
            </ul>
        </div>
    </div>
</div>
@endsection
