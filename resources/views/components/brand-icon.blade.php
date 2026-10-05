@props(['name', 'class' => 'size-4'])
@php
    $icons = [
        'fiverr' => '<text x="12" y="17" text-anchor="middle" font-size="15" font-weight="700" fill="currentColor" font-family="Arial, sans-serif">fi</text>',
        'upwork' => '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none"/><text x="12" y="16" text-anchor="middle" font-size="9" font-weight="700" fill="currentColor" font-family="Arial, sans-serif">Up</text>',
        'linkedin' => '<path fill="currentColor" d="M6.94 8.5H4.56V19h2.38V8.5zM5.75 4.75a1.38 1.38 0 100 2.76 1.38 1.38 0 000-2.76zM19.44 19h-2.37v-5.4c0-1.29-.46-2.16-1.6-2.16-.88 0-1.4.59-1.63 1.16-.08.2-.1.49-.1.77V19H11.4s.03-9.6 0-10.5h2.37v1.49a2.35 2.35 0 012.13-1.18c1.56 0 2.73 1.02 2.73 3.2V19z"/>',
        'github' => '<path fill="currentColor" d="M12 .5C5.65.5.5 5.65.5 12c0 5.09 3.29 9.4 7.86 10.93.57.1.78-.25.78-.55 0-.27-.01-1.16-.02-2.11-3.2.7-3.87-1.36-3.87-1.36-.53-1.34-1.29-1.7-1.29-1.7-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.56-.29-5.26-1.28-5.26-5.7 0-1.26.45-2.29 1.19-3.09-.12-.29-.52-1.47.11-3.06 0 0 .97-.31 3.18 1.18a11 11 0 015.8 0c2.2-1.49 3.17-1.18 3.17-1.18.64 1.59.24 2.77.12 3.06.74.8 1.18 1.83 1.18 3.09 0 4.43-2.7 5.4-5.28 5.69.42.36.78 1.08.78 2.18 0 1.57-.02 2.84-.02 3.23 0 .3.2.66.79.55A10.52 10.52 0 0023.5 12c0-6.35-5.15-11.5-11.5-11.5Z"/>',
    ];
@endphp
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="{{ $class }}" aria-hidden="true" {{ $attributes }}>
    {!! $icons[$name] ?? '' !!}
</svg>
