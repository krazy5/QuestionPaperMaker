<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        // ✅ Use your manual subscriptions table (date columns!)
        $isSubscribed = $user->manualSubscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();

        if (! $isSubscribed) {
            return redirect()
                ->route('subscription.pricing')
                ->with('error', 'You need an active subscription to access this feature.');
        }

        return $next($request);
    }
}
