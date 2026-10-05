<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

class BlogPostContentEnhancer
{
    /**
     * Adds ids to h2/h3 headings (anchor links and "jump to" sitelinks), opens external links safely
     * (rel="noopener", still followable), and gives body images lazy loading and alt text.
     *
     * @return array{html: string, headings: array<int, array{id: string, text: string, level: int}>}
     */
    public function enhance(string $html, string $fallbackAlt): array
    {
        if (trim($html) === '') {
            return ['html' => $html, 'headings' => []];
        }

        $previousLibxmlSetting = libxml_use_internal_errors(true);

        $document = new DOMDocument;
        $document->loadHTML(
            '<?xml encoding="utf-8"?><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previousLibxmlSetting);

        $root = $document->getElementsByTagName('div')->item(0);
        $headings = [];
        $usedIds = [];

        foreach ($root->getElementsByTagName('*') as $element) {
            match ($element->tagName) {
                'h2', 'h3' => $this->addHeadingId($element, $headings, $usedIds),
                'a' => $this->secureExternalLink($element),
                'img' => $this->optimizeImage($element, $fallbackAlt),
                default => null,
            };
        }

        $enhanced = '';
        foreach ($root->childNodes as $child) {
            $enhanced .= $document->saveHTML($child);
        }

        return ['html' => $enhanced, 'headings' => $headings];
    }

    /**
     * @param  array<int, array{id: string, text: string, level: int}>  $headings
     * @param  array<string, true>  $usedIds
     */
    private function addHeadingId(DOMElement $heading, array &$headings, array &$usedIds): void
    {
        $text = trim(preg_replace('/\s+/u', ' ', $heading->textContent));
        $base = $heading->getAttribute('id') ?: (Str::slug($text) ?: 'section');
        $id = $base;

        for ($suffix = 2; isset($usedIds[$id]); $suffix++) {
            $id = "{$base}-{$suffix}";
        }

        $usedIds[$id] = true;
        $heading->setAttribute('id', $id);
        $headings[] = ['id' => $id, 'text' => $text, 'level' => (int) substr($heading->tagName, 1)];
    }

    private function secureExternalLink(DOMElement $link): void
    {
        $host = parse_url($link->getAttribute('href'), PHP_URL_HOST);

        if ($host === null || $host === false || strcasecmp($host, (string) parse_url(config('app.url'), PHP_URL_HOST)) === 0) {
            return;
        }

        $link->setAttribute('target', '_blank');
        $link->setAttribute('rel', 'noopener');
    }

    private function optimizeImage(DOMElement $image, string $fallbackAlt): void
    {
        $image->setAttribute('loading', 'lazy');
        $image->setAttribute('decoding', 'async');

        if (trim($image->getAttribute('alt')) === '') {
            $image->setAttribute('alt', $fallbackAlt);
        }
    }
}
