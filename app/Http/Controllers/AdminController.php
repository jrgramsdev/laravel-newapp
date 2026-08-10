<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the list of contact submissions.
     */
    public function index(): View
    {
        $messages = ContactMessage::latest()->paginate(10);

        return view('admin.index', compact('messages'));
    }
}
