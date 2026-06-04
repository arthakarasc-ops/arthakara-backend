<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:100',
            'password' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'nickname' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'password.required' => 'Password harus diisi.',
            'phone_number.required' => 'Nomor telepon harus diisi.',
            'phone_number.max' => 'Nomor telepon maksimal 20 karakter.',
            'nickname.required' => 'Nama panggilan harus diisi.',
            'nickname.max' => 'Nama panggilan maksimal 100 karakter.',
        ];
    }
}
