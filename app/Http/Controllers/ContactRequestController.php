<?php

namespace App\Http\Controllers;
use App\Models\ContactRequest;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    public function create(string $plan)
    {
        // keep it simple; lock to known plans
        abort_unless(in_array($plan, ['Basic','Professional','Enterprise'], true), 404);

        return view('subscription.contact', [
            'plan'  => $plan,
            'user'  => auth()->user(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_name'      => 'required|string|in:Basic,Professional',
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|max:150',
            'phone'          => 'nullable|string|max:30',
            'preferred_date' => 'nullable|date|after_or_equal:today',
            'preferred_time' => 'nullable|date_format:H:i',
            'message'        => 'nullable|string|max:2000',
        ]);

        ContactRequest::create($data + [
            'user_id' => auth()->id(),
            'status'  => 'new',
        ]);

        // (optional) email yourself — add later if you want
        // \Mail::to(config('mail.from.address'))->send(new \App\Mail\NewContactRequestMail($contact));

        return redirect()
            ->route('dashboard')
            ->with('success', 'Thanks! We received your request. We’ll contact you shortly.');
    }
}
