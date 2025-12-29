@extends('layouts.app')

@section('title', 'Go Pro')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold mb-4">
            🚀 Upgrade to <span class="text-primary">YouDare Pro</span>
        </h1>
        <p class="text-xl text-base-content/70">
            Unlock premium features and take your Truth or Dare experience to the next level!
        </p>
    </div>

    <!-- Pricing Card -->
    <div class="card bg-base-100 shadow-xl border-2 border-primary mb-8">
        <div class="card-body">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex-1">
                    <div class="badge badge-primary badge-lg mb-4">LIFETIME ACCESS</div>
                    <h2 class="card-title text-3xl mb-2">Pro Account</h2>
                    <p class="text-base-content/70">One-time payment. Yours forever.</p>

                    <div class="mt-6">
                        <div class="text-5xl font-bold">
                            ${{ number_format($amount / 100, 2) }}
                        </div>
                        <p class="text-sm text-base-content/60 mt-1">One-time payment</p>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <form action="{{ route('stripe.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Go Pro Now
                        </button>
                    </form>
                    <p class="text-xs text-center mt-2 text-base-content/60">
                        Secure checkout with Stripe
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="mb-8">
        <h3 class="text-2xl font-bold mb-6 text-center">✨ Pro Features</h3>

        <div class="grid md:grid-cols-2 gap-4">
            <!-- Feature 1 - Main Feature -->
            <div class="card bg-gradient-to-br from-primary/20 to-secondary/20 border-2 border-primary md:col-span-2">
                <div class="card-body">
                    <h4 class="card-title text-xl">
                        <span class="text-3xl mr-2">🔥</span>
                        Unlock Heat Levels 3, 4, and 5
                    </h4>
                    <p class="text-base-content/70">
                        Access adult-only content with heat levels 3-5! From light teasing and partial undressing to full nudity and explicit party games. Free users are limited to family-friendly levels 1-2 only.
                    </p>
                </div>
            </div>


        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mb-8">
        <h3 class="text-2xl font-bold mb-6 text-center">❓ Frequently Asked Questions</h3>

        <div class="join join-vertical w-full">
            <div class="collapse collapse-arrow join-item border border-base-300">
                <input type="radio" name="faq-accordion" checked="checked" />
                <div class="collapse-title text-lg font-medium">
                    Is this really a lifetime subscription?
                </div>
                <div class="collapse-content">
                    <p>Yes! Pay once and enjoy Pro features forever. No recurring charges, no hidden fees.</p>
                </div>
            </div>

            <div class="collapse collapse-arrow join-item border border-base-300">
                <input type="radio" name="faq-accordion" />
                <div class="collapse-title text-lg font-medium">
                    Is the payment secure?
                </div>
                <div class="collapse-content">
                    <p>Absolutely! All payments are processed securely through Stripe, one of the world's most trusted payment processors.</p>
                </div>
            </div>

            <div class="collapse collapse-arrow join-item border border-base-300">
                <input type="radio" name="faq-accordion" />
                <div class="collapse-title text-lg font-medium">
                    Can I get a refund?
                </div>
                <div class="collapse-content">
                    <p>We offer a 30-day money-back guarantee. If you're not satisfied, contact our support team for a full refund.</p>
                </div>
            </div>

            <div class="collapse collapse-arrow join-item border border-base-300">
                <input type="radio" name="faq-accordion" />
                <div class="collapse-title text-lg font-medium">
                    When will I get access to Pro features?
                </div>
                <div class="collapse-content">
                    <p>Immediately after payment! Your account will be upgraded instantly and you can start enjoying Pro features right away.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="text-center">
        <div class="card bg-gradient-to-r from-primary to-secondary text-primary-content">
            <div class="card-body">
                <h3 class="card-title text-2xl justify-center mb-2">Ready to upgrade?</h3>
                <p class="mb-4">Join all the other Pro users and unlock the full potential of YouDare!</p>
                <form action="{{ route('stripe.checkout') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="btn btn-neutral btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Upgrade to Pro - ${{ number_format($amount / 100, 2) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
