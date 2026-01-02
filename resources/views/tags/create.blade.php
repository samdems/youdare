@extends('layouts.app')

@section('title', 'Create New Tag')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('tags.index') }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to All Tags
        </a>
    </div>

    <!-- Create Tag Form -->
    <div class="card bg-base-100 shadow-xl">
        <div class="bg-gradient-to-r from-primary to-secondary text-white">
            <div class="card-body">
                <h1 class="card-title text-3xl">
                    <span class="text-4xl mr-2">🏷️</span>
                    Create New Tag
                </h1>
                <p class="text-white/90">Add a new category to organize content</p>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('tags.store') }}" method="POST">
                @csrf

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
                        value="{{ old('name') }}"
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

                <!-- Slug (optional) -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Slug (optional)</span>
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        class="input input-bordered font-mono @error('slug') input-error @enderror"
                        placeholder="e.g., family-friendly (auto-generated if left empty)"
                        value="{{ old('slug') }}"
                        maxlength="255"
                        pattern="[a-z0-9\-]+"
                    >
                    <label class="label">
                        @error('slug')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">URL-friendly version (lowercase, hyphens only). Leave empty to auto-generate.</span>
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
                    >{{ old('description') }}</textarea>
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
                            <option value="{{ $group->id }}" {{ old('tag_group_id') == $group->id ? 'selected' : '' }}>
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
                            {{ old('is_default') ? 'checked' : '' }}
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
                        <option value="none" {{ old('default_for_gender') == 'none' ? 'selected' : '' }}>None - Do not auto-assign</option>
                        <option value="male" {{ old('default_for_gender') == 'male' ? 'selected' : '' }}>Male Players Only</option>
                        <option value="female" {{ old('default_for_gender') == 'female' ? 'selected' : '' }}>Female Players Only</option>
                        <option value="both" {{ old('default_for_gender') == 'both' ? 'selected' : '' }}>Both Male and Female</option>
                    </select>
                    <label class="label">
                        @error('default_for_gender')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">Automatically assign this tag to players based on their gender selection</span>
                        @enderror
                    </label>
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
                        <option value="1" {{ old('min_spice_level', 1) == 1 ? 'selected' : '' }}>🌶️ Level 1 - Mild (Everyone)</option>
                        <option value="2" {{ old('min_spice_level') == 2 ? 'selected' : '' }}>🌶️🌶️ Level 2 - Medium</option>
                        <option value="3" {{ old('min_spice_level') == 3 ? 'selected' : '' }}>🌶️🌶️🌶️ Level 3 - Hot</option>
                        <option value="4" {{ old('min_spice_level') == 4 ? 'selected' : '' }}>🌶️🌶️🌶️🌶️ Level 4 - Very Hot</option>
                        <option value="5" {{ old('min_spice_level') == 5 ? 'selected' : '' }}>🌶️🌶️🌶️🌶️🌶️ Level 5 - Extreme</option>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Create Tag
                    </button>
                    <button type="submit" name="action" value="save_and_new" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create & Add Another
                    </button>
                    <a href="{{ route('tags.index') }}" class="btn btn-ghost md:col-span-2">
                        Cancel
                    </a>
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
            <h3 class="font-bold">💡 Tips for Creating Tags</h3>
            <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li><strong>Be Specific:</strong> Choose clear, unambiguous names</li>
                <li><strong>Consistent Naming:</strong> Follow a consistent naming pattern</li>
                <li><strong>User-Friendly:</strong> Use names that users will easily understand</li>
                <li><strong>Descriptions Help:</strong> Add descriptions to clarify the tag's purpose</li>
                <li><strong>Think Ahead:</strong> Consider how the tag will be used for filtering</li>
            </ul>
        </div>
    </div>

    <!-- Examples Section -->
    <div class="card bg-base-200 mt-6">
        <div class="card-body">
            <h3 class="card-title text-lg mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                Tag Examples
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="space-y-2">
                    <div class="font-semibold">Content Ratings:</div>
                    <ul class="list-disc list-inside space-y-1 text-base-content/70">
                        <li>Family Friendly</li>
                        <li>Adults Only</li>
                        <li>Teen Appropriate</li>
                    </ul>
                </div>
                <div class="space-y-2">
                    <div class="font-semibold">Activity Types:</div>
                    <ul class="list-disc list-inside space-y-1 text-base-content/70">
                        <li>Physical</li>
                        <li>Mental</li>
                        <li>Creative</li>
                    </ul>
                </div>
                <div class="space-y-2">
                    <div class="font-semibold">Contexts:</div>
                    <ul class="list-disc list-inside space-y-1 text-base-content/70">
                        <li>Party Mode</li>
                        <li>Romantic</li>
                        <li>Workplace Safe</li>
                    </ul>
                </div>
                <div class="space-y-2">
                    <div class="font-semibold">Intensity:</div>
                    <ul class="list-disc list-inside space-y-1 text-base-content/70">
                        <li>Extreme</li>
                        <li>Moderate</li>
                        <li>Light-hearted</li>
                    </ul>
                </div>
            </div>
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

        // Trigger on page load for old values
        textarea.dispatchEvent(new Event('input'));
    }

    // Auto-generate slug from name
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            // Only auto-generate if slug field is empty
            if (slugInput.value === '') {
                const slug = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Remove invalid chars
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-') // Replace multiple hyphens with single
                    .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens

                slugInput.value = slug;
            }
        });

        // Mark slug as manually edited if user types in it
        slugInput.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });

        // Only auto-generate if not manually edited
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                const slug = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');

                slugInput.value = slug;
            }
        });
    }
</script>
@endpush
@endsection
