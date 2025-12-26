@extends('layouts.app')

@section('title', 'All Tags')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Tags</h1>
            <p class="text-base-content/70 mt-2">Manage content categories and user preferences</p>
        </div>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('tags.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Tag
                </a>
            @endif
        @endauth
    </div>

    <!-- All Tags List -->
    @if($tags->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tags as $tag)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-200">
                    <div class="card-body">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="card-title text-xl">
                                <span class="text-2xl mr-2">🏷️</span>
                                {{ $tag->name }}
                            </h3>
                            <div class="flex flex-wrap gap-1">
                                @if($tag->is_default)
                                    <div class="badge badge-primary badge-sm">Default</div>
                                @endif
                                @if($tag->default_for_gender === 'male')
                                    <div class="badge badge-info badge-sm">👨 Male</div>
                                @elseif($tag->default_for_gender === 'female')
                                    <div class="badge badge-secondary badge-sm">👩 Female</div>
                                @elseif($tag->default_for_gender === 'both')
                                    <div class="badge badge-accent badge-sm">👥 Both</div>
                                @endif
                                <div class="badge badge-warning badge-sm">{{ str_repeat('🌶️', $tag->min_spice_level) }}</div>
                            </div>
                        </div>

                        <!-- Slug -->
                        <div class="mb-2">
                            <span class="text-xs font-mono bg-base-200 px-2 py-1 rounded">{{ $tag->slug }}</span>
                        </div>

                        <!-- Description -->
                        @if($tag->description)
                            <p class="text-base-content/70 text-sm mb-4">
                                {{ $tag->description }}
                            </p>
                        @endif

                        <!-- Stats -->
                        <div class="flex gap-4 text-sm mb-4">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span class="text-base-content/70">{{ $tag->tasks_count ?? 0 }} tasks</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card-actions justify-end">
                            <a href="{{ route('tags.show', $tag) }}" class="btn btn-ghost btn-sm">
                                View
                            </a>
                            @auth
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('tags.edit', $tag) }}" class="btn btn-ghost btn-sm">
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
            {{ $tags->links() }}
        </div>
    @else
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body items-center text-center">
                <div class="text-6xl mb-4">🏷️</div>
                <h3 class="card-title">No tags found</h3>
                <p class="text-base-content/70 mb-6">Create your first tag to start categorizing content.</p>
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="card-actions">
                            <a href="{{ route('tags.create') }}" class="btn btn-primary">
                                Create Your First Tag
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @endif
</div>

<!-- Info Alert -->
<div class="alert alert-info mt-8">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <div>
        <h3 class="font-bold">How Tags Work</h3>
        <ul class="list-disc list-inside text-sm mt-2 space-y-1">
            <li><strong>Organization:</strong> Tags help categorize and organize tasks</li>
            <li><strong>Filtering:</strong> Use tags to find tasks in specific categories</li>
            <li><strong>Multiple Tags:</strong> Tasks can have multiple tags for better organization</li>
            <li><strong>Easy Navigation:</strong> Click on a tag to see all tasks in that category</li>
        </ul>
    </div>
</div>
@endsection
