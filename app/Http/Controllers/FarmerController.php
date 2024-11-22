<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $farmers = Farmer::with(['user.personalInfo'])->get();
        return $this->success(['farmers' => $farmers]);
    }

    public function show(Farmer $farmer)
    {
        $farmer->load(['user.personalInfo', 'farms']);
        return $this->success(['farmer' => $farmer]);
    }

    public function approve(Farmer $farmer)
    {
        $farmer->IsApproved = true;
        $farmer->save();

        return $this->success([
            'farmer' => $farmer
        ], 'Farmer approved successfully');
    }

    public function reject(Farmer $farmer)
    {
        $farmer->IsApproved = false;
        $farmer->save();

        return $this->success([
            'farmer' => $farmer
        ], 'Farmer disapproved successfully');
    }
}
