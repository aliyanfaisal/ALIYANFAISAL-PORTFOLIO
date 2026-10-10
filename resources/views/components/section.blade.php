@props(['title'])
<section {{ $attributes->class('mt-10') }}>
    <h2 class="border-b border-accent pb-1 text-sm font-bold uppercase tracking-wider text-accent">{{ $title }}</h2>
    <div class="mt-4">
        {{ $slot }}
    </div>
</section>
