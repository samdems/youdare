@extends('layouts.app')

@section('title', 'Promo Codes')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Promo Codes</h1>
            <p class="text-base-content/70 mt-2">Manage discount codes for pro subscriptions</p>
        </div>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('promo-codes.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Promo Code
                </a>
            @endif
        @endauth
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Promo Codes List -->
    @if($promoCodes->count() > 0)
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promoCodes as $promoCode)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-lg">{{ $promoCode->code }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="badge badge-success badge-lg">{{ $promoCode->percent_off }}% OFF</div>
                            </td>
                            <td>
                                @if($promoCode->is_active)
                                    <div class="badge badge-success">Active</div>
                                @else
                                    <div class="badge badge-ghost">Inactive</div>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm text-base-content/70">
                                    {{ $promoCode->created_at->format('M j, Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('promo-codes.edit', $promoCode) }}" class="btn btn-ghost btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('promo-codes.destroy', $promoCode) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this promo code?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm text-error">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $promoCodes->links() }}
        </div>
    @else
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body items-center text-center">
                <div class="text-6xl mb-4">🎟️</div>
                <h3 class="card-title">No promo codes found</h3>
                <p class="text-base-content/70 mb-6">Create your first promo code to offer discounts.</p>
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="card-actions">
                            <a href="{{ route('promo-codes.create') }}" class="btn btn-primary">
                                Create Your First Promo Code
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @endif
</div>

<!-- Info Alert -->
<div class="alert alert-info mt-8">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <div>
        <h3 class="font-bold">About Promo Codes</h3>
        <ul class="list-disc list-inside text-sm mt-2 space-y-1">
            <li><strong>Percent Off:</strong> Set a discount percentage (1-100%)</li>
            <li><strong>Active Status:</strong> Toggle codes on/off without deleting them</li>
            <li><strong>Unique Codes:</strong> Each code must be unique</li>
            <li><strong>Case Sensitive:</strong> Codes are case-sensitive when users enter them</li>
        </ul>
    </div>
</div>
@endsection
