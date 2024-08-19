<x-mail::message>
# Registration Successful

Dear Admin,

A new user has successfully registered. Here are the details:

- **Name:** {{ $registrationData['name'] }}
- **Phone Number:** {{ $registrationData['number'] }}
- **WhatsApp Number:** {{ $registrationData['whatsapp_number'] }}
- **Vehicle Type:** {{ $registrationData['vehicle_type'] }}
- **Seating Capacity:** {{ $registrationData['seating_capacity'] }}
- **Vehicle Number:** {{ $registrationData['vehicle_number'] }}
- **Parking Location:** {{ $registrationData['parking_location'] }}
- **District:** {{ $registrationData['district'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
