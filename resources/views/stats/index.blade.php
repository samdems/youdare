@extends('layouts.app')

@section('title', 'Statistics')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">📊 Task Statistics</h1>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="stats shadow bg-base-100">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="stat-title">Total Tasks</div>
                <div class="stat-value text-primary">{{ $grandTotal }}</div>
                <div class="stat-desc">Published tasks</div>
            </div>
        </div>

        <div class="stats shadow bg-base-100">
            <div class="stat">
                <div class="stat-figure text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="stat-title">Truths</div>
                <div class="stat-value text-info">{{ $totalTruths }}</div>
                <div class="stat-desc">{{ $grandTotal > 0 ? round(($totalTruths / $grandTotal) * 100, 1) : 0 }}% of total</div>
            </div>
        </div>

        <div class="stats shadow bg-base-100">
            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="stat-title">Dares</div>
                <div class="stat-value text-secondary">{{ $totalDares }}</div>
                <div class="stat-desc">{{ $grandTotal > 0 ? round(($totalDares / $grandTotal) * 100, 1) : 0 }}% of total</div>
            </div>
        </div>

        <div class="stats shadow bg-base-100">
            <div class="stat">
                <div class="stat-figure text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                <div class="stat-title">Drafts</div>
                <div class="stat-value text-warning">{{ $draftCount }}</div>
                <div class="stat-desc">Unpublished tasks</div>
            </div>
        </div>
    </div>

    <!-- Stats by Spice Level -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title mb-4">🌶️ Tasks by Spice Level</h2>

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Spice Level</th>
                            <th class="text-center">💬 Truths</th>
                            <th class="text-center">🎯 Dares</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats as $stat)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold spice-{{ $stat['spice_level'] }}">
                                        {{ $stat['spice_level'] }}
                                    </span>
                                    <span class="badge badge-outline">{{ $stat['spice_name'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info badge-lg">{{ $stat['truths'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary badge-lg">{{ $stat['dares'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary badge-lg">{{ $stat['total'] }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <progress
                                        class="progress progress-primary w-32"
                                        value="{{ $stat['total'] }}"
                                        max="{{ $grandTotal }}">
                                    </progress>
                                    <span class="text-sm opacity-70">
                                        {{ $grandTotal > 0 ? round(($stat['total'] / $grandTotal) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="font-bold bg-base-200">
                            <td>Total</td>
                            <td class="text-center">
                                <span class="badge badge-info badge-lg">{{ $totalTruths }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary badge-lg">{{ $totalDares }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary badge-lg">{{ $grandTotal }}</span>
                            </td>
                            <td class="text-center">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Visual Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Truths by Spice Level -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">💬 Truths by Spice Level</h2>
                <div class="space-y-4 mt-4">
                    @foreach($stats as $stat)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-semibold spice-{{ $stat['spice_level'] }}">
                                {{ $stat['spice_level'] }} - {{ $stat['spice_name'] }}
                            </span>
                            <span class="text-sm badge badge-info">{{ $stat['truths'] }}</span>
                        </div>
                        <progress
                            class="progress progress-info w-full h-4"
                            value="{{ $stat['truths'] }}"
                            max="{{ $totalTruths > 0 ? $totalTruths : 1 }}">
                        </progress>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Dares by Spice Level -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">🎯 Dares by Spice Level</h2>
                <div class="space-y-4 mt-4">
                    @foreach($stats as $stat)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-semibold spice-{{ $stat['spice_level'] }}">
                                {{ $stat['spice_level'] }} - {{ $stat['spice_name'] }}
                            </span>
                            <span class="text-sm badge badge-secondary">{{ $stat['dares'] }}</span>
                        </div>
                        <progress
                            class="progress progress-secondary w-full h-4"
                            value="{{ $stat['dares'] }}"
                            max="{{ $totalDares > 0 ? $totalDares : 1 }}">
                        </progress>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Spice Level Visual Distribution -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title mb-4">🌶️ Overall Spice Distribution</h2>
            <div class="flex flex-col gap-3">
                @foreach($stats as $stat)
                <div class="flex items-center gap-3">
                    <div class="w-32 text-right">
                        <span class="font-bold spice-{{ $stat['spice_level'] }}">
                            {{ $stat['spice_name'] }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-1 items-center">
                            <progress
                                class="progress progress-primary w-full h-6"
                                value="{{ $stat['total'] }}"
                                max="{{ $grandTotal > 0 ? $grandTotal : 1 }}">
                            </progress>
                            <span class="badge badge-lg bg-spice-{{ $stat['spice_level'] }} border-0 min-w-[80px]">
                                {{ $stat['total'] }} tasks
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card bg-base-100 shadow-xl mt-8">
        <div class="card-body">
            <h2 class="card-title">Quick Actions</h2>
            <div class="flex flex-wrap gap-2 mt-4">
                <a href="{{ route('tasks.index') }}" class="btn btn-primary">
                    📋 View All Tasks
                </a>
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('tasks.create') }}" class="btn btn-secondary">
                            ➕ Create New Task
                        </a>
                    @endif
                @endauth
                <a href="{{ route('game') }}" class="btn btn-accent">
                    🎮 Play Game
                </a>
                <a href="{{ route('tags.index') }}" class="btn btn-ghost">
                    🏷️ View Tags
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .spice-1 { color: #10b981; }
    .spice-2 { color: #3b82f6; }
    .spice-3 { color: #f59e0b; }
    .spice-4 { color: #ef4444; }
    .spice-5 { color: #dc2626; }

    .bg-spice-1 { background-color: #d1fae5; color: #065f46; }
    .bg-spice-2 { background-color: #dbeafe; color: #1e40af; }
    .bg-spice-3 { background-color: #fef3c7; color: #92400e; }
    .bg-spice-4 { background-color: #fee2e2; color: #991b1b; }
    .bg-spice-5 { background-color: #fecaca; color: #7f1d1d; }
</style>
@endpush
@endsection
