@extends('layouts.app')

@section('title', $tagGroup->name . ' - Tag Group')

@section('content')
<div class="mb-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('tag-groups.index') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Tag Groups
                </a>
            </div>
            <h1 class="text-3xl font-bold flex items-center gap-3">
                <span class="text-4xl">📁</span>
                {{ $tagGroup->name }}
            </h1>
            <div class="mt-3 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono bg-base-200 px-2 py-1 rounded">{{ $tagGroup->slug }}</span>
                    <span class="badge badge-neutral">Sort Order: {{ $tagGroup->sort_order }}</span>
                    <span class="badge badge-primary">{{ $tagGroup->tags_count ?? 0 }} tags</span>
                </div>
                @if($tagGroup->description)
                    <p class="text-base-content/70 max-w-2xl">
                        {{ $tagGroup->description }}
                    </p>
                @endif
            </div>
        </div>
        @auth
            @if(Auth::user()->isAdmin())
                <div class="flex gap-2">
                    <a href="{{ route('tag-groups.edit', $tagGroup) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                </div>
            @endif
        @endauth
    </div>

    <!-- Tags in this Group -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-4">Tags in this Group</h2>

        @if($tagGroup->tags && $tagGroup->tags->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($tagGroup->tags as $tag)
                    <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-200">
                        <div class="card-body p-4">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg flex items-center gap-2">
                                    <span>🏷️</span>
                                    {{ $tag->name }}
                                </h3>
                            </div>

                            <!-- Badges -->
                            <div class="flex flex-wrap gap-1 mb-2">
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

                            <!-- Description -->
                            @if($tag->description)
                                <p class="text-base-content/70 text-sm mb-3 line-clamp-2">
                                    {{ $tag->description }}
                                </p>
                            @endif

                            <!-- Actions -->
                            <div class="card-actions justify-end pt-2 border-t border-base-300">
                                <a href="{{ route('tags.show', $tag) }}" class="btn btn-ghost btn-xs">
                                    View
                                </a>
                                @auth
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('tags.edit', $tag) }}" class="btn btn-ghost btn-xs">
                                            Edit
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body items-center text-center py-12">
                    <div class="text-6xl mb-4">🏷️</div>
                    <h3 class="card-title">No tags in this group yet</h3>
                    <p class="text-base-content/70 mb-6">Assign tags to this group by editing individual tags.</p>
                    @auth
                        @if(Auth::user()->isAdmin())
                            <div class="card-actions">
                                <a href="{{ route('tags.index') }}" class="btn btn-primary">
                                    Go to Tags
                                </a>
                                <a href="{{ route('tags.create') }}" class="btn btn-outline">
                                    Create New Tag
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        @endif
    </div>

    <!-- Metadata -->
    <div class="card bg-base-100 shadow-lg mt-6">
        <div class="card-body">
            <h3 class="card-title text-lg mb-4">Metadata</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-base-content/60">Created:</span>
                    <span class="font-medium ml-2">{{ $tagGroup->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <div>
                    <span class="text-base-content/60">Last Updated:</span>
                    <span class="font-medium ml-2">{{ $tagGroup->updated_at->format('M d, Y g:i A') }}</span>
                </div>
                <div>
                    <span class="text-base-content/60">ID:</span>
                    <span class="font-medium ml-2">{{ $tagGroup->id }}</span>
                </div>
                <div>
                    <span class="text-base-content/60">Tag Count:</span>
                    <span class="font-medium ml-2">{{ $tagGroup->tags_count ?? $tagGroup->tags->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="font-bold">Managing Tags in Groups</h3>
            <p class="text-sm mt-1">
                To add or remove tags from this group, edit individual tags and select this group from the "Tag Group" dropdown.
            </p>
        </div>
    </div>
</div>
@endsection
