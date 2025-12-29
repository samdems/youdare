@extends('layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <!-- Cancel Icon -->
    <div class="mb-8">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-warning/20 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
    </div>

    <!-- Cancel Message -->
    <h1 class="text-4xl font-bold mb-4">
        Payment Cancelled
    </h1>
    <p class="text-xl text-base-content/70 mb-8">
        Your payment was cancelled. No charges were made to your account.
    </p>

    <!-- Info Card -->
    <div class="card bg-base-200 mb-8">
        <div class="card-body">
            <h2 class="card-title justify-center mb-4">Why upgrade to Pro?</h2>
            <div class="text-left space-y-3">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🎯</span>
                    <div>
                        <h4 class="font-bold">Unlimited Access</h4>
                        <p class="text-sm text-base-content/70">Get unlimited access to all premium tasks and features.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-2xl">💎</span>
                    <div>
                        <h4 class="font-bold">Lifetime Ownership</h4>
                        <p class="text-sm text-base-content/70">Pay once and enjoy Pro features forever.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🚀</span>
                    <div>
                        <h4 class="font-bold">Premium Experience</h4>
                        <p class="text-sm text-base-content/70">Enjoy an ad-free, enhanced gaming experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('stripe.go-pro') }}" class="btn btn-primary btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Try Again - Go Pro
        </a>
        <a href="{{ route('game') }}" class="btn btn-outline btn-lg">
            Continue with Free
        </a>
    </div>

    <!-- Support Info -->
    <div class="mt-8 p-4 bg-base-200 rounded-lg">
        <p class="text-sm text-base-content/70">
            Having issues with payment? Contact our support team for assistance.
        </p>
    </div>
</div>
@endsection
