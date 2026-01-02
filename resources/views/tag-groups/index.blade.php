@extends('layouts.app')

@section('title', 'Tag Groups')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Tag Groups</h1>
            <p class="text-base-content/70 mt-2">Organize tags into logical categories</p>
        </div>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('tag-groups.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Tag Group
                </a>
            @endif
        @endauth
    </div>

    <!-- Tag Groups List -->
    @if($tagGroups->count() > 0)
        <div class="space-y-6">
            @foreach($tagGroups as $tagGroup)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-200">
                    <div class="card-body">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="card-title text-2xl">
                                        <span class="text-3xl mr-2">📁</span>
                                        {{ $tagGroup->name }}
                                    </h3>
                                    <div class="badge badge-neutral badge-sm">Order: {{ $tagGroup->sort_order }}</div>
                                </div>

                                <!-- Slug -->
                                <div class="mb-2">
                                    <span class="text-xs font-mono bg-base-200 px-2 py-1 rounded">{{ $tagGroup->slug }}</span>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <div class="badge badge-primary badge-lg">
                                    {{ $tagGroup->tags_count ?? 0 }} tags
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($tagGroup->description)
                            <p class="text-base-content/70 text-sm mb-4">
                                {{ $tagGroup->description }}
                            </p>
                        @endif

                        <!-- Actions -->
                        <div class="card-actions justify-end pt-4 border-t border-base-300">
                            <a href="{{ route('tag-groups.show', $tagGroup) }}" class="btn btn-ghost btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Tags
                            </a>
                            @auth
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('tag-groups.edit', $tagGroup) }}" class="btn btn-ghost btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('tag-groups.destroy', $tagGroup) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this tag group? All tags in this group will be unassigned but not deleted.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-error btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $tagGroups->links() }}
        </div>
    @else
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body items-center text-center">
                <div class="text-6xl mb-4">📁</div>
                <h3 class="card-title">No tag groups found</h3>
                <p class="text-base-content/70 mb-6">Create your first tag group to start organizing tags into categories.</p>
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="card-actions">
                            <a href="{{ route('tag-groups.create') }}" class="btn btn-primary">
                                Create Your First Tag Group
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
        <h3 class="font-bold">How Tag Groups Work</h3>
        <ul class="list-disc list-inside text-sm mt-2 space-y-1">
            <li><strong>Organization:</strong> Tag groups help categorize tags into logical sections</li>
            <li><strong>Better UX:</strong> Groups make it easier for users to find relevant tags during game setup</li>
            <li><strong>Sort Order:</strong> Control the order in which groups appear using the sort_order field</li>
            <li><strong>Flexible:</strong> Tags can belong to a group or remain ungrouped</li>
        </ul>
    </div>
</div>

<!-- Quick Link to Tags -->
<div class="mt-6">
    <a href="{{ route('tags.index') }}" class="btn btn-outline">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        Manage Tags
    </a>
</div>
@endsection
