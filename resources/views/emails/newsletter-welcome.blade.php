@php
    $topics = $subscriber->categories()->count() ? 'the topics you picked' : 'all topics';
@endphp
<x-email.layout preheader="You're subscribed. Here's what to expect and how to tune it.">
    <p style="margin: 0 0 16px; text-align: center;">
        <span style="display: inline-block; width: 52px; height: 52px; line-height: 52px; border-radius: 50%; background: #eef2ff; color: #6366f1; font-size: 26px; font-weight: bold;">&#10003;</span>
    </p>
    <p style="margin: 0; text-align: center; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: #6366f1; font-weight: bold;">
        Subscription confirmed
    </p>
    <h1 style="margin: 6px 0 12px; text-align: center; font-size: 26px; line-height: 1.25;">You're in. Thanks for subscribing.</h1>
    <p style="margin: 0 0 24px; text-align: center; color: #52525b;">
        Hi, glad to have you. Here's what lands in your inbox.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px;">
        <tr>
            <td style="padding: 14px 16px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 10px;">
                <p style="margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #a1a1aa; font-weight: bold;">How often</p>
                <p style="margin: 2px 0 0; font-size: 16px; font-weight: bold;">{{ ucfirst(strtolower($subscriber->frequency->label())) }}</p>
            </td>
        </tr>
        <tr><td style="height: 10px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
        <tr>
            <td style="padding: 14px 16px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 10px;">
                <p style="margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #a1a1aa; font-weight: bold;">What you get</p>
                <p style="margin: 2px 0 0; font-size: 16px; font-weight: bold;">The best new posts, covering {{ $topics }}</p>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 16px; text-align: center; color: #52525b;">
        That's just the default. Prefer a different rhythm, or only certain topics?
    </p>
    <p style="margin: 0 0 24px; text-align: center;">
        <a href="{{ $subscriber->preferencesUrl() }}" style="display: inline-block; background: #6366f1; color: #ffffff; padding: 14px 28px; border-radius: 999px; text-decoration: none; font-weight: bold; font-size: 15px;">Choose how often and what you get</a>
    </p>

    <p style="margin: 0; text-align: center; font-size: 12px; color: #a1a1aa;">
        You can unsubscribe anytime from that same page.
    </p>
</x-email.layout>
