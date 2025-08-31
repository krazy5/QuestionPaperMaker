<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManualSubscription;
use Carbon\Carbon;


class SubscriptionController extends Controller
{
    /**
     * Display the pricing page.
     */
    public function index()
        {
            $activeSubscription = auth()->user()->activeManualSubscription();
        return view('subscription.pricing', compact('activeSubscription'));

        }

        
        
         public function subscribe(Request $request)
            {
                $user = $request->user();

                // Prevent new checkout if a manual subscription is currently active
    if ($user->manualSubscriptions()
        ->where('status', 'active')
        ->where('starts_at', '<=', now())
        ->where('ends_at', '>', now())
        ->exists()) {
        return back()->with('error', 'You already have an active manual subscription.');
    }

                // map form "plan" value to real Stripe price IDs
                $planMap = [
                    'Basic'        => 'price_1RzX6cSCyX2t6sdoio3fO4Y9', // 👈 your Basic Price ID
                    'Professional' => 'price_yyyyyyyyyyyyyyyy', // 👈 your Pro Price ID
                ];

                $plan = $request->input('plan');

                if (!isset($planMap[$plan])) {
                    return back()->with('error', 'Invalid plan selected.');
                }

                 try {
                    return $user->newSubscription('default', $planMap[$plan])
                            ->checkout([
        'payment_method_types'      => ['card'],            // card only for now
        'billing_address_collection'=> 'required',          // ✅ collect full billing address
        'customer_update'           => [                    // ✅ let Checkout write it onto the Customer
            'address' => 'auto',
            'name'    => 'auto',
        ],
        // (optional) also collect a phone if you want:
        // 'customer_update' => ['address' => 'auto', 'name' => 'auto', 'shipping' => 'auto'],

        'success_url' => route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => route('subscription.pricing'),
    ]);
                    } catch (\Throwable $e) {
                        report($e); // goes to storage/logs/laravel.log
                        return back()->with('error', 'Checkout error: ' . $e->getMessage());
                    }
            }
        
        
        
        
        
        // public function subscribe(Request $request)
        // {
        //     $hasActiveSubscription = auth()->user()->manualSubscriptions()
        //             ->where('status', 'active')
        //             ->where('ends_at', '>', now())
        //             ->exists();

        //         if ($hasActiveSubscription) {
        //             return redirect()->route('subscription.pricing')
        //                             ->with('error', 'You already have an active subscription.');
        //         }

        //         ManualSubscription::create([
        //             'user_id'   => auth()->id(),
        //             'plan_name' => $request->plan,
        //             'starts_at' => Carbon::now(),
        //             'ends_at'   => Carbon::now()->addMonth(),
        //             'status'    => 'active',
        //         ]);

        //         return redirect()->route('dashboard')->with('success', 'Thank you for subscribing! Your plan is now active.');

        // }

        // still receives your old param, but fetch the row from manual_subscriptions
        public function cancel(ManualSubscription $subscription)
        {
            if ($subscription->user_id !== auth()->id()) {
                abort(403);
            }

            $subscription->update(['status' => 'cancelled']);

            return redirect()->route('profile.edit')->with('status', 'subscription-cancelled');
        }

}
