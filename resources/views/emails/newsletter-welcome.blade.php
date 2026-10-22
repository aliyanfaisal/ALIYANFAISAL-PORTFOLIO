<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; color: #18181b; line-height: 1.6; max-width: 560px;">
    <h2 style="margin-bottom: 4px;">You're in. Thanks for subscribing.</h2>
    <p>You'll get the best new posts from the blog <strong>{{ strtolower($subscriber->frequency->label()) }}</strong>, covering {{ $subscriber->categories()->count() ? 'the topics you picked' : 'all topics' }}.</p>
    <p>That's just the default. Prefer a different rhythm, or only certain topics?</p>
    <p><a href="{{ $subscriber->preferencesUrl() }}" style="display: inline-block; background: #6366f1; color: #ffffff; padding: 10px 20px; border-radius: 999px; text-decoration: none; font-weight: bold;">Choose how often and what you get</a></p>
    <p style="font-size: 12px; color: #a1a1aa;">You can unsubscribe anytime from that same page.</p>
</body>
</html>
