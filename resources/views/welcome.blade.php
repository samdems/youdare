<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'YouDare') }} - Truth or Dare Game</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gradient-to-br from-primary/10 via-secondary/10 to-accent/10">
        <!-- Navigation -->
        <div class="navbar bg-base-100 shadow-lg">
            <div class="navbar-start">
                <a href="/" class="btn btn-ghost text-xl">
                    🔥 <span class="text-primary font-bold">YouDare</span>
                </a>
            </div>
            <div class="navbar-end gap-2">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('tasks.create') }}" class="btn btn-ghost">
                            ➕ Create Task
                        </a>
                    @endif
                    <a href="{{ route('game') }}" class="btn btn-primary">
                        🎮 Play Game
                    </a>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                <span class="text-xl">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                            <li class="menu-title">
                                <span>{{ Auth::user()->name }}</span>
                            </li>
                            <li><a href="{{ route('tasks.index') }}">📋 View Tasks</a></li>
                            @if(Auth::user()->isAdmin())
                                <li><a href="{{ route('tasks.create') }}">➕ Create Task</a></li>
                                <li><a href="{{ route('tags.create') }}">🏷️ Create Tag</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left">🚪 Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('tasks.index') }}" class="btn btn-ghost">
                        View Tasks
                    </a>
                    <a href="{{ route('game') }}" class="btn btn-ghost">
                        🎮 Play Game
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-ghost">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Register
                    </a>
                @endauth
            </div>
        </div>

        <!-- Hero Section -->
        <div class="hero min-h-[calc(100vh-4rem)]">
            <div class="hero-content text-center">
                <div class="max-w-4xl">
                    <div class="mb-8">
                        <span class="text-8xl">🔥</span>
                    </div>
                    <h1 class="text-6xl font-bold mb-6 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        Truth or Dare
                    </h1>
                    <p class="text-2xl mb-8 opacity-80">
                        The ultimate party game that brings excitement, laughter, and unforgettable moments!
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                        <a href="{{ route('game') }}" class="btn btn-primary btn-lg gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                            Start Playing
                        </a>
                        <a href="{{ route('tasks.create') }}" class="btn btn-outline btn-lg gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Create Task
                        </a>
                        <a href="{{ route('tasks.index') }}" class="btn btn-ghost btn-lg gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                            Browse All Tasks
                        </a>
                        @auth
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('stats.index') }}" class="btn btn-ghost btn-lg gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                    </svg>
                                    View Stats
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body items-center text-center">
                                <span class="text-5xl mb-4">💬</span>
                                <h3 class="card-title">Truths</h3>
                                <p>Reveal secrets and get to know your friends on a deeper level</p>
                            </div>
                        </div>

                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body items-center text-center">
                                <span class="text-5xl mb-4">🎯</span>
                                <h3 class="card-title">Dares</h3>
                                <p>Take on exciting challenges and create hilarious memories</p>
                            </div>
                        </div>

                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body items-center text-center">
                                <span class="text-5xl mb-4">🌶️</span>
                                <h3 class="card-title">Spice Levels</h3>
                                <p>Choose your comfort level from mild to extreme intensity</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="stats stats-vertical lg:stats-horizontal shadow mt-16 w-full">
                        <div class="stat">
                            <div class="stat-figure text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Available Tasks</div>
                            <div class="stat-value text-primary">{{ \App\Models\Task::published()->count() }}</div>
                            <div class="stat-desc">Ready to play</div>
                        </div>

                        <div class="stat">
                            <div class="stat-figure text-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Truths</div>
                            <div class="stat-value text-secondary">{{ \App\Models\Task::published()->truths()->count() }}</div>
                            <div class="stat-desc">Questions to answer</div>
                        </div>

                        <div class="stat">
                            <div class="stat-figure text-accent">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Dares</div>
                            <div class="stat-value text-accent">{{ \App\Models\Task::published()->dares()->count() }}</div>
                            <div class="stat-desc">Challenges to complete</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer footer-center p-10 bg-base-200 text-base-content">
            <div>
                <p class="font-bold text-lg">
                    🔥 YouDare - Truth or Dare Game
                </p>
                <p>&copy; {{ date('Y') }} All rights reserved</p>
            </div>
        </footer>
    </div>

    <script>
        // Theme persistence
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>
