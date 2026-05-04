<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Status;
use App\Models\Type;
use App\Models\Variant;
use Illuminate\Http\Request;

class OtherWebController extends Controller
{
    public function counter() {
        $statusCount = Status::count();
        $typeCount = Type::count();
        $colorCount = Color::count();
        $variantCount = Variant::count();

        return view('components.other.other', compact('statusCount', 'typeCount', 'colorCount', 'variantCount'));
    }
}

