@component('mail::message')
# New Contact Form Submission

**Name:** {{ $name }}
**Email:** {{ $email }}
**Phone:** {{ $phone }}
**Subject:** {{ $subject }}

Message:
{{ $message }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
