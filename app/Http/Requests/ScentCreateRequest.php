<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ScentCreateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:scents,name'],
            'extra_price' => ['nullable', 'integer', 'min:0']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wangi harus diisi',
            'name.unique' => 'Nama wangi sudah ada',
            'extra_price.integer' => 'Harga tambahan harus berupa angka',
            'extra_price.min' => 'Harga tambahan tidak boleh negatif'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ]));
    }
}
