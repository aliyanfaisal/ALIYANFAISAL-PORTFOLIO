<?php

namespace App\Support\Seo;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Seo
{
    public static function personId(): string
    {
        return url('/').'/#person';
    }

    public static function websiteId(): string
    {
        return url('/').'/#website';
    }

    /**
     * The current URL with only whitelisted query parameters (utm_*, gclid, q ... are dropped,
     * page=1 collapses to the bare URL) so paginated pages self-canonicalize.
     */
    public static function canonical(?Request $request = null): string
    {
        $request ??= request();

        $query = collect($request->query())
            ->only(config('seo.canonical_params'))
            ->reject(fn ($value, $key) => $key === 'page' && (int) $value <= 1)
            ->all();

        $url = $request->url();

        return $query === [] ? $url : $url.'?'.http_build_query($query);
    }

    public static function absoluteUrl(string $path): string
    {
        return Str::startsWith($path, ['http://', 'https://']) ? $path : asset($path);
    }

    /**
     * @return array{url: string, width: int, height: int}
     */
    public static function defaultImage(): array
    {
        return [
            'url' => asset(config('seo.default_og_image')),
            'width' => config('seo.og_image_width'),
            'height' => config('seo.og_image_height'),
        ];
    }

    /**
     * One JSON-LD <script> payload; the only place JSON-LD is encoded.
     *
     * @param  array<int, array<string, mixed>>  $nodes
     */
    public static function graphJson(array $nodes): string
    {
        return json_encode(
            ['@context' => 'https://schema.org', '@graph' => array_values($nodes)],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG,
        );
    }
}
