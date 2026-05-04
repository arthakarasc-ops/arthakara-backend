<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductCreateRequest extends FormRequest
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
            'type_id' => ['required', 'exists:types,id'],
            'slug' => ['required', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required'],
            'image' => ['required', 'max:255'],
            'color_ids' => ['sometimes', 'array'],
            'color_ids.*' => ['exists:colors,id'],
            'scent_ids' => ['sometimes', 'array'],
            'scent_ids.*' => ['exists:scents,id']
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ]));
    }
}
