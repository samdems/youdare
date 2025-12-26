<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'YouDare') }} - @yield('title', 'Truth or Dare')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .spice-1 { color: #10b981; }
        .spice-2 { color: #3b82f6; }
        .spice-3 { color: #f59e0b; }
        .spice-4 { color: #ef4444; }
        .spice-5 { color: #dc2626; }

        .bg-spice-1 { background-color: #d1fae5; }
        .bg-spice-2 { background-color: #dbeafe; }
        .bg-spice-3 { background-color: #fef3c7; }
        .bg-spice-4 { background-color: #fee2e2; }
        .bg-spice-5 { background-color: #fecaca; }
    </style>

    @stack('styles')
</head>
<body class="antialiased">
    <div id="app">
        <!-- Navigation -->
        <div class="navbar bg-base-100 shadow-lg">
            <div class="navbar-start">
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a href="{{ route('game') }}">🎮 Play Game</a></li>
                        <li><a href="{{ route('tasks.index') }}">All Tasks</a></li>
                        @auth
                            @if(Auth::user()->isAdmin())
                                <li><a href="{{ route('tasks.create') }}">Create Task</a></li>
                            @endif
                        @endauth
                        <li><a href="{{ route('tags.index') }}">🏷️ Tags</a></li>
                        <li><a href="{{ route('tasks.random') }}">🎲 Random Task</a></li>
                    </ul>
                </div>
                <a href="{{ route('tasks.index') }}" class="btn btn-ghost text-xl">
                    🔥 <span class="text-primary font-bold">YouDare</span>
                </a>
            </div>
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="{{ route('game') }}" class="{{ request()->routeIs('game') ? 'active' : '' }}">🎮 Play Game</a></li>
                    <li><a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.index') ? 'active' : '' }}">All Tasks</a></li>
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li><a href="{{ route('tasks.create') }}" class="{{ request()->routeIs('tasks.create') ? 'active' : '' }}">Create Task</a></li>
                        @endif
                    @endauth
                    <li><a href="{{ route('tags.index') }}" class="{{ request()->routeIs('tags.*') ? 'active' : '' }}">🏷️ Tags</a></li>
                    <li><a href="{{ route('tasks.random') }}">🎲 Random Task</a></li>
                </ul>
            </div>
            <div class="navbar-end gap-2">
                @auth
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
                    <a href="{{ route('login') }}" class="btn btn-ghost">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth

                <label class="swap swap-rotate btn btn-ghost btn-circle">
                    <input type="checkbox" class="theme-controller" value="dark" />
                    <svg class="swap-off fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/>
                    </svg>
                    <svg class="swap-on fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/>
                    </svg>
                </label>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="container mx-auto px-4 mt-4">
                <div role="alert" class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mx-auto px-4 mt-4">
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="py-8">
            <div class="container mx-auto px-4">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer footer-center p-10 bg-base-200 text-base-content rounded mt-12">
            <div>
                <p class="font-bold">
                    🔥 YouDare - Truth or Dare Game
                </p>
                <p>&copy; {{ date('Y') }} All rights reserved</p>
            </div>
        </footer>
    </div>

    @stack('scripts')

    <script>
        // Theme persistence
        const themeController = document.querySelector('.theme-controller');
        const htmlElement = document.documentElement;

        // Load saved theme on page load
        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', savedTheme);
        if (themeController) {
            themeController.checked = savedTheme === 'dark';
        }

        // Save theme when changed
        if (themeController) {
            themeController.addEventListener('change', function() {
                const theme = this.checked ? 'dark' : 'light';
                htmlElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            });
        }
    </script>
</body>
</html>
