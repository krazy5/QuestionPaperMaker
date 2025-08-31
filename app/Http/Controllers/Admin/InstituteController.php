<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ManualSubscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controller responsible for managing institutes from the admin panel.
 *
 * This controller allows administrators to list institutes, view subscription history,
 * assign new manual subscriptions, and cancel existing subscriptions.
 */
class InstituteController extends Controller
{
    /**
     * Display a paginated list of institutes with optional filtering.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        $sort = $request->input('sort', 'newest');
        if (!in_array($sort, ['newest', 'oldest', 'name_asc', 'name_desc'], true)) {
            $sort = 'newest';
        }

        $search       = trim((string) $request->input('search', ''));
        $activeFilter = $request->input('active_filter'); // '', 'with', 'without'

        $query = User::query()->where('role', 'institute');

        // search by institute name, fallback to name/email
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('institute_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // filter by manual subscription status
        // "with" should return institutes that currently have an active manual subscription
        // i.e. subscriptions where the start time is in the past and the end time is in the future
        if ($activeFilter === 'with') {
            $query->whereHas('manualSubscriptions', function ($q) {
                $q->where('status', 'active')
                  ->where('starts_at', '<=', now())
                  ->where('ends_at', '>',  now());
            });
        } elseif ($activeFilter === 'without') {
            // "without" should return institutes that do not have a currently active manual subscription
            $query->whereDoesntHave('manualSubscriptions', function ($q) {
                $q->where('status', 'active')
                  ->where('starts_at', '<=', now())
                  ->where('ends_at', '>',  now());
            });
        }

        // sort results
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderByRaw('COALESCE(institute_name, name) ASC');
                break;
            case 'name_desc':
                $query->orderByRaw('COALESCE(institute_name, name) DESC');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $institutes = $query->paginate($perPage)->withQueryString();

        return view('admin.institutes.index', compact('institutes'));
    }

    /**
     * Display the specified institute along with its subscription history.
     *
     * @param User $institute
     * @return \Illuminate\View\View
     */
    public function show(User $institute)
    {
        $subscriptions = $institute->manualSubscriptions()->latest('starts_at')->get();
        return view('admin.institutes.show', compact('institute', 'subscriptions'));
    }

    /**
     * Activate a new manual subscription for an institute.
     *
     * Validates that the date/time inputs follow the `datetime-local` format (Y-m-d\TH:i),
     * cancels any existing active subscriptions, and stores the new subscription with the
     * precise start and end datetimes.
     *
     * @param Request $request
     * @param User $institute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSubscription(Request $request, User $institute)
    {
        // Validate inputs. We accept a generic date string so that we can parse
        // various formats (e.g. ISO 8601 from datetime-local or formats like
        // "mm/dd/yyyy hh:mm AM/PM" from date pickers). The date rule ensures
        // the value can be parsed by Carbon. The after_or_equal rule ensures
        // the end date/time is not before the start.
        $validated = $request->validate([
            'plan_name' => 'required|string|in:Basic,Professional,Enterprise',
            'starts_at' => 'required|date',
            'ends_at'   => 'required|date|after_or_equal:starts_at',
        ]);

        // Cancel any existing active manual subscriptions for this institute.
        // We cancel subscriptions with status 'active' that have not yet ended,
        // regardless of whether they have started. This prevents overlapping future
        // subscriptions and ensures the institute can have only one pending/active plan at a time.
        $institute->manualSubscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->update(['status' => 'cancelled']);

        // Parse the start and end times using Carbon with the application timezone.
        $starts = Carbon::parse($validated['starts_at'], config('app.timezone'));
        $ends   = Carbon::parse($validated['ends_at'],   config('app.timezone'));

        // Create the new subscription. By storing Carbon instances, we ensure
        // the exact date and time (including minutes) are saved to the database.
        ManualSubscription::create([
            'user_id'   => $institute->id,
            'plan_name' => $validated['plan_name'],
            'starts_at' => $starts,
            'ends_at'   => $ends,
            'status'    => 'active',
        ]);

        return redirect()->route('admin.institutes.show', $institute)
            ->with('success', 'Subscription activated successfully!');
    }

    /**
     * Cancel the specified manual subscription.
     *
     * This action marks the subscription as cancelled and then redirects back to the institute page.
     *
     * @param ManualSubscription $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription(ManualSubscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);

        return redirect()
            ->route('admin.institutes.show', $subscription->user)
            ->with('success', 'Subscription has been cancelled successfully.');
    }

    /**
     * Show the form for creating a new institute.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.institutes.create');
    }

    /**
     * Store a newly created institute in storage.
     *
     * This method demonstrates how to create an institute and optionally attach
     * an initial manual subscription. It expects form fields for the institute's
     * name, email, and role, as well as optional plan_name, starts_at, and ends_at
     * when adding an initial subscription.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate user-related fields. Extend these rules as needed.
        $validatedUser = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|in:institute',
            'institute_name' => 'nullable|string|max:255',
        ]);

        // Create the institute user
        $institute = User::create([
            'name'           => $validatedUser['name'],
            'email'          => $validatedUser['email'],
            'password'       => bcrypt($validatedUser['password']),
            'role'           => 'institute',
            'institute_name' => $validatedUser['institute_name'] ?? null,
        ]);

        // If subscription details are provided, validate and add a manual subscription
        if ($request->filled('plan_name')) {
            $validatedSub = $request->validate([
                'plan_name' => 'required|string|in:Basic,Professional,Enterprise',
                'starts_at' => 'required|date',
                'ends_at'   => 'required|date|after_or_equal:starts_at',
            ]);

            // Parse times with timezone awareness
            $starts = Carbon::parse($validatedSub['starts_at'], config('app.timezone'));
            $ends   = Carbon::parse($validatedSub['ends_at'],   config('app.timezone'));

            ManualSubscription::create([
                'user_id'   => $institute->id,
                'plan_name' => $validatedSub['plan_name'],
                'starts_at' => $starts,
                'ends_at'   => $ends,
                'status'    => 'active',
            ]);
        }

        return redirect()->route('admin.institutes.show', $institute)
            ->with('success', 'Institute created successfully.');
    }
}