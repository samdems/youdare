@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <!-- Success Icon -->
    <div class="mb-8">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-success/20 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>

    <!-- Success Message -->
    <h1 class="text-4xl font-bold mb-4">
        🎉 Welcome to <span class="text-primary">YouDare Pro!</span>
    </h1>
    <p class="text-xl text-base-content/70 mb-8">
        Your payment was successful and your account has been upgraded!
    </p>

    <!-- Pro Badge -->
    <div class="card bg-gradient-to-r from-primary to-secondary text-primary-content mb-8">
        <div class="card-body">
            <div class="flex items-center justify-center gap-4">
                <div class="badge badge-lg badge-neutral">PRO</div>
                <div class="text-left">
                    <h3 class="font-bold text-lg">{{ $user->name }}</h3>
                    <p class="text-sm opacity-80">{{ $user->email }}</p>
                </div>
            </div>
            <div class="divider">Lifetime Access</div>
            <p class="text-sm opacity-90">
                Your Pro account is active and will never expire!
            </p>
        </div>
    </div>

    <!-- What's Next -->
    <div class="card bg-base-200 mb-8">
        <div class="card-body">
            <h2 class="card-title justify-center mb-4">✨ What's Next?</h2>
            <div class="text-left space-y-3">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🎮</span>
                    <div>
                        <h4 class="font-bold">Start Playing</h4>
                        <p class="text-sm text-base-content/70">Jump into the game and enjoy all premium features!</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🔥</span>
                    <div>
                        <h4 class="font-bold">Explore Premium Content</h4>
                        <p class="text-sm text-base-content/70">Access exclusive dares and truths only available to Pro users.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-2xl">📊</span>
                    <div>
                        <h4 class="font-bold">Track Your Progress</h4>
                        <p class="text-sm text-base-content/70">View your game statistics and history.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('game') }}" class="btn btn-primary btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Start Playing
        </a>
        <a href="/" class="btn btn-outline btn-lg">
            Go to Homepage
        </a>
    </div>

    <!-- Support Info -->
    <div class="mt-8 p-4 bg-base-200 rounded-lg">
        <p class="text-sm text-base-content/70">
            Need help? Contact our support team and we'll be happy to assist you!
        </p>
    </div>
</div>
@endsection
