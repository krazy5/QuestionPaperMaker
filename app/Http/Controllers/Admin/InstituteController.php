<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription; // <-- Import the Subscription model
use Illuminate\Http\Request;

class InstituteController extends Controller
{
    public function index()
    {
        $institutes = User::where('role', 'institute')->latest()->paginate(15);
        return view('admin.institutes.index', compact('institutes'));
    }

    public function show(User $institute)
    {
        $subscriptions = $institute->subscriptions()->latest('starts_at')->get();
        return view('admin.institutes.show', compact('institute', 'subscriptions'));
    }

    // ADD THIS ENTIRE NEW METHOD
    public function storeSubscription(Request $request, User $institute)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|in:Basic,Professional',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
        ]);

        // Create the subscription for the specified institute
        Subscription::create([
            'user_id' => $institute->id,
            'plan_name' => $validated['plan_name'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => 'active',
        ]);

        // Redirect back to the institute's management page with a success message
        return redirect()->route('admin.institutes.show', $institute)
                         ->with('success', 'Subscription activated successfully!');
    }

    // ADD THIS ENTIRE NEW METHOD
    public function cancelSubscription(Subscription $subscription)
    {
        // Update the status to 'cancelled'
        $subscription->update(['status' => 'cancelled']);

        // Redirect back to the institute's management page with a success message
        return redirect()->route('admin.institutes.show', $subscription->user_id)
                         ->with('success', 'Subscription has been cancelled successfully.');
    }
}