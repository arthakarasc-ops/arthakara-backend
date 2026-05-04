<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillingAddressCreateRequest;
use App\Models\BillingAddress;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class BillingAddressController extends Controller
{
    public function createBillingAddress(BillingAddressCreateRequest $request): JsonResponse {
        $user = Auth::user();

        $decayMinutes = 1;
        $maxAttemps = 3;
        $key = 'create-billing-address: ' . $user->email;

        if (RateLimiter::tooManyAttempts($key, $maxAttemps)) {
            $second = RateLimiter::availableIn($key);

            throw new HttpResponseException(response()->json([
                'error' => 'Too many attemps. Please try again after ' . $second . ' second'
            ]));
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $data = $request->validated();

        if (BillingAddress::where('is_default', $data['is_default'])->count() == 1) {
            throw new HttpResponseException(response()->json([
                'error' => 'Just allowed one default address.'
            ]));
        }

        $billingAddress = new BillingAddress($data);
        $billingAddress->save();

        return response()->json([
            'message' => 'Billing address created successfully.',
            'data' => [
                'id' => $billingAddress->id,
                'first_name' => $billingAddress->first_name,
                'last_name' => $billingAddress->last_name,
                'address' => $billingAddress->address,
                'appartment_suite' => $billingAddress->appartment_suite,
                'city' => $billingAddress->city,
                'province' => $billingAddress->province,
                'postal_code' => $billingAddress->postal_code,
                'country' => $billingAddress->country
            ],
            'isSuccess' => true
        ])->setStatusCode(200);
    }

}

