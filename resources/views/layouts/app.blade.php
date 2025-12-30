<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'YouDare') }} - @yield('title', 'Truth or Dare')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon_io/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon_io/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon_io/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon_io/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('favicon_io/site.webmanifest') }}">

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

        /* Logo SVG Styling */
        .logo-svg {
            filter: brightness(0) saturate(100%) invert(45%) sepia(89%) saturate(2384%) hue-rotate(226deg) brightness(102%) contrast(101%);
        }

        [data-theme="dark"] .logo-svg {
            filter: brightness(0) saturate(100%) invert(100%) sepia(0%) saturate(7500%) hue-rotate(324deg) brightness(104%) contrast(104%);
        }

        /* Pro Badge Styling */
        .badge-pro {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            animation: pulse-glow 2s ease-in-out infinite;
            border: 2px solid rgba(255, 255, 255, 0.2);
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .badge-pro:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.6);
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            }
            50% {
                box-shadow: 0 4px 16px rgba(102, 126, 234, 0.5);
            }
        }

        .pro-indicator {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .pro-star-shine {
            filter: drop-shadow(0 0 2px rgba(255, 255, 255, 0.8));
        }
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
                        @auth
                            @if(Auth::user()->isPro())
                                <li class="menu-title">
                                    <div class="badge badge-pro badge-sm gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        PRO MEMBER
                                    </div>
                                </li>
                            @endif
                        @endauth
                        <li><a href="{{ route('game') }}">🎮 Play Game</a></li>
                        @auth
                            @if(!Auth::user()->isPro())
                                <li><a href="{{ route('stripe.go-pro') }}" class="text-primary font-bold">⚡ Go Pro</a></li>
                            @endif
                            @if(Auth::user()->isAdmin())
                                <li><a href="{{ route('tasks.index') }}">All Tasks</a></li>
                                <li><a href="{{ route('tasks.create') }}">Create Task</a></li>
                                <li><a href="{{ route('tags.index') }}">🏷️ Tags</a></li>
                                <li><a href="{{ route('stats.index') }}">📊 Stats</a></li>
                            @endif
                        @endauth

                    </ul>
                </div>
                <a href="/" class="btn btn-ghost text-xl flex items-center gap-2">
                    <img src="{{ asset('logo.svg') }}" alt="YouDare Logo" class="h-8 logo-svg">
                </a>
            </div>
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="{{ route('game') }}" class="{{ request()->routeIs('game') ? 'active' : '' }}">🎮 Play Game</a></li>
                    @auth
                        @if(!Auth::user()->isPro())
                            <li><a href="{{ route('stripe.go-pro') }}" class="text-primary font-bold {{ request()->routeIs('stripe.*') ? 'active' : '' }}">⚡ Go Pro</a></li>
                        @endif
                        @if(Auth::user()->isAdmin())
                            <li><a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.index') ? 'active' : '' }}">All Tasks</a></li>
                            <li><a href="{{ route('tasks.create') }}" class="{{ request()->routeIs('tasks.create') ? 'active' : '' }}">Create Task</a></li>
                            <li><a href="{{ route('tags.index') }}" class="{{ request()->routeIs('tags.*') ? 'active' : '' }}">🏷️ Tags</a></li>
                            <li><a href="{{ route('stats.index') }}" class="{{ request()->routeIs('stats.*') ? 'active' : '' }}">📊 Stats</a></li>
                        @endif
                    @endauth

                </ul>
            </div>
            <div class="navbar-end gap-2">
                @auth
                    @if(Auth::user()->isPro())
                        <div class="tooltip tooltip-bottom" data-tip="✨ Pro Member - Lifetime Access ✨">
                            <div class="badge badge-pro badge-lg gap-2 hidden sm:flex text-base px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 pro-star-shine" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="font-extrabold">PRO MEMBER</span>
                            </div>
                        </div>
                    @endif
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder relative">
                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                <span class="text-xl">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                            @if(Auth::user()->isPro())
                                <span class="absolute -top-1 -right-1 flex h-5 w-5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full pro-indicator opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-5 w-5 pro-indicator items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </span>
                                </span>
                            @endif
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                            <li class="menu-title flex flex-row items-center gap-2">
                                <span>{{ Auth::user()->name }}</span>
                                @if(Auth::user()->isPro())
                                    <span class="badge badge-pro badge-sm gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        PRO
                                    </span>
                                @endif
                            </li>
                            @if(!Auth::user()->isPro())
                                <li><a href="{{ route('stripe.go-pro') }}" class="text-primary font-bold">⚡ Go Pro</a></li>
                            @endif
                            @if(Auth::user()->isAdmin())
                                <li><a href="{{ route('tasks.create') }}">➕ Create Task</a></li>
                                <li><a href="{{ route('tags.create') }}">🏷️ Create Tag</a></li>
                                <li><a href="{{ route('stats.index') }}">📊 View Stats</a></li>
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
                    <a href="{{ route('login') }}" class="btn btn-primary">🔐 Get Started</a>
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
                <div class="flex items-center justify-center gap-2 mb-2">
                    <img src="{{ asset('logo.svg') }}" alt="YouDare Logo" class="h-10 logo-svg">
                </div>
                <p class="font-bold">
                    YouDare - Truth or Dare Game
                </p>
                <div class="flex gap-4 text-sm">
                    <a href="{{ route('privacy') }}" class="link link-hover">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="link link-hover">Terms of Service</a>
                </div>
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
