@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('tasks.show', $task) }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Task
        </a>
    </div>

    <!-- Edit Task Form -->
    <div class="card bg-base-100 shadow-xl">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
            <div class="card-body">
                <h1 class="card-title text-3xl">Edit Task</h1>
                <p class="text-indigo-100">Update the task details</p>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('tasks.update', $task) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Type Selection -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Task Type <span class="text-error">*</span></span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="truth" class="hidden peer" {{ old('type', $task->type) === 'truth' ? 'checked' : '' }} required>
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
                            <input type="radio" name="type" value="dare" class="hidden peer" {{ old('type', $task->type) === 'dare' ? 'checked' : '' }} required>
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
                    <div class="bg-base-200 border border-base-300 rounded-lg p-4 mb-3">
                        <div class="text-sm text-base-content">
                            <div class="font-semibold mb-2">Template Variables Available:</div>
                            <div class="space-y-1">
                                <div><code class="bg-base-300 px-1 rounded">@{{someone}}</code> - Random player (use filters below to specify requirements)</div>
                                <div><code class="bg-base-300 px-1 rounded">@{{number_of_players}}</code> - Total number of players</div>
                                <div><code class="bg-base-300 px-1 rounded">@{{number_of_players/2}}</code> - Number of players divided by 2 (rounded)</div>
                            </div>
                        </div>
                    </div>
                    <textarea
                        name="description"
                        id="description"
                        rows="5"
                        class="textarea textarea-bordered textarea-lg @error('description') textarea-error @enderror"
                        placeholder="e.g., Give @{{someone}} a compliment or Tell @{{someone}} your biggest secret"
                        required
                        minlength="10"
                        maxlength="500"
                    >{{ old('description', $task->description) }}</textarea>
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
                                <input type="radio" name="spice_rating" value="{{ $i }}" class="hidden peer" {{ old('spice_rating', $task->spice_rating) == $i ? 'checked' : '' }} required>
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

                <!-- Tag Configuration Tabs -->
                <div class="mb-6">
                    <div class="text-lg font-semibold mb-4">Tag Configuration</div>

                    @php
                        $tags = \App\Models\Tag::orderBy('name')->get();
                    @endphp

                    <div role="tablist" class="tabs tabs-boxed bg-base-200 p-1 mb-6">
                        <!-- Player Filters Tab -->
                        <input type="radio" name="tag_tabs" role="tab" class="tab text-base font-semibold h-12 [&:checked]:bg-primary [&:checked]:text-primary-content" aria-label="Player Filters" checked />
                        <div role="tabpanel" class="tab-content bg-base-100 rounded-lg p-6 mt-4">
                            <p class="text-sm opacity-70 mb-6">
                                Configure which players can see and receive this task based on their tags.
                            </p>

                            <!-- Tags Selection -->
                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Must Have Tags</span>
                                </label>
                                <p class="text-sm opacity-70 mb-3">
                                    Players must have at least one of these tags to see this task. Leave empty for all players.
                                </p>

                                @if($tags->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($tags as $tag)
                                            <label class="cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name="tags[]"
                                                    value="{{ $tag->id }}"
                                                    class="hidden peer"
                                                    {{ (is_array(old('tags', $task->tags->pluck('id')->toArray())) && in_array($tag->id, old('tags', $task->tags->pluck('id')->toArray()))) ? 'checked' : '' }}
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

                            <!-- Can't Have Tags Selection -->
                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Can't Have Tags (Negative Filter)</span>
                                </label>
                                <p class="text-sm opacity-70 mb-3">
                                    Players must <strong>NOT</strong> have any of these tags to see this task. If a player has any of these tags, they won't see this task.
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
                                                    {{ (is_array(old('cant_have_tags', $task->cant_have_tags)) && in_array($tag->id, old('cant_have_tags', $task->cant_have_tags))) ? 'checked' : '' }}
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
                        </div>

                        <!-- Someone Filters Tab -->
                        <input type="radio" name="tag_tabs" role="tab" class="tab text-base font-semibold h-12 [&:checked]:bg-secondary [&:checked]:text-secondary-content" aria-label="Someone Filters" />
                        <div role="tabpanel" class="tab-content bg-base-100 rounded-lg p-6 mt-4">
                            <p class="text-sm opacity-70 mb-6">
                                Configure filters for the <code class="bg-base-300 px-1 rounded">@{{someone}}</code> template variable. These determine which random player gets selected when your task uses <code class="bg-base-300 px-1 rounded">@{{someone}}</code>.
                            </p>

                            <!-- Someone Gender Filter -->
                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">@{{someone}} Gender Filter</span>
                                </label>
                                <p class="text-sm opacity-70 mb-3">
                                    Filter by gender relative to the current player.
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <label class="cursor-pointer">
                                        <input
                                            type="radio"
                                            name="someone_gender"
                                            value="any"
                                            class="hidden peer"
                                            {{ old('someone_gender', $task->someone_gender ?? 'any') === 'any' ? 'checked' : '' }}
                                        >
                                        <div class="card border-2 border-base-300 peer-checked:border-primary peer-checked:bg-primary/10 hover:border-primary/50 transition-all">
                                            <div class="card-body p-3 flex-row items-center gap-3">
                                                <span class="text-2xl">🌐</span>
                                                <div class="flex-1">
                                                    <div class="font-semibold">Any Gender</div>
                                                    <div class="text-xs opacity-70">No gender restriction</div>
                                                </div>
                                                <div class="w-5 h-5 rounded-full border-2 border-base-300 peer-checked:border-primary peer-checked:bg-primary flex items-center justify-center">
                                                    <div class="w-2 h-2 rounded-full bg-white hidden peer-checked:block"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input
                                            type="radio"
                                            name="someone_gender"
                                            value="same"
                                            class="hidden peer"
                                            {{ old('someone_gender', $task->someone_gender ?? 'any') === 'same' ? 'checked' : '' }}
                                        >
                                        <div class="card border-2 border-base-300 peer-checked:border-info peer-checked:bg-info/10 hover:border-info/50 transition-all">
                                            <div class="card-body p-3 flex-row items-center gap-3">
                                                <span class="text-2xl">👥</span>
                                                <div class="flex-1">
                                                    <div class="font-semibold">Same Gender</div>
                                                    <div class="text-xs opacity-70">As current player</div>
                                                </div>
                                                <div class="w-5 h-5 rounded-full border-2 border-base-300 peer-checked:border-info peer-checked:bg-info flex items-center justify-center">
                                                    <div class="w-2 h-2 rounded-full bg-white hidden peer-checked:block"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input
                                            type="radio"
                                            name="someone_gender"
                                            value="other"
                                            class="hidden peer"
                                            {{ old('someone_gender', $task->someone_gender ?? 'any') === 'other' ? 'checked' : '' }}
                                        >
                                        <div class="card border-2 border-base-300 peer-checked:border-secondary peer-checked:bg-secondary/10 hover:border-secondary/50 transition-all">
                                            <div class="card-body p-3 flex-row items-center gap-3">
                                                <span class="text-2xl">🔄</span>
                                                <div class="flex-1">
                                                    <div class="font-semibold">Other Gender</div>
                                                    <div class="text-xs opacity-70">Different from current player</div>
                                                </div>
                                                <div class="w-5 h-5 rounded-full border-2 border-base-300 peer-checked:border-secondary peer-checked:bg-secondary flex items-center justify-center">
                                                    <div class="w-2 h-2 rounded-full bg-white hidden peer-checked:block"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                @error('someone_gender')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Someone Must Have Tags Selection -->
                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">@{{someone}} Must Have Tags</span>
                                </label>
                                <p class="text-sm opacity-70 mb-3">
                                    Select tags that the random player must have. The player must have ALL of these tags. Leave empty for any player.
                                </p>

                                @if($tags->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($tags as $tag)
                                            <label class="cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name="someone_tags[]"
                                                    value="{{ $tag->id }}"
                                                    class="hidden peer"
                                                    {{ (is_array(old('someone_tags', $task->someone_tags)) && in_array($tag->id, old('someone_tags', $task->someone_tags))) ? 'checked' : '' }}
                                                >
                                                <div class="card border-2 border-base-300 peer-checked:border-info peer-checked:bg-info/10 hover:border-info/50 transition-all">
                                                    <div class="card-body p-3 flex-row items-center gap-3">
                                                        <span class="text-2xl">👤</span>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="font-semibold truncate">{{ $tag->name }}</div>
                                                            @if($tag->description)
                                                                <div class="text-xs opacity-70 truncate">{{ $tag->description }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="w-5 h-5 rounded border-2 border-base-300 peer-checked:border-info peer-checked:bg-info flex items-center justify-center">
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

                                @error('someone_tags')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Someone Can't Have Tags Selection -->
                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">@{{someone}} Can't Have Tags</span>
                                </label>
                                <p class="text-sm opacity-70 mb-3">
                                    Select tags that the random player must <strong>NOT</strong> have. If the player has any of these tags, they won't be selected.
                                </p>

                                @if($tags->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($tags as $tag)
                                            <label class="cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name="someone_cant_have_tags[]"
                                                    value="{{ $tag->id }}"
                                                    class="hidden peer"
                                                    {{ (is_array(old('someone_cant_have_tags', $task->someone_cant_have_tags)) && in_array($tag->id, old('someone_cant_have_tags', $task->someone_cant_have_tags))) ? 'checked' : '' }}
                                                >
                                                <div class="card border-2 border-base-300 peer-checked:border-warning peer-checked:bg-warning/10 hover:border-warning/50 transition-all">
                                                    <div class="card-body p-3 flex-row items-center gap-3">
                                                        <span class="text-2xl">🚷</span>
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

                                @error('someone_cant_have_tags')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>

                        <!-- Output Tags Tab -->
                        <input type="radio" name="tag_tabs" role="tab" class="tab text-base font-semibold h-12 [&:checked]:bg-success [&:checked]:text-success-content" aria-label="Output Tags" />
                        <div role="tabpanel" class="tab-content bg-base-100 rounded-lg p-6 mt-4">
                            <p class="text-sm opacity-70 mb-6">
                                Configure what happens to the player's tags when they complete this task.
                            </p>

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
                                                    {{ (is_array(old('tags_to_remove', $task->tags_to_remove)) && in_array($tag->id, old('tags_to_remove', $task->tags_to_remove))) ? 'checked' : '' }}
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
                                                    {{ (is_array(old('tags_to_add', $task->tags_to_add)) && in_array($tag->id, old('tags_to_add', $task->tags_to_add))) ? 'checked' : '' }}
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
                        </div>
                    </div>
                </div>

                <!-- Draft Status -->
                <div class="form-control mb-8">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input
                            type="checkbox"
                            name="draft"
                            value="1"
                            class="checkbox checkbox-primary"
                            {{ old('draft', $task->draft) ? 'checked' : '' }}
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
                <div class="flex gap-4">
                    <button type="submit" class="btn btn-primary flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                        </svg>
                        Update Task
                    </button>
                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-ghost flex-1">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Info -->
    <div class="stats stats-vertical lg:stats-horizontal shadow mt-6 w-full">
        <div class="stat">
            <div class="stat-title">Task ID</div>
            <div class="stat-value text-xl">{{ $task->id }}</div>
            <div class="stat-desc">Unique identifier</div>
        </div>

        <div class="stat">
            <div class="stat-title">Created</div>
            <div class="stat-value text-xl">{{ $task->created_at->format('M d, Y') }}</div>
            <div class="stat-desc">{{ $task->created_at->diffForHumans() }}</div>
        </div>

        <div class="stat">
            <div class="stat-title">Last Updated</div>
            <div class="stat-value text-xl">{{ $task->updated_at->format('M d, Y') }}</div>
            <div class="stat-desc">{{ $task->updated_at->format('g:i A') }}</div>
        </div>

        <div class="stat">
            <div class="stat-title">Current Status</div>
            <div class="stat-value text-xl">
                <div class="badge {{ $task->draft ? 'badge-warning' : 'badge-success' }} badge-lg">
                    {{ $task->draft ? 'Draft' : 'Published' }}
                </div>
            </div>
            <div class="stat-desc">Visibility state</div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="alert alert-error mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div class="flex-1">
            <h3 class="font-bold">⚠️ Danger Zone</h3>
            <p class="text-sm">Deleting this task is permanent and cannot be undone.</p>
        </div>
        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this task? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-error btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Delete Task
            </button>
        </form>
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

        // Trigger on page load for existing value
        textarea.dispatchEvent(new Event('input'));
    }
</script>
@endpush
@endsection
