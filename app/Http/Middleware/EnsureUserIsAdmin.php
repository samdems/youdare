<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Unauthenticated.",
                    ],
                    401,
                );
            }
            return redirect()
                ->route("login")
                ->with("error", "You must be logged in to access this page.");
        }

        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Unauthorized. Admin access required.",
                    ],
                    403,
                );
            }
            abort(403, "Unauthorized. Admin access required.");
        }

        return $next($request);
    }
}
