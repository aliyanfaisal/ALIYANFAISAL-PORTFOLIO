{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ config('seo.site_name') }} — Blog</title>
        <link>{{ route('blog.index') }}</link>
        <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />
        <description>{{ config('seo.descriptions.blog') }}</description>
        <language>en</language>
        @if ($posts->isNotEmpty())
            <lastBuildDate>{{ $posts->max('updated_at')->toRssString() }}</lastBuildDate>
        @endif
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('blog.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post) }}</guid>
                <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">{{ config('seo.person.name') }}</dc:creator>
                @foreach ($post->categories as $category)
                    <category>{{ $category->name }}</category>
                @endforeach
                <description>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($post->body_html))), 300) }}</description>
                @if ($post->image_path)
                    <enclosure url="{{ $post->imageUrl() }}" length="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists($post->image_path) ? \Illuminate\Support\Facades\Storage::disk('public')->size($post->image_path) : 0 }}" type="{{ str_ends_with($post->image_path, '.webp') ? 'image/webp' : 'image/jpeg' }}" />
                    <media:content url="{{ $post->imageUrl() }}" medium="image" @if ($post->image_width) width="{{ $post->image_width }}" height="{{ $post->image_height }}" @endif />
                @endif
            </item>
        @endforeach
    </channel>
</rss>
