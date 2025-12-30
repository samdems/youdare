@extends('layouts.app')

@section('title', 'Create Promo Code')

@section('content')
<div class="mb-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Create Promo Code</h1>
        <p class="text-base-content/70 mt-2">Add a new discount code for pro subscriptions</p>
    </div>

    <!-- Breadcrumbs -->
    <div class="text-sm breadcrumbs mb-6">
        <ul>
            <li><a href="{{ route('promo-codes.index') }}">Promo Codes</a></li>
            <li>Create</li>
        </ul>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-error mb-6 max-w-2xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-bold">There were errors with your submission:</h3>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card bg-base-100 shadow-xl max-w-2xl">
        <div class="card-body">
            <form action="{{ route('promo-codes.store') }}" method="POST">
                @csrf

                <!-- Code -->
                <div class="form-control w-full mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Code <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="text"
                        name="code"
                        placeholder="e.g., SAVE20, WINTER2024"
                        class="input input-bordered w-full @error('code') input-error @enderror"
                        value="{{ old('code') }}"
                        required
                        autofocus
                        style="text-transform: uppercase;"
                        oninput="this.value = this.value.toUpperCase()"
                    >
                    <label class="label">
                        <span class="label-text-alt text-base-content/70">Enter a unique promo code (automatically converted to uppercase)</span>
                    </label>
                    @error('code')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Percent Off -->
                <div class="form-control w-full mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Percent Off <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="number"
                        name="percent_off"
                        placeholder="e.g., 20"
                        class="input input-bordered w-full @error('percent_off') input-error @enderror"
                        value="{{ old('percent_off') }}"
                        min="1"
                        max="100"
                        required
                    >
                    <label class="label">
                        <span class="label-text-alt text-base-content/70">Discount percentage (1-100)</span>
                    </label>
                    @error('percent_off')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="form-control mb-6">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input
                            type="checkbox"
                            name="is_active"
                            class="checkbox checkbox-primary"
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >
                        <div>
                            <span class="label-text font-semibold">Active</span>
                            <p class="text-sm text-base-content/70">Allow this promo code to be used</p>
                        </div>
                    </label>
                </div>

                <!-- Actions -->
                <div class="card-actions justify-end gap-2">
                    <a href="{{ route('promo-codes.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Create Promo Code
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info mt-6 max-w-2xl">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="font-bold">Tips for Creating Promo Codes</h3>
            <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li>Use memorable, easy-to-type codes</li>
                <li>Consider seasonal or event-based codes (e.g., SUMMER2024)</li>
                <li>Keep codes concise but descriptive</li>
                <li>You can deactivate codes later without deleting them</li>
            </ul>
        </div>
    </div>
</div>
@endsection
