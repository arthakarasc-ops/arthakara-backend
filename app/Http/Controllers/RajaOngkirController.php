<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Exception;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    protected $rajaOngkirService;

    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
    }

    public function getProvinces()
    {
        try {
            $provinces = $this->rajaOngkirService->getProvinces();
            return response()->json([
                'data' => $provinces,
                'isSuccess' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    public function getCities(Request $request)
    {
        try {
            $provinceId = $request->query('province');
            $cities = $this->rajaOngkirService->getCities($provinceId);
            return response()->json([
                'data' => $cities,
                'isSuccess' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    public function getCost(Request $request)
    {
        $request->validate([
            'destination' => 'required|numeric',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required|string|in:jne,tiki,pos'
        ]);

        try {
            $costs = $this->rajaOngkirService->getCost(
                $request->destination,
                $request->weight,
                $request->courier
            );

            return response()->json([
                'data' => $costs,
                'isSuccess' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }
}
