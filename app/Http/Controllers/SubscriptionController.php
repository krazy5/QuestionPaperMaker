<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display the pricing page.
     */
    public function index()
    {
        // Get the user's current active subscription
        $activeSubscription = auth()->user()->subscriptions()
                                    ->where('status', 'active')
                                    ->where('ends_at', '>', now())
                                    ->latest('starts_at')
                                    ->first();

        // Pass the variable to the view
        return view('subscription.pricing', compact('activeSubscription'));
    }

    /**
     * Handle the demo subscription.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:Basic,Professional',
        ]);

        $hasActiveSubscription = auth()->user()->subscriptions()
                                    ->where('status', 'active')
                                    ->where('ends_at', '>', now())
                                    ->exists();

        if ($hasActiveSubscription) {
            return redirect()->route('subscription.pricing')
                             ->with('error', 'You already have an active subscription.');
        }

        Subscription::create([
            'user_id' => auth()->id(),
            'plan_name' => $request->plan,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMonth(),
            'status' => 'active',
        ]);

        return redirect()->route('dashboard')->with('success', 'Thank you for subscribing! Your plan is now active.');
    }
    // ADD THIS NEW METHOD
    public function cancel(Subscription $subscription)
    {
        // Security check: Ensure the logged-in user owns this subscription
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        // Update the status to 'cancelled'
        $subscription->update(['status' => 'cancelled']);

        // Redirect back to the profile page with a success message
        return redirect()->route('profile.edit')->with('status', 'subscription-cancelled');
    }
}
