<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemoRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: real visitors never see or fill this field.
        if ($request->filled('website')) {
            return $this->thanks();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'team_size' => ['nullable', 'in:1-5,6-15,16-50,50+'],
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        $message = ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => 'ManaJet demo request',
            'message' => implode("\n", array_filter([
                'Product: ManaJet',
                $data['company'] ? 'Company: '.$data['company'] : null,
                $data['team_size'] ? 'Team size: '.$data['team_size'] : null,
                '',
                $data['message'] ?? null,
            ], fn ($line) => $line !== null)),
        ]);

        if ($notifyEmail = config('mail.contact_recipient')) {
            Mail::to($notifyEmail)->send(new ContactMessageMail($message));
        }

        return $this->thanks();
    }

    private function thanks(): RedirectResponse
    {
        return redirect(route('products.show', 'manajet').'#demo')
            ->with('demo_status', 'Thanks! Your demo request is in. I\'ll reply by email shortly.');
    }
}
