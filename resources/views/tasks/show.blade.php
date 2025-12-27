@extends('layouts.app')

@section('title', 'View Task')

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

    <!-- Task Card -->
    <div class="card bg-base-100 shadow-xl">
        <!-- Header -->
        <div class="bg-gradient-to-r {{ $task->type === 'truth' ? 'from-blue-500 to-blue-600' : 'from-purple-500 to-purple-600' }} text-white">
            <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="card-title text-3xl">
                        {{ $task->type === 'truth' ? '💬 Truth' : '🎯 Dare' }}
                    </h1>
                    <div class="flex flex-col items-end gap-2">
                        <div class="badge badge-lg bg-white/30 border-white/30 text-white">
                            🌶️ Spice Level: {{ $task->spice_rating }}/5
                        </div>
                        @if($task->draft)
                            <div class="badge badge-warning badge-lg gap-2">
                                📝 Draft
                            </div>
                        @endif
                    </div>
                </div>
                <p class="text-xl text-white/90">{{ $task->spice_level }}</p>
            </div>
        </div>

        <!-- Content -->
        <div class="card-body">
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-3">Task Description:</h2>
                <div class="alert {{ $task->type === 'truth' ? 'alert-info' : 'alert-secondary' }}">
                    <div class="text-xl leading-relaxed">
                        {{ $task->description }}
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($task->tags()->count() > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3">Tags:</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($task->tags as $tag)
                            <a href="{{ route('tags.show', $tag) }}" class="badge badge-lg badge-primary gap-2 hover:badge-secondary transition-colors">
                                <span>🏷️</span>
                                <span>{{ $tag->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tags to Remove on Completion -->
            @if($task->tags_to_remove && count($task->tags_to_remove) > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3">Tags Removed on Completion:</h2>
                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <div class="font-semibold mb-2">When this task is completed, these tags will be removed from the player:</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($task->getRemovableTags() as $tag)
                                    <a href="{{ route('tags.show', $tag) }}" class="badge badge-lg badge-error gap-2 hover:opacity-75 transition-opacity">
                                        <span>🗑️</span>
                                        <span>{{ $tag->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tags to Add on Completion -->
            @if($task->tags_to_add && count($task->tags_to_add) > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3">Tags Added on Completion:</h2>
                    <div class="alert alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <div class="font-semibold mb-2">When this task is completed, these tags will be added to the player:</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($task->getAddableTags() as $tag)
                                    <a href="{{ route('tags.show', $tag) }}" class="badge badge-lg badge-success gap-2 hover:opacity-75 transition-opacity">
                                        <span>✨</span>
                                        <span>{{ $tag->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Can't Have Tags -->
            @if($task->cant_have_tags && count($task->cant_have_tags) > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3">Can't Have Tags (Negative Filter):</h2>
                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <div>
                            <div class="font-semibold mb-2">This task will NOT appear for players who have any of these tags:</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($task->getCantHaveTags() as $tag)
                                    <a href="{{ route('tags.show', $tag) }}" class="badge badge-lg badge-warning gap-2 hover:opacity-75 transition-opacity">
                                        <span>🚫</span>
                                        <span>{{ $tag->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Task Details -->
            <div class="stats stats-vertical lg:stats-horizontal shadow mb-8 w-full">
                <div class="stat">
                    <div class="stat-title">Type</div>
                    <div class="stat-value text-2xl capitalize">{{ $task->type }}</div>
                    <div class="stat-desc">Task category</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Spice Rating</div>
                    <div class="stat-value text-2xl spice-{{ $task->spice_rating }}">
                        {{ str_repeat('🌶️', $task->spice_rating) }}
                    </div>
                    <div class="stat-desc">({{ $task->spice_rating }}/5)</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Status</div>
                    <div class="stat-value text-2xl">
                        <div class="badge {{ $task->draft ? 'badge-warning' : 'badge-success' }} badge-lg">
                            {{ $task->draft ? 'Draft' : 'Published' }}
                        </div>
                    </div>
                    <div class="stat-desc">Current state</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Created</div>
                    <div class="stat-value text-xl">{{ $task->created_at->format('M d') }}</div>
                    <div class="stat-desc">{{ $task->created_at->format('Y') }}</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Edit Task
                        </a>

                        <form action="{{ route('tasks.toggleDraft', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-lg w-full">
                                {{ $task->draft ? '📤 Publish' : '📝 Mark as Draft' }}
                            </button>
                        </form>
                    @endif
                @endauth



                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('tasks.create') }}" class="btn btn-accent btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Task
                        </a>
                    @endif
                @endauth

                <a href="{{ route('tasks.index') }}" class="btn btn-ghost btn-lg {{ auth()->guest() || (auth()->check() && !Auth::user()->isAdmin()) ? 'md:col-span-2' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                    All Tasks
                </a>

                @auth
                    @if(Auth::user()->isAdmin())
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error btn-lg w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="alert mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-sm">
            <div>Task ID: <span class="font-semibold">{{ $task->id }}</span></div>
            <div>Last Updated: <span class="font-semibold">{{ $task->updated_at->format('M d, Y \a\t g:i A') }}</span></div>
        </div>
    </div>
</div>
@endsection
