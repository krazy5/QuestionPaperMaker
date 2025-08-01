<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the logged-in user has an active subscription
        // The where('ends_at', '>', now()) check ensures expired plans are not considered active.
        $isSubscribed = $request->user()->subscriptions()
                                ->where('status', 'active')
                                ->where('ends_at', '>', now())
                                ->exists();

        if (! $isSubscribed) {
            // If they are not subscribed, redirect them to the pricing page
            return redirect()->route('subscription.pricing')
                             ->with('error', 'You need an active subscription to access this feature.');
        }

        return $next($request);
    }
}