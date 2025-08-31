<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactRequestController extends Controller
{
    public function index(Request $request)
    {
        // Safe inputs
        $perPage = (int) $request->input('per_page', 15);
        if (! in_array($perPage, [10,15,25,50,100], true)) $perPage = 15;

        $sort = $request->input('sort','newest');
        if (! in_array($sort, ['newest','oldest'], true)) $sort = 'newest';

        $status = $request->input('status','');
        if ($status !== '' && ! in_array($status, ['new','contacted','scheduled','closed'], true)) {
            $status = '';
        }

        $plan = $request->input('plan','');
        if ($plan !== '' && ! in_array($plan, ['Basic','Professional'], true)) {
            $plan = '';
        }

        $q = trim((string) $request->input('q',''));
        $from = $request->input('from','');
        $to   = $request->input('to','');

        $query = ContactRequest::query();

        // Filters
        if ($q !== '') {
            $query->where(function($qq) use ($q) {
                $qq->where('name','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%")
                   ->orWhere('phone','like',"%{$q}%")
                   ->orWhere('message','like',"%{$q}%");
            });
        }
        if ($status !== '') {
            $query->where('status',$status);
        }
        if ($plan !== '') {
            $query->where('plan_name',$plan);
        }
        if ($from) {
            $query->whereDate('created_at','>=',$from);
        }
        if ($to) {
            $query->whereDate('created_at','<=',$to);
        }

        // Sort
        $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

        $requests = $query->paginate($perPage)->withQueryString();

        return view('admin.contact_requests.index', compact('requests'));
    }

    public function show(ContactRequest $contactRequest)
    {
        return view('admin.contact_requests.show', compact('contactRequest'));
    }

    public function update(Request $request, ContactRequest $contactRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:new,contacted,scheduled,closed',
        ]);

        $contactRequest->update(['status' => $data['status']]);

        return back()->with('success', 'Status updated.');
    }
}
