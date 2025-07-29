<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paper;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'institutes' => User::where('role', 'institute')->count(),
            'questions' => Question::count(),
            'papers' => Paper::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}