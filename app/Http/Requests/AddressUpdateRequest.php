<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_default' => ['required'],
            'first_name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'address' => ['required', 'max:200'],
            'appartment_suite' => ['nullable', 'max:200'],
            'city' => ['required', 'max:100'],
            'province' => ['required', 'max:100'],
            'postal_code' => ['required', 'max:100'],
            'country' => ['required', 'max:100'],
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ]));
    }
}
