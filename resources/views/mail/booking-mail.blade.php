<x-mail::message>
# New Booking Request

**Name:** {{ $name }}

**Phone Number:** {{ $number }}

**Email:** {{ $email }}

**Car:** {{ $car }}

@if($message)
**Message:** {{ $message }}
@endif

**Start Date:** {{ $start_date }}

**End Date:** {{ $end_date }}

**Start Time:** {{ $start_time }}

**End Time:** {{ $end_time }}

@if($message)
**Vehicle Type:** {{ $vehicle_type }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
