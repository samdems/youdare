@extends('layouts.app')

@section('title', 'All Tasks')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">All Tasks</h1>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Task
                </a>
            @endif
        @endauth
    </div>

    <!-- Filter Form -->
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <h2 class="card-title">Filters</h2>
            <form method="GET" action="{{ route('tasks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Type</span>
                    </label>
                    <select name="type" class="select select-bordered w-full">
                        <option value="">All Types</option>
                        <option value="truth" {{ request('type') == 'truth' ? 'selected' : '' }}>Truth</option>
                        <option value="dare" {{ request('type') == 'dare' ? 'selected' : '' }}>Dare</option>
                        <option value="group" {{ request('type') == 'group' ? 'selected' : '' }}>Group</option>
                    </select>
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Min Spice</span>
                    </label>
                    <select name="min_spice" class="select select-bordered w-full">
                        <option value="">Any</option>
                        <option value="1" {{ request('min_spice') == '1' ? 'selected' : '' }}>1 - Mild</option>
                        <option value="2" {{ request('min_spice') == '2' ? 'selected' : '' }}>2 - Medium</option>
                        <option value="3" {{ request('min_spice') == '3' ? 'selected' : '' }}>3 - Hot</option>
                        <option value="4" {{ request('min_spice') == '4' ? 'selected' : '' }}>4 - Extra Hot</option>
                        <option value="5" {{ request('min_spice') == '5' ? 'selected' : '' }}>5 - Extreme</option>
                    </select>
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Max Spice</span>
                    </label>
                    <select name="max_spice" class="select select-bordered w-full">
                        <option value="">Any</option>
                        <option value="1" {{ request('max_spice') == '1' ? 'selected' : '' }}>1 - Mild</option>
                        <option value="2" {{ request('max_spice') == '2' ? 'selected' : '' }}>2 - Medium</option>
                        <option value="3" {{ request('max_spice') == '3' ? 'selected' : '' }}>3 - Hot</option>
                        <option value="4" {{ request('max_spice') == '4' ? 'selected' : '' }}>4 - Extra Hot</option>
                        <option value="5" {{ request('max_spice') == '5' ? 'selected' : '' }}>5 - Extreme</option>
                    </select>
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <select name="draft" class="select select-bordered w-full">
                        <option value="">All</option>
                        <option value="0" {{ request('draft') === '0' ? 'selected' : '' }}>Published</option>
                        <option value="1" {{ request('draft') === '1' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div class="form-control w-full md:col-span-4">
                    <label class="label">
                        <span class="label-text">Tags</span>
                    </label>
                    <select name="tags[]" multiple class="select select-bordered w-full h-32" size="5">
                        <option value="" disabled>Select tags to filter (hold Ctrl/Cmd to select multiple)</option>
                        @foreach($allTags as $tag)
                            <option value="{{ $tag->id }}" {{ in_array($tag->id, request('tags', [])) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="label">
                        <span class="label-text-alt">Tasks with any of the selected tags will be shown</span>
                    </label>
                </div>

                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Apply Filters
                    </button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-ghost">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks List -->
    @if($tasks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tasks as $task)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-200">
                    <div class="card-body">
                        <!-- Header with Type and Spice -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="badge {{ $task->type === 'truth' ? 'badge-info' : ($task->type === 'dare' ? 'badge-secondary' : 'badge-success') }} badge-lg gap-2">
                                {{ $task->type === 'truth' ? '💬 Truth' : ($task->type === 'dare' ? '🎯 Dare' : '👥 Group') }}
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="badge badge-outline gap-1">
                                    🌶️ {{ $task->spice_rating }}
                                </div>
                                @if($task->draft)
                                    <div class="badge badge-warning">Draft</div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-base-content/80 mb-4 line-clamp-3">
                            {{ $task->description }}
                        </p>

                        <!-- Tags -->
                        @if($task->tags()->count() > 0)
                            <div class="mb-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($task->tags()->limit(3)->get() as $tag)
                                        <a href="{{ route('tags.show', $tag) }}" class="badge badge-sm badge-primary hover:badge-secondary transition-colors">
                                            {{ $tag->name }}
                                        </a>
                                    @endforeach
                                    @if($task->tags()->count() > 3)
                                        <span class="badge badge-sm badge-ghost">+{{ $task->tags()->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="mb-3">
                                <span class="badge badge-sm badge-ghost">No Tags</span>
                            </div>
                        @endif

                        <!-- Spice Level Text -->
                        <div class="mb-4">
                            <div class="text-sm opacity-70">
                                <strong>Spice Level:</strong> {{ $task->spice_level }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card-actions justify-end">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-primary btn-sm">
                                View
                            </a>
                            @auth
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-ghost btn-sm">
                                        Edit
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $tasks->links() }}
        </div>
    @else
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body items-center text-center">
                <div class="text-6xl mb-4">🤷</div>
                <h3 class="card-title">No tasks found</h3>
                <p class="text-base-content/70 mb-6">Try adjusting your filters or create a new task.</p>
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="card-actions">
                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                Create Your First Task
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection
