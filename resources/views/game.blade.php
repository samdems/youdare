@extends('layouts.app')

@section('title', 'Play Game')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Vue Component with Tag Support -->
    <game-manager
        data-is-pro="{{ Auth::check() && Auth::user()->isPro() ? '1' : '0' }}"
    ></game-manager>

    <script>
        // Pass isPro as global variable for Vue to access
        window.USER_IS_PRO = {{ Auth::check() && Auth::user()->isPro() ? 'true' : 'false' }};
    </script>
</div>
@endsection
