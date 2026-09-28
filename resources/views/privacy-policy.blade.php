<x-layouts.app title="Privacy Policy — Aliyan Faisal">
    @php
        $settings = \App\Models\Setting::current();
    @endphp

    <section class="mx-auto max-w-3xl px-6 py-16">
        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Legal</p>
        <h1 class="mt-3 text-4xl font-bold text-zinc-900 dark:text-white">Privacy Policy</h1>
        <p class="mt-4 text-sm text-zinc-400">Last updated: {{ now()->format('F j, Y') }}</p>

        <div class="prose prose-zinc mt-10 max-w-none dark:prose-invert prose-headings:font-bold prose-a:text-indigo-500 dark:prose-a:text-indigo-400">
            <p>
                This Privacy Policy describes how {{ $settings->site_name }} ("we", "us", or "this site")
                collects, uses, and discloses information when you visit {{ $settings->site_name }} (the "Site"),
                including our blog and contact pages.
            </p>

            <h2>Information We Collect</h2>
            <h3>Information You Provide</h3>
            <p>
                When you use our contact form, subscribe to our newsletter, or post a comment, we collect the
                information you submit, such as your name, email address, and message content.
            </p>

            <h3>Information Collected Automatically</h3>
            <p>
                Like most websites, we automatically collect certain information when you visit the Site,
                including your IP address, browser type, device information, pages visited, and the date and
                time of your visit. This is typically collected through cookies and similar tracking technologies.
            </p>

            <h2>Cookies</h2>
            <p>
                Cookies are small text files stored on your device. We use cookies to keep the Site functioning
                properly and to understand how visitors use the Site. You can disable cookies through your
                browser settings, though some features of the Site may not work correctly without them.
            </p>

            <h2>Google AdSense and Advertising</h2>
            <p>
                This Site may display advertisements served by Google AdSense. Google, as a third-party vendor,
                uses cookies (such as the DoubleClick cookie) to serve ads based on your prior visits to this
                Site and other websites. Google's use of advertising cookies enables it and its partners to
                serve ads based on your visit to this Site and/or other sites on the internet.
            </p>
            <p>
                You may opt out of personalized advertising by visiting
                <a href="https://adssettings.google.com" target="_blank" rel="noopener">Google Ads Settings</a>.
                Alternatively, you can opt out of some third-party vendors' use of cookies for personalized
                advertising by visiting
                <a href="https://www.aboutads.info" target="_blank" rel="noopener">www.aboutads.info</a>.
            </p>
            <p>
                Third-party vendors, including Google, use cookies to serve ads based on a user's prior visits
                to this website or other websites. Google's use of advertising cookies enables it and its
                partners to serve ads to users based on their visit to this Site and/or other sites on the
                Internet. Users may opt out of the use of the DoubleClick cookie for interest-based advertising
                by visiting Google's Ads Settings.
            </p>

            <h2>Google Analytics</h2>
            <p>
                We may use Google Analytics to understand how visitors interact with the Site. Google Analytics
                uses cookies to collect information such as how often users visit the Site, what pages they
                visit, and what other sites they used prior to coming to this Site. We use the information we
                get from Google Analytics to improve the Site. Google Analytics collects only the IP address
                assigned to you on the date you visit the Site, rather than your name or other identifying
                information.
            </p>

            <h2>Third-Party Links</h2>
            <p>
                The Site may contain links to third-party websites, including project demos, social profiles,
                and freelance marketplace profiles. We are not responsible for the privacy practices or content
                of those third-party sites.
            </p>

            <h2>How We Use Your Information</h2>
            <ul>
                <li>To respond to inquiries submitted through the contact form</li>
                <li>To send newsletter updates to subscribers who opt in</li>
                <li>To display and moderate blog comments</li>
                <li>To analyze Site traffic and improve content and functionality</li>
                <li>To serve relevant advertising through Google AdSense</li>
            </ul>

            <h2>Data Retention</h2>
            <p>
                We retain contact form submissions, newsletter subscriptions, and comments for as long as
                necessary to fulfil the purposes described in this policy, unless a longer retention period is
                required by law.
            </p>

            <h2>Your Rights</h2>
            <p>
                Depending on your location, you may have the right to access, correct, or delete personal
                information we hold about you, or to opt out of certain data collection. To exercise these
                rights, contact us using the details below.
            </p>

            <h2>Children's Privacy</h2>
            <p>
                This Site is not directed at children under the age of 13, and we do not knowingly collect
                personal information from children.
            </p>

            <h2>Changes to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. Any changes will be posted on this page
                with an updated "Last updated" date.
            </p>

            <h2>Contact Us</h2>
            <p>
                If you have questions about this Privacy Policy, please contact us at
                <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a>
                or via our <a href="{{ route('contact.create') }}">contact page</a>.
            </p>
        </div>
    </section>
</x-layouts.app>
