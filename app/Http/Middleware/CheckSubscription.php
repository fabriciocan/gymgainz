<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasActiveAccess()) {
            return $next($request);
        }

        return response()->json(['error' => 'subscription_required'], 403);
    }
}
