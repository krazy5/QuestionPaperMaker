<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InstituteController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100], true)) $perPage = 15;

        $sort = $request->input('sort', 'newest');
        if (!in_array($sort, ['newest','oldest','name_asc','name_desc'], true)) $sort = 'newest';

        $search       = trim((string) $request->input('search', ''));
        $activeFilter = $request->input('active_filter'); // '', 'with', 'without'

        $query = User::query()->where('role', 'institute');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('institute_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($activeFilter === 'with') {
            $query->whereHas('subscriptions', function ($q) {
                $q->where('status', 'active')->where('ends_at', '>', now());
            });
        } elseif ($activeFilter === 'without') {
            $query->whereDoesntHave('subscriptions', function ($q) {
                $q->where('status', 'active')->where('ends_at', '>', now());
            });
        }

        switch ($sort) {
            case 'oldest':   $query->orderBy('created_at', 'asc'); break;
            case 'name_asc': $query->orderByRaw('COALESCE(institute_name, name) ASC'); break;
            case 'name_desc':$query->orderByRaw('COALESCE(institute_name, name) DESC'); break;
            default:         $query->orderBy('created_at', 'desc'); break;
        }

        $institutes = $query->paginate($perPage)->withQueryString();

        return view('admin.institutes.index', compact('institutes'));
    }

    public function create()
    {
        return view('admin.institutes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institute_name' => 'nullable|string|max:255',
            'name'           => 'required|string|max:255',
            'email'          => 'required|email:rfc,dns|unique:users,email',
            'password'       => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'institute_name' => $validated['institute_name'] ?? null,
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => 'institute',
        ]);

        return redirect()->route('admin.institutes.show', $user)
                         ->with('success', 'Institute account created successfully!');
    }

    public function show(User $institute)
    {
        $subscriptions = $institute->subscriptions()->latest('starts_at')->get();
        return view('admin.institutes.show', compact('institute', 'subscriptions'));
    }

    public function storeSubscription(Request $request, User $institute)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|in:Basic,Professional',
            'starts_at' => 'required|date',
            'ends_at'   => 'required|date|after_or_equal:starts_at',
        ]);

        Subscription::create([
            'user_id'   => $institute->id,
            'plan_name' => $validated['plan_name'],
            'starts_at' => $validated['starts_at'],
            'ends_at'   => $validated['ends_at'],
            'status'    => 'active',
        ]);

        return redirect()->route('admin.institutes.show', $institute)
                         ->with('success', 'Subscription activated successfully!');
    }

    public function cancelSubscription(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);

        return redirect()->route('admin.institutes.show', $subscription->user_id)
                         ->with('success', 'Subscription has been cancelled successfully.');
    }
}
