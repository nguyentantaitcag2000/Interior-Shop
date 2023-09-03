<?php

namespace App\Http\Controllers;

use App\Models\ship_method;
use Illuminate\Http\Request;

class ShipMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ship_method::all();
        return $data;
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ship_method $ship_method)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ship_method $ship_method)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ship_method $ship_method)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ship_method $ship_method)
    {
        //
    }
}
