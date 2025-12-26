@extends('layouts.app')

@section('title', 'Create New Task')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('tasks.index') }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to All Tasks
        </a>
    </div>

    <!-- Create Task Form -->
    <div class="card bg-base-100 shadow-xl">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
            <div class="card-body">
                <h1 class="card-title text-3xl">Create New Task</h1>
                <p class="text-indigo-100">Add a new truth or dare to the collection</p>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf

                <!-- Type Selection -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Task Type <span class="text-error">*</span></span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="truth" class="hidden peer" {{ old('type') === 'truth' ? 'checked' : '' }} required>
                            <div class="card border-2 border-base-300 peer-checked:border-info peer-checked:bg-info/10 hover:border-info/50 transition-all">
                                <div class="card-body flex-row items-center gap-4">
                                    <span class="text-4xl">💬</span>
                                    <div>
                                        <div class="font-semibold text-lg">Truth</div>
                                        <div class="text-sm opacity-70">A question to answer</div>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="dare" class="hidden peer" {{ old('type') === 'dare' ? 'checked' : '' }} required>
                            <div class="card border-2 border-base-300 peer-checked:border-secondary peer-checked:bg-secondary/10 hover:border-secondary/50 transition-all">
                                <div class="card-body flex-row items-center gap-4">
                                    <span class="text-4xl">🎯</span>
                                    <div>
                                        <div class="font-semibold text-lg">Dare</div>
                                        <div class="text-sm opacity-70">A challenge to complete</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Description <span class="text-error">*</span></span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="5"
                        class="textarea textarea-bordered textarea-lg @error('description') textarea-error @enderror"
                        placeholder="Enter the task description (10-500 characters)"
                        required
                        minlength="10"
                        maxlength="500"
                    >{{ old('description') }}</textarea>
                    <label class="label">
                        @error('description')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @else
                            <span class="label-text-alt">Minimum 10 characters, maximum 500 characters</span>
                        @enderror
                        <span class="label-text-alt" id="charCount">0 / 500</span>
                    </label>
                </div>

                <!-- Spice Rating -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Spice Rating <span class="text-error">*</span></span>
                    </label>
                    <p class="text-sm opacity-70 mb-3">How intense or daring is this task?</p>

                    <div class="space-y-3">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="spice_rating" value="{{ $i }}" class="hidden peer" {{ old('spice_rating') == $i ? 'checked' : '' }} required>
                                <div class="card border-2 border-base-300 peer-checked:border-primary peer-checked:bg-primary/10 hover:border-primary/50 transition-all bg-spice-{{ $i }}">
                                    <div class="card-body py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="text-2xl">{{ str_repeat('🌶️', $i) }}</span>
                                                <div>
                                                    <div class="font-semibold spice-{{ $i }}">
                                                        Level {{ $i }} -
                                                        @if($i === 1) Mild
                                                        @elseif($i === 2) Medium
                                                        @elseif($i === 3) Hot
                                                        @elseif($i === 4) Extra Hot
                                                        @elseif($i === 5) Extreme
                                                        @endif
                                                    </div>
                                                    <div class="text-sm opacity-70">
                                                        @if($i === 1) Safe for everyone
                                                        @elseif($i === 2) Slightly embarrassing
                                                        @elseif($i === 3) Moderately challenging
                                                        @elseif($i === 4) Very daring
                                                        @elseif($i === 5) Extremely bold
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-base-300 peer-checked:border-primary peer-checked:bg-primary"></div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endfor
                    </div>
                    @error('spice_rating')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Tags Selection -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Tags</span>
                    </label>
                    <p class="text-sm opacity-70 mb-3">
                        Select tags to categorize this task. Tags help organize and filter content.
                    </p>

                    @php
                        $tags = \App\Models\Tag::orderBy('name')->get();
                    @endphp

                    @if($tags->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($tags as $tag)
                                <label class="cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="tags[]"
                                        value="{{ $tag->id }}"
                                        class="hidden peer"
                                        {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) ? 'checked' : '' }}
                                    >
                                    <div class="card border-2 border-base-300 peer-checked:border-primary peer-checked:bg-primary/10 hover:border-primary/50 transition-all">
                                        <div class="card-body p-3 flex-row items-center gap-3">
                                            <span class="text-2xl">🏷️</span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold truncate">{{ $tag->name }}</div>
                                                @if($tag->description)
                                                    <div class="text-xs opacity-70 truncate">{{ $tag->description }}</div>
                                                @endif
                                            </div>
                                            <div class="w-5 h-5 rounded border-2 border-base-300 peer-checked:border-primary peer-checked:bg-primary flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white hidden peer-checked:block" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>No tags available. <a href="{{ route('tags.create') }}" class="link">Create tags</a> to categorize content.</span>
                        </div>
                    @endif

                    @error('tags')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Tags to Remove Selection -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Tags to Remove on Completion</span>
                    </label>
                    <p class="text-sm opacity-70 mb-3">
                        Select tags that should be removed from the player when they complete this task. This is useful for one-time tasks or progressive challenges.
                    </p>

                    @if($tags->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($tags as $tag)
                                <label class="cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="tags_to_remove[]"
                                        value="{{ $tag->id }}"
                                        class="hidden peer"
                                        {{ (is_array(old('tags_to_remove')) && in_array($tag->id, old('tags_to_remove'))) ? 'checked' : '' }}
                                    >
                                    <div class="card border-2 border-base-300 peer-checked:border-error peer-checked:bg-error/10 hover:border-error/50 transition-all">
                                        <div class="card-body p-3 flex-row items-center gap-3">
                                            <span class="text-2xl">🗑️</span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold truncate">{{ $tag->name }}</div>
                                                @if($tag->description)
                                                    <div class="text-xs opacity-70 truncate">{{ $tag->description }}</div>
                                                @endif
                                            </div>
                                            <div class="w-5 h-5 rounded border-2 border-base-300 peer-checked:border-error peer-checked:bg-error flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white hidden peer-checked:block" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>No tags available.</span>
                        </div>
                    @endif

                    @error('tags_to_remove')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Can't Have Tags Selection -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Can't Have Tags (Negative Filter)</span>
                    </label>
                    <p class="text-sm opacity-70 mb-3">
                        Select tags that players must <strong>NOT</strong> have for this task to appear. This is the opposite of regular tags - if a player has any of these tags, they won't see this task.
                    </p>

                    @if($tags->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($tags as $tag)
                                <label class="cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="cant_have_tags[]"
                                        value="{{ $tag->id }}"
                                        class="hidden peer"
                                        {{ (is_array(old('cant_have_tags')) && in_array($tag->id, old('cant_have_tags'))) ? 'checked' : '' }}
                                    >
                                    <div class="card border-2 border-base-300 peer-checked:border-warning peer-checked:bg-warning/10 hover:border-warning/50 transition-all">
                                        <div class="card-body p-3 flex-row items-center gap-3">
                                            <span class="text-2xl">🚫</span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold truncate">{{ $tag->name }}</div>
                                                @if($tag->description)
                                                    <div class="text-xs opacity-70 truncate">{{ $tag->description }}</div>
                                                @endif
                                            </div>
                                            <div class="w-5 h-5 rounded border-2 border-base-300 peer-checked:border-warning peer-checked:bg-warning flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white hidden peer-checked:block" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>No tags available.</span>
                        </div>
                    @endif

                    @error('cant_have_tags')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Tags to Add on Completion Selection -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Tags to Add on Completion</span>
                    </label>
                    <p class="text-sm opacity-70 mb-3">
                        Select tags that should be <strong>added</strong> to the player when they complete this task. Use this to reward players, mark progression, or unlock new content tiers.
                    </p>

                    @if($tags->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($tags as $tag)
                                <label class="cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="tags_to_add[]"
                                        value="{{ $tag->id }}"
                                        class="hidden peer"
                                        {{ (is_array(old('tags_to_add')) && in_array($tag->id, old('tags_to_add'))) ? 'checked' : '' }}
                                    >
                                    <div class="card border-2 border-base-300 peer-checked:border-success peer-checked:bg-success/10 hover:border-success/50 transition-all">
                                        <div class="card-body p-3 flex-row items-center gap-3">
                                            <span class="text-2xl">✨</span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold truncate">{{ $tag->name }}</div>
                                                @if($tag->description)
                                                    <div class="text-xs opacity-70 truncate">{{ $tag->description }}</div>
                                                @endif
                                            </div>
                                            <div class="w-5 h-5 rounded border-2 border-base-300 peer-checked:border-success peer-checked:bg-success flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white hidden peer-checked:block" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>No tags available.</span>
                        </div>
                    @endif

                    @error('tags_to_add')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Draft Status -->
                <div class="form-control mb-8">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input
                            type="checkbox"
                            name="draft"
                            value="1"
                            class="checkbox checkbox-primary"
                            {{ old('draft') ? 'checked' : '' }}
                        >
                        <span class="label-text">
                            Save as draft (not visible in random task selection)
                        </span>
                    </label>
                    @error('draft')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Create Task
                    </button>
                    <button type="submit" name="action" value="save_and_new" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create & Add Another
                    </button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-ghost md:col-span-2">
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
            <h3 class="font-bold">💡 Tips for Creating Great Tasks</h3>
            <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li>Keep descriptions clear and concise</li>
                <li>Be creative and fun, but respectful</li>
                <li>Choose the appropriate spice level based on intensity</li>
                <li>Use drafts to save work-in-progress tasks</li>
                <li>Truth tasks should be interesting questions</li>
                <li>Dare tasks should be achievable challenges</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Character counter for description
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');

    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + ' / 500';

            if (count < 10) {
                charCount.classList.add('text-error');
                charCount.classList.remove('text-warning');
            } else if (count > 450) {
                charCount.classList.add('text-warning');
                charCount.classList.remove('text-error');
            } else {
                charCount.classList.remove('text-error', 'text-warning');
            }
        });

        // Trigger on page load for old values
        textarea.dispatchEvent(new Event('input'));
    }
</script>
@endpush
@endsection
