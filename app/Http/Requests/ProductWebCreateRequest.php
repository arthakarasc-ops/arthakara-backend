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
            'collection_id' => ['required', 'exists:collections,id'],
            'type_ids' => ['required', 'array', 'min:1'],
            'type_ids.*' => ['exists:types,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['required'],
            'image'   => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'image_3' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'color_ids' => ['required', 'array', 'min:1'],
            'color_ids.*' => ['exists:colors,id'],
            'scent_ids' => ['nullable', 'array'],
            'scent_ids.*' => ['exists:scents,id'],
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ]));
    }
}
