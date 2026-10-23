<x-email.layout preheader="Your confirmation code is {{ $code }}. It expires in 10 minutes.">
    <p style="margin: 0; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: #6366f1; font-weight: bold;">
        Confirm your subscription
    </p>
    <h1 style="margin: 6px 0 16px; font-size: 22px;">Your confirmation code</h1>
    <p style="margin: 0 0 20px;">Enter this code on the site to finish subscribing to the newsletter:</p>
    <p style="margin: 0 0 20px; padding: 18px; background: #f4f4f5; border: 1px dashed #d4d4d8; border-radius: 8px; text-align: center; font-size: 34px; font-weight: bold; letter-spacing: 10px; font-family: 'SFMono-Regular', Consolas, 'Courier New', monospace;">{{ $code }}</p>
    <p style="margin: 0; color: #71717a;">It expires in 10 minutes. If you didn't ask for this, you can ignore this email and nothing will happen.</p>
</x-email.layout>
