<?php

namespace App\Http\Requests;


use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class SendRegistartionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:255',
            'seating_capacity' => 'required|string|max:255',
            'vehicle_number' => 'required|string|max:255',
            'parking_location' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'district' => 'required|string|max:255',
            'vehicle_photo' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            'driver_image' => 'required|file|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
