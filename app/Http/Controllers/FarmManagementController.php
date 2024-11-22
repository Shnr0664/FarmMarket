<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Traits\ApiResponse;

class FarmManagementController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $farms = Farm::with(['farmer.user.personalInfo'])->get();
        return $this->success(['farms' => $farms]);
    }

    public function show(Farm $farm)
    {
        $farm->load(['farmer.user.personalInfo', 'products']);
        return $this->success(['farm' => $farm]);
    }

}
