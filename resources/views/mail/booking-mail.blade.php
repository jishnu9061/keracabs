<x-mail::message>
# New Booking Request

**Name:** {{ $name }}

**Phone Number:** {{ $number }}

**Email:** {{ $email }}

**Car:** {{ $car }}

@if($message)
**Message:** {{ $message }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
