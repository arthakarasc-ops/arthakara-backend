<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductVariantWebCreateRequest extends FormRequest
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
            'color_id' => ['nullable', 'exists:colors,id'],
            'fabric_id' => ['nullable', 'exists:fabrics,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'], 
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ]));
    }
}
