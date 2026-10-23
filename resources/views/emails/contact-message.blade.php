<x-email.layout preheader="New message from {{ $contactMessage->name }}">
    <h2 style="margin: 0 0 4px; font-size: 22px;">New contact message</h2>
    <p style="color: #71717a; margin: 0 0 16px;">Submitted via aliyanfaisal.com</p>

    <table style="border-collapse: collapse; margin: 0 0 16px;">
        <tr>
            <td style="padding: 4px 12px 4px 0; color: #71717a;">Name</td>
            <td style="padding: 4px 0; font-weight: bold;">{{ $contactMessage->name }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 12px 4px 0; color: #71717a;">Email</td>
            <td style="padding: 4px 0; font-weight: bold;">{{ $contactMessage->email }}</td>
        </tr>
        @if ($contactMessage->subject)
            <tr>
                <td style="padding: 4px 12px 4px 0; color: #71717a;">Subject</td>
                <td style="padding: 4px 0; font-weight: bold;">{{ $contactMessage->subject }}</td>
            </tr>
        @endif
    </table>

    <p style="margin: 0; white-space: pre-line; border-left: 3px solid #6366f1; padding-left: 12px;">{{ $contactMessage->message }}</p>
</x-email.layout>
