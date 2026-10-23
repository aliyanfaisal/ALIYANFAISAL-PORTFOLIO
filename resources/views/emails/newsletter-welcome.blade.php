<x-email.layout preheader="Thanks for subscribing — here's how to tune what you get.">
    <h2 style="margin: 0 0 12px; font-size: 22px;">You're in. Thanks for subscribing.</h2>
    <p style="margin: 0 0 12px;">You'll get the best new posts from the blog <strong>{{ strtolower($subscriber->frequency->label()) }}</strong>, covering {{ $subscriber->categories()->count() ? 'the topics you picked' : 'all topics' }}.</p>
    <p style="margin: 0 0 20px;">That's just the default. Prefer a different rhythm, or only certain topics?</p>
    <p style="margin: 0 0 20px;"><a href="{{ $subscriber->preferencesUrl() }}" style="display: inline-block; background: #6366f1; color: #ffffff; padding: 12px 24px; border-radius: 999px; text-decoration: none; font-weight: bold;">Choose how often and what you get</a></p>
    <p style="margin: 0; font-size: 12px; color: #a1a1aa;">You can unsubscribe anytime from that same page.</p>
</x-email.layout>
