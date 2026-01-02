@extends('layouts.app')

@section('title', 'Edit Tag')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('tags.show', $tag) }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Tag
        </a>
    </div>

    <!-- Edit Tag Form -->
    <div class="card bg-base-100 shadow-xl">
        <div class="bg-gradient-to-r from-primary to-secondary text-white">
            <div class="card-body">
                <h1 class="card-title text-3xl">
                    <span class="text-4xl mr-2">🏷️</span>
                    Edit Tag
                </h1>
                <p class="text-white/90">Update tag information</p>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('tags.update', $tag) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Tag Name <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="input input-bordered input-lg @error('name') input-error @enderror"
                        placeholder="e.g., Family Friendly, Adults Only, Party Mode"
                        value="{{ old('name', $tag->name) }}"
                        required
                        maxlength="255"
                    >
                    <label class="label">
                        @error('name')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">A clear, descriptive name for this category</span>
                        @enderror
                    </label>
                </div>

                <!-- Slug -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Slug</span>
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="input input-bordered font-mono @error('slug') input-error @enderror"
                        placeholder="e.g., family-friendly"
                        value="{{ old('slug', $tag->slug) }}"
                        maxlength="255"
                        pattern="[a-z0-9\-]+"
                    >
                    <label class="label">
                        @error('slug')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">URL-friendly version (lowercase, hyphens only)</span>
                        @enderror
                    </label>
                </div>

                <!-- Description -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Description</span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="textarea textarea-bordered @error('description') textarea-error @enderror"
                        placeholder="Describe what type of content this tag is for..."
                        maxlength="1000"
                    >{{ old('description', $tag->description) }}</textarea>
                    <label class="label">
                        @error('description')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">Help users understand when to use this tag (optional)</span>
                        @enderror
                        <span class="label-text-alt" id="charCount">0 / 1000</span>
                    </label>
                </div>

                <!-- Tag Group -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Tag Group</span>
                    </label>
                    <select
                        name="tag_group_id"
                        id="tag_group_id"
                        class="select select-bordered @error('tag_group_id') select-error @enderror"
                    >
                        <option value="">No Group (Ungrouped)</option>
                        @foreach(\App\Models\TagGroup::orderBy('sort_order')->orderBy('name')->get() as $group)
                            <option value="{{ $group->id }}" {{ old('tag_group_id', $tag->tag_group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="label">
                        @error('tag_group_id')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">Organize this tag into a group for better organization in game setup</span>
                        @enderror
                    </label>
                </div>

                <!-- Default Tag Checkbox -->
                <div class="form-control mb-6">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input
                            type="checkbox"
                            name="is_default"
                            id="is_default"
                            class="checkbox checkbox-primary"
                            value="1"
                            {{ old('is_default', $tag->is_default) ? 'checked' : '' }}
                        >
                        <div>
                            <span class="label-text font-semibold">Set as Default Tag</span>
                            <p class="text-sm opacity-70 mt-1">
                                Default tags are automatically assigned to all players when they join a game
                            </p>
                        </div>
                    </label>
                    @error('is_default')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Default for Gender -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Auto-assign for Gender</span>
                    </label>
                    <select
                        name="default_for_gender"
                        id="default_for_gender"
                        class="select select-bordered @error('default_for_gender') select-error @enderror"
                    >
                        <option value="none" {{ old('default_for_gender', $tag->default_for_gender) == 'none' ? 'selected' : '' }}>None - Do not auto-assign</option>
                        <option value="male" {{ old('default_for_gender', $tag->default_for_gender) == 'male' ? 'selected' : '' }}>Male Players Only</option>
                        <option value="female" {{ old('default_for_gender', $tag->default_for_gender) == 'female' ? 'selected' : '' }}>Female Players Only</option>
                        <option value="both" {{ old('default_for_gender', $tag->default_for_gender) == 'both' ? 'selected' : '' }}>Both Male and Female</option>
                    </select>
                    <label class="label">
                        @error('default_for_gender')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">Automatically assign this tag to players based on their gender selection</span>
                        @enderror
                    </label>
                </div>

                <!-- Current Usage Info -->
                <div class="alert alert-info mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm">
                        <div class="font-semibold mb-1">Current Usage</div>
                        <div class="flex gap-4">
                            <span>📋 {{ $tag->tasks_count ?? $tag->tasks()->count() }} {{ Str::plural('task', $tag->tasks_count ?? $tag->tasks()->count()) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Minimum Spice Level -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Minimum Spice Level <span class="text-error">*</span></span>
                    </label>
                    <select
                        name="min_spice_level"
                        id="min_spice_level"
                        class="select select-bordered @error('min_spice_level') select-error @enderror"
                        required
                    >
                        <option value="1" {{ old('min_spice_level', $tag->min_spice_level) == 1 ? 'selected' : '' }}>🌶️ Level 1 - Mild (Everyone)</option>
                        <option value="2" {{ old('min_spice_level', $tag->min_spice_level) == 2 ? 'selected' : '' }}>🌶️🌶️ Level 2 - Medium</option>
                        <option value="3" {{ old('min_spice_level', $tag->min_spice_level) == 3 ? 'selected' : '' }}>🌶️🌶️🌶️ Level 3 - Hot</option>
                        <option value="4" {{ old('min_spice_level', $tag->min_spice_level) == 4 ? 'selected' : '' }}>🌶️🌶️🌶️🌶️ Level 4 - Very Hot</option>
                        <option value="5" {{ old('min_spice_level', $tag->min_spice_level) == 5 ? 'selected' : '' }}>🌶️🌶️🌶️🌶️🌶️ Level 5 - Extreme</option>
                    </select>
                    <label class="label">
                        @error('min_spice_level')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">This tag will only be available to games with this spice level or higher</span>
                        @enderror
                    </label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="btn btn-primary flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Update Tag
                    </button>
                    <a href="{{ route('tags.show', $tag) }}" class="btn btn-ghost flex-1">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card bg-error/10 border-2 border-error/30 shadow-xl mt-6">
        <div class="card-body">
            <h3 class="card-title text-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Danger Zone
            </h3>
            <p class="text-base-content/70 mb-4">
                Deleting this tag will remove it from all {{ $tag->tasks()->count() }} tasks. This action cannot be undone.
            </p>
            <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tag? This will remove it from all tasks and users. This action cannot be undone!');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Delete This Tag
                </button>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="alert alert-warning mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-6 h-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <h3 class="font-bold">⚠️ Important Notes</h3>
            <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li><strong>Changing the name</strong> will update it everywhere</li>
                <li><strong>Changing the slug</strong> may break existing links</li>
                <li><strong>Existing associations</strong> with tasks will be preserved</li>
                <li><strong>Tasks will retain</strong> the updated tag automatically</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Character counter for description
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + ' / 1000';

            if (count > 900) {
                charCount.classList.add('text-warning');
            } else {
                charCount.classList.remove('text-warning');
            }
        });

        // Trigger on page load
        textarea.dispatchEvent(new Event('input'));
    }

    // Slug validation
    if (slugInput) {
        slugInput.addEventListener('input', function() {
            this.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        });
    }

    // Optional: Auto-update slug when name changes (only if slug matches current pattern)
    if (nameInput && slugInput) {
        const originalSlug = slugInput.value;

        nameInput.addEventListener('input', function() {
            // Check if slug seems to be auto-generated from the current name
            const nameSlug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');

            // Only update if slug hasn't been manually changed
            if (slugInput.value === originalSlug || slugInput.value === nameSlug) {
                slugInput.value = nameSlug;
            }
        });
    }
</script>
@endpush
@endsection
