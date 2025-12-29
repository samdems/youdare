@extends('layouts.app')

@section('title', 'Get Started')

@section('content')
<div class="max-w-md mx-auto">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold mb-4">🚀 Get Started</h2>

            <p class="text-base-content/70 mb-4">
                Enter your email address and we'll send you a magic link. If you don't have an account, we'll create one for you automatically. No password needed!
            </p>

            @if ($errors->any())
                <div role="alert" class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-control w-full mb-6">
                    <label class="label">
                        <span class="label-text">Email Address</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        placeholder="your@email.com"
                        class="input input-bordered w-full @error('email') input-error @enderror"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    />
                    @error('email')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="card-actions flex flex-col gap-2">
                    <button type="submit" class="btn btn-primary w-full">
                        ✨ Send Magic Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
