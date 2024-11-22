<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        if ($request->user()->isAdmin()) {
            $farmers = Farmer::with(['user.personalInfo'])->get();
            return $this->success(['farmers' => $farmers]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }
    }

    public function show(Farmer $farmer)
    {
        $farmer->load(['user.personalInfo', 'farms']);
        return $this->success(['farmer' => $farmer]);
    }

    public function approve(Farmer $farmer, Request $request)
    {
        if ($request->user()->isAdmin()) {
            $farmer->IsApproved = true;
            $farmer->save();

            return $this->success([
                'farmer' => $farmer
            ], 'Farmer approved successfully');
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }
    }

    public function reject(Farmer $farmer, Request $request)
    {
        if ($request->user()->isAdmin()) {
            $farmer->IsApproved = false;
            $farmer->save();

            return $this->success([
                'farmer' => $farmer
            ], 'Farmer disapproved successfully');
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }
    }
}
