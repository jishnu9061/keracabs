<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManagerUpdateRequest extends FormRequest
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
        $manager = $this->route('manager');
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:managers,user_name,' .$manager->id,
            'password' => 'nullable|string|min:8|confirmed',
            'contact' => 'required|string|max:15',
        ];
    }
}
