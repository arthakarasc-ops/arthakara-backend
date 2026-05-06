<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Status;
use App\Models\Type;
use Illuminate\Http\Request;

class OtherWebController extends Controller
{
    public function counter() {
        $statusCount = Status::count();
        $typeCount = Type::count();
        $colorCount = Color::count();
        $scentCount = \App\Models\Scent::count();

        return view('components.other.other', compact('statusCount', 'typeCount', 'colorCount', 'scentCount'));
    }
}

