<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderCreateRequest extends FormRequest
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
            'shipping_address.first_name' => ['required', 'string', 'max:100'],
            'shipping_address.last_name' => ['required', 'string', 'max:100'],
            'shipping_address.address' => ['required', 'string', 'max:200'],
            'shipping_address.appartment_suite' => ['nullable', 'string', 'max:200'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.province' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:100'],
            'shipping_address.country' => ['required', 'string', 'max:100'],
            'shipping_address.phone_number' => ['required', 'string', 'max:20'],
            'billing_address.first_name' => ['required', 'string', 'max:100'],
            'billing_address.last_name' => ['required', 'string', 'max:100'],
            'billing_address.address' => ['required', 'string', 'max:200'],
            'billing_address.appartment_suite' => ['nullable', 'string', 'max:200'],
            'billing_address.city' => ['required', 'string', 'max:100'],
            'billing_address.province' => ['required', 'string', 'max:100'],
            'billing_address.postal_code' => ['required', 'string', 'max:100'],
            'billing_address.country' => ['required', 'string', 'max:100'],
            'billing_address.phone_number' => ['required', 'string', 'max:20'],
            'shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.scents' => ['required', 'array', 'size:2'],
            'items.*.scents.*' => ['required', 'integer', 'exists:scents,id']
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ]));
    }
}
