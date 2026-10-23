@props(['preheader' => null, 'footerNote' => null])
@php
    $settings = \App\Models\Setting::current();
    $links = array_filter([
        'About' => route('about'),
        'Products' => route('products.index'),
        'Open Source' => route('open-source.index'),
        'Blog' => route('blog.index'),
        'Contact' => route('contact.create'),
    ]);
    $social = array_filter([
        'GitHub' => $settings->github_url,
        'LinkedIn' => $settings->linkedin_url,
        'Telegram' => $settings->telegram_channel_url,
        'WhatsApp' => $settings->whatsapp_channel_url,
    ]);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $settings->site_name }}</title>
</head>
<body style="margin: 0; padding: 0; background: #f4f4f5; font-family: -apple-system, 'Segoe UI', Helvetica, Arial, sans-serif; color: #18181b; line-height: 1.6;">
    @if ($preheader)
        <div style="display: none; max-height: 0; overflow: hidden; opacity: 0; color: transparent;">{{ $preheader }}</div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background: #f4f4f5;">
        <tr>
            <td align="center" style="padding: 24px 12px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px;">
                    {{-- Header --}}
                    <tr>
                        <td style="background: #18181b; border-radius: 12px 12px 0 0; padding: 22px 28px;">
                            <a href="{{ route('home') }}" style="color: #ffffff; text-decoration: none; font-size: 18px; font-weight: bold; letter-spacing: 0.5px;">
                                {{ strtoupper($settings->site_name) }}<span style="color: #818cf8;">.</span>
                            </a>
                            <div style="margin-top: 2px; font-size: 12px; color: #a1a1aa;">Software Engineer &amp; AI Developer</div>
                        </td>
                    </tr>
                    <tr><td style="background: #6366f1; height: 3px; font-size: 0; line-height: 0;">&nbsp;</td></tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background: #ffffff; padding: 28px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background: #fafafa; border-top: 1px solid #e4e4e7; border-radius: 0 0 12px 12px; padding: 24px 28px; text-align: center; font-size: 12px; color: #71717a;">
                            <p style="margin: 0 0 12px; font-size: 13px;">
                                @foreach ($links as $label => $url)
                                    <a href="{{ $url }}" style="color: #3f3f46; text-decoration: none; font-weight: 600;">{{ $label }}</a>@if (! $loop->last)<span style="color: #d4d4d8;"> &nbsp;|&nbsp; </span>@endif
                                @endforeach
                            </p>

                            @if ($social)
                                <p style="margin: 0 0 14px;">
                                    @foreach ($social as $label => $url)
                                        <a href="{{ $url }}" style="color: #6366f1; text-decoration: none;">{{ $label }}</a>@if (! $loop->last)<span style="color: #d4d4d8;"> &nbsp;·&nbsp; </span>@endif
                                    @endforeach
                                </p>
                            @endif

                            @if ($footerNote)
                                <p style="margin: 0 0 14px; color: #71717a;">{{ $footerNote }}</p>
                            @endif

                            <p style="margin: 0; color: #a1a1aa;">
                                &copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.<br>
                                <a href="{{ route('privacy-policy') }}" style="color: #a1a1aa;">Privacy Policy</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
