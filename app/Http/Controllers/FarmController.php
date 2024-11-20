<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFarmRequest;
use App\Http\Requests\UpdateFarmRequest;
use App\Models\Farm;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farms = auth()->user()->farmer->farms()->with('products')->get();
        return $this->success(['farms' => $farms]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFarmRequest $request)
    {
        $validated = $request->validated();
        $validated['farmer_id'] = auth()->user()->farmer->id;
        
        $farm = Farm::create($validated);
        
        return $this->success(
            ['farm' => $farm->load('products')],
            'Farm created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Farm $farm)
    {
        if ($farm->farmer_id !== auth()->user()->farmer->id) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success(['farm' => $farm->load('products')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farm $farm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFarmRequest $request, Farm $farm)
    {
        $farm->update($request->validated());
        
        return $this->success(
            ['farm' => $farm->fresh()->load('products')],
            'Farm updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farm $farm)
    {
        if ($farm->farmer_id !== auth()->user()->farmer->id) {
            return $this->error('Unauthorized', 403);
        }

        $farm->delete();
        return $this->success(null, 'Farm deleted successfully');
    }
}
