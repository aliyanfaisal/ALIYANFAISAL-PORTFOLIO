<?php

namespace App\Http\Controllers;

use App\Enums\NewsletterFrequency;
use App\Mail\NewsletterOtpMail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\Category;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    /**
     * Step one: take the email and send a 6-digit code. Nobody is subscribed until they confirm it.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => $data['email']]);

        if ($subscriber->isVerified() && $subscriber->isSubscribed()) {
            return $this->backToForm($request)->with('status', "You're already subscribed.");
        }

        if ($subscriber->otpCooldownActive()) {
            return $this->backToForm($request)
                ->with('newsletter_pending_email', $subscriber->email)
                ->with('status', 'A code was just sent. Give it a minute before asking for another.');
        }

        Mail::to($subscriber->email)->send(new NewsletterOtpMail($subscriber->issueOtp()));

        return $this->backToForm($request)
            ->with('newsletter_pending_email', $subscriber->email)
            ->with('status', 'We emailed you a 6-digit code. Enter it below to confirm.');
    }

    /**
     * Step two: confirm the code, which activates the subscription and sends the welcome email.
     */
    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'code' => ['required', 'digits:6'],
        ]);

        $subscriber = NewsletterSubscriber::where('email', $data['email'])->first();

        if (! $subscriber || ! $subscriber->attemptOtp($data['code'])) {
            return $this->backToForm($request)
                ->with('newsletter_pending_email', $data['email'])
                ->withErrors(['code' => 'That code is wrong or has expired. Request a new one by submitting your email again.'], 'newsletter');
        }

        Mail::to($subscriber->email)->queue(new NewsletterWelcomeMail($subscriber));

        return $this->backToForm($request)->with('status', "You're subscribed! Check your inbox for a welcome email.");
    }

    private function backToForm(Request $request): RedirectResponse
    {
        $anchor = in_array($request->input('newsletter_form'), ['newsletter', 'newsletter-post'], true)
            ? $request->input('newsletter_form')
            : 'newsletter';

        return redirect(url()->previous().'#'.$anchor)->with('newsletter_form', $anchor);
    }

    public function preferences(string $token): View
    {
        $subscriber = $this->subscriber($token);

        return view('newsletter.preferences', [
            'subscriber' => $subscriber,
            'categories' => Category::orderBy('name')->get(),
            'selectedCategoryIds' => $subscriber->categories()->pluck('categories.id')->all(),
        ]);
    }

    public function updatePreferences(Request $request, string $token): RedirectResponse
    {
        $subscriber = $this->subscriber($token);

        $data = $request->validate([
            'frequency' => ['required', Rule::enum(NewsletterFrequency::class)],
            'topics' => ['required', 'in:all,selected'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        // Resubscribing from this page is only for people who already confirmed their address.
        $subscriber->update(['frequency' => $data['frequency']]);

        if ($subscriber->isVerified()) {
            $subscriber->update(['unsubscribed_at' => null]);
        }

        $subscriber->categories()->sync($data['topics'] === 'selected' ? ($data['categories'] ?? []) : []);

        return back()->with('status', "Saved — you'll now get the newsletter {$subscriber->frequency->label()}.");
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        $subscriber = $this->subscriber($token);

        if ($subscriber->isSubscribed()) {
            $subscriber->update(['unsubscribed_at' => now()]);
        }

        return redirect()->route('newsletter.preferences', $token)->with('status', "You've been unsubscribed. Sorry to see you go.");
    }

    /**
     * RFC 8058 one-click unsubscribe, hit directly by the mail client.
     */
    public function oneClickUnsubscribe(string $token): Response
    {
        $this->subscriber($token)->update(['unsubscribed_at' => now()]);

        return response()->noContent();
    }

    private function subscriber(string $token): NewsletterSubscriber
    {
        return NewsletterSubscriber::where('token', $token)->firstOrFail();
    }
}
