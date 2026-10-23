<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create($data + ContactMessage::senderDetails($request));

        $notifyEmail = Setting::adminRecipient();

        if ($notifyEmail) {
            Mail::to($notifyEmail)->send(new ContactMessageMail($contactMessage));
        }

        return back()->with('status', 'Thanks for reaching out! I\'ll get back to you soon.');
    }
}
