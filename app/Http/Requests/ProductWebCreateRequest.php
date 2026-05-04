<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductWebCreateRequest extends FormRequest
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
            'name' => ['required', 'max:100'],
            'collection_id' => ['required', 'exists:collections,id'], // Optional: Add existence check for better validation
            'type_id' => ['required', 'exists:types,id'], // Optional: Add existence check
            'price' => ['required', 'numeric', 'min:0'], // Optional: Add numeric/min for price
            'description' => ['required'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'], // Validate as image file, max 2MB
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ]));
    }
}
