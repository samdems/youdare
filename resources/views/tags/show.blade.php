@extends('layouts.app')

@section('title', $tag->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('tags.index') }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to All Tags
        </a>
    </div>

    <!-- Tag Header Card -->
    <div class="card bg-gradient-to-br from-primary to-secondary text-white shadow-xl mb-6">
        <div class="card-body">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-5xl">🏷️</span>
                        <div>
                            <h1 class="text-4xl font-bold mb-2">{{ $tag->name }}</h1>
                            <div class="badge badge-lg bg-white/20 text-white border-white/30">
                                {{ $tag->slug }}
                            </div>
                        </div>
                    </div>

                    @if($tag->description)
                        <p class="text-lg text-white/90 mb-4">
                            {{ $tag->description }}
                        </p>
                    @endif

                    <!-- Stats -->
                    <div class="flex gap-6 text-white/90">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="font-semibold">{{ $tag->tasks_count ?? 0 }}</span>
                            <span>{{ Str::plural('task', $tag->tasks_count ?? 0) }}</span>
                        </div>

                    </div>
                </div>

                <!-- Action Buttons -->
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('tags.create') }}" class="btn btn-sm bg-white/20 hover:bg-white/30 text-white border-white/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                New Tag
                            </a>
                            <a href="{{ route('tags.edit', $tag) }}" class="btn btn-sm bg-white/20 hover:bg-white/30 text-white border-white/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tag? This will remove it from all tasks and users.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm bg-error/80 hover:bg-error text-white border-error w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>



    <!-- Tagged Tasks Preview -->
    @if($tag->tasks()->count() > 0)
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Tagged Tasks
                    <span class="badge badge-lg">{{ $tag->tasks()->count() }}</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($tag->tasks()->published()->limit(6)->get() as $task)
                        <div class="card bg-base-200 hover:bg-base-300 transition-colors">
                            <div class="card-body p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="badge {{ $task->type === 'truth' ? 'badge-info' : ($task->type === 'dare' ? 'badge-secondary' : 'badge-success') }}">
                                        {{ $task->type === 'truth' ? '💬 Truth' : ($task->type === 'dare' ? '🎯 Dare' : '👥 Group') }}
                                    </div>
                                    <div class="badge badge-outline">
                                        🌶️ {{ $task->spice_rating }}
                                    </div>
                                </div>
                                <p class="text-sm line-clamp-2">{{ $task->description }}</p>
                                <div class="card-actions justify-end mt-2">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-xs btn-ghost">View</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($tag->tasks()->count() > 6)
                    <div class="text-center mt-4">
                        <a href="{{ route('tasks.index', ['tag' => $tag->slug]) }}" class="btn btn-sm btn-ghost">
                            View All {{ $tag->tasks()->count() }} Tasks
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>No tasks have been tagged with "{{ $tag->name }}" yet.</span>
        </div>
    @endif

    <!-- Additional Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tag Details -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg">Tag Details</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-semibold text-base-content/70">Name:</span>
                        <p class="text-base-content">{{ $tag->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-base-content/70">Created:</span>
                        <p class="text-base-content text-sm">{{ $tag->created_at->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-base-content/70">Total Tasks:</span>
                        <p class="text-base-content">{{ $tag->tasks_count ?? 0 }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-base-content/70">Last Updated:</span>
                        <p class="text-base-content text-sm">{{ $tag->updated_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Info -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg">Usage Information</h3>
                <div class="space-y-4">
                    <div class="stat p-0">
                        <div class="stat-figure text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="stat-title">Total Tasks</div>
                        <div class="stat-value text-primary">{{ $tag->tasks_count ?? 0 }}</div>
                        <div class="stat-desc">
                            {{ $tag->tasks()->where('draft', false)->count() }} published,
                            {{ $tag->tasks()->where('draft', true)->count() }} draft
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection
