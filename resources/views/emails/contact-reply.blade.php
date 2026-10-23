<x-email.layout preheader="A reply to your message to {{ \App\Models\Setting::current()->site_name }}">
    <p style="margin: 0 0 16px;">Hi {{ $contactMessage->name }},</p>

    <div style="margin: 0 0 24px; white-space: pre-line;">{{ $reply->body }}</div>

    <p style="margin: 0 0 24px;">
        Best regards,<br>
        <strong>{{ \App\Models\Setting::current()->site_name }}</strong>
    </p>

    <div style="border-top: 1px solid #e4e4e7; padding-top: 16px;">
        <p style="margin: 0 0 8px; font-size: 12px; color: #a1a1aa;">
            Your message{{ $contactMessage->created_at ? ' on '.$contactMessage->created_at->format('M j, Y') : '' }}@if ($contactMessage->subject) · {{ $contactMessage->subject }}@endif
        </p>
        <p style="margin: 0; white-space: pre-line; border-left: 3px solid #e4e4e7; padding-left: 12px; color: #71717a; font-size: 14px;">{{ $contactMessage->message }}</p>
    </div>
</x-email.layout>
