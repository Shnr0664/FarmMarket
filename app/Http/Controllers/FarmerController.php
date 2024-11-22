<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    use ApiResponse;

    public function approve(Farmer $farmer)
    {
        $farmer->IsApproved = true;
        $farmer->save();

        return $this->success([
            'farmer' => $farmer
        ], 'Farmer approved successfully');
    }
}
