<x-mail::message>
# Registration Successful

Dear {{ $registrationData['name'] }},

Thank you for registering. Here are your details:

- **Name:** {{ $registrationData['name'] }}
- **Phone Number:** {{ $registrationData['number'] }}
- **Vehicle Type:** {{ $registrationData['vehicle_type'] }}
- **Seating Capacity:** {{ $registrationData['seating_capacity'] }}
- **Vehicle Number:** {{ $registrationData['vehicle_number'] }}
- **Parking Location:** {{ $registrationData['parking_location'] }}
- **District:** {{ $registrationData['district'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
