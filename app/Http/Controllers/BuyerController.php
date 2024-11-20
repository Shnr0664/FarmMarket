<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuyerRequest;
use App\Http\Requests\UpdateBuyerRequest;
use App\Models\buyer;

class BuyerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreBuyerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(buyer $buyer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(buyer $buyer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuyerRequest $request, buyer $buyer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(buyer $buyer)
    {
        //
    }
}
