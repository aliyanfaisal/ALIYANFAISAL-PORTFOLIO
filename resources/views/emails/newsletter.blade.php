<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin: 0; padding: 24px 12px; background: #f4f4f5; font-family: Arial, sans-serif; color: #18181b; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 28px;">
        <p style="margin: 0; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: #6366f1; font-weight: bold;">
            {{ $newsletter->frequency->label() }} digest
        </p>
        <h1 style="margin: 6px 0 20px; font-size: 22px;">{{ $newsletter->subject }}</h1>

        @foreach ($posts as $post)
            <div style="margin-bottom: 24px; padding-bottom: 24px; {{ $loop->last ? '' : 'border-bottom: 1px solid #e4e4e7;' }}">
                @if ($post->imageUrl())
                    <a href="{{ route('blog.show', $post) }}"><img src="{{ $post->imageUrl() }}" alt="" width="544" style="width: 100%; height: auto; border-radius: 8px; margin-bottom: 12px;"></a>
                @endif
                <h2 style="margin: 0 0 6px; font-size: 18px;">
                    <a href="{{ route('blog.show', $post) }}" style="color: #18181b; text-decoration: none;">{{ $post->title }}</a>
                </h2>
                @if ($post->excerpt)
                    <p style="margin: 0 0 8px; color: #52525b;">{{ \Illuminate\Support\Str::limit($post->excerpt, 180) }}</p>
                @endif
                <a href="{{ route('blog.show', $post) }}" style="color: #6366f1; font-weight: bold; text-decoration: none;">Read the post →</a>
            </div>
        @endforeach

        <p style="margin: 24px 0 0; font-size: 12px; color: #a1a1aa;">
            You're receiving this because you subscribed at aliyanfaisal.com.
            <a href="{{ $subscriber->preferencesUrl() }}" style="color: #71717a;">Change how often you get it</a> or
            <a href="{{ $subscriber->preferencesUrl() }}" style="color: #71717a;">unsubscribe</a>.
        </p>
    </div>
</body>
</html>
