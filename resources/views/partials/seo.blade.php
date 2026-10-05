@php
    $seoTitle = $title ?: config('seo.default_title');
    $seoDescription = $description ?: config('seo.default_description');
    $seoCanonical = $canonical ?: \App\Support\Seo\Seo::canonical();
    $seoImage = $image ?: \App\Support\Seo\Seo::defaultImage();
    $seoRobots = $robots ?: config('seo.robots');
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">

<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:image" content="{{ $seoImage['url'] }}">
<meta property="og:image:width" content="{{ $seoImage['width'] }}">
<meta property="og:image:height" content="{{ $seoImage['height'] }}">
<meta property="og:site_name" content="{{ config('seo.site_name') }}">
<meta property="og:locale" content="{{ config('seo.locale') }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage['url'] }}">

<link rel="alternate" type="application/rss+xml" title="{{ config('seo.site_name') }} — Blog" href="{{ route('blog.feed') }}">

@if (config('seo.google_site_verification'))
    <meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">
@endif
@if (config('seo.bing_site_verification'))
    <meta name="msvalidate.01" content="{{ config('seo.bing_site_verification') }}">
@endif

@if (! empty($graph))
    <script type="application/ld+json">{!! \App\Support\Seo\Seo::graphJson($graph) !!}</script>
@endif

@if (config('seo.ga4_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('seo.ga4_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @js(config('seo.ga4_id')));
    </script>
@endif
