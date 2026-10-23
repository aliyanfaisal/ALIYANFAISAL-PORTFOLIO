<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Rules\SafeComment;
use App\Models\Setting;
use App\Services\TurnstileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request, TurnstileService $turnstile): RedirectResponse
    {
        // Honeypot: real visitors never see or fill this field.
        if ($request->filled('website')) {
            return back()->with('status', 'Thanks for reaching out! I\'ll get back to you soon.');
        }

        // Visitors may leave an email or phone number in a message; everything else is filtered.
        $allow = ['an email address', 'a phone number'];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', new SafeComment($allow, 'sent')],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150', new SafeComment($allow, 'sent')],
            'message' => ['required', 'string', 'max:5000', new SafeComment($allow, 'sent')],
            'cf-turnstile-response' => ['required', 'string'],
        ], ['cf-turnstile-response.required' => 'Please complete the verification.']);

        if (! $turnstile->verify($data['cf-turnstile-response'], $request->ip())) {
            return back()->withErrors(['cf-turnstile-response' => 'Verification failed. Please try again.'])->withInput();
        }

        unset($data['cf-turnstile-response']);

        $contactMessage = ContactMessage::create($data + ContactMessage::senderDetails($request));

        $notifyEmail = Setting::adminRecipient();

        if ($notifyEmail) {
            Mail::to($notifyEmail)->send(new ContactMessageMail($contactMessage));
        }

        return back()->with('status', 'Thanks for reaching out! I\'ll get back to you soon.');
    }
}
