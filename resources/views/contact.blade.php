<x-layouts.app title="Contact" description="Contact Aliyan Faisal about research, supervision and graduate study.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Contact</h1>
    <p class="mt-3">I am happy to hear from prospective supervisors and research groups, and to answer questions about any project on this site.</p>

    <dl class="mt-6 space-y-3">
        <div><dt class="font-semibold">Email</dt><dd><a href="mailto:{{ $person['email'] }}">{{ $person['email'] }}</a></dd></div>
        <div><dt class="font-semibold">GitHub</dt><dd><a href="{{ $person['github'] }}" rel="noopener">{{ $person['github'] }}</a></dd></div>
        <div><dt class="font-semibold">LinkedIn</dt><dd><a href="{{ $person['linkedin'] }}" rel="noopener">{{ $person['linkedin'] }}</a></dd></div>
        <div><dt class="font-semibold">Location</dt><dd>{{ $person['location'] }}</dd></div>
    </dl>
</x-layouts.app>
