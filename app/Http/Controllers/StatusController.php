<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function getStatuses(): JsonResponse {
        $statuses = Status::all();

        return response()->json([
            'data' => StatusResource::collection($statuses)
        ]);
    }
}

