<?php

namespace App\Http\Controllers;

use App\Models\method_of_payment;
use Illuminate\Http\Request;

class MethodOfPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = method_of_payment::all();
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
    public function show(method_of_payment $method_of_payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(method_of_payment $method_of_payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, method_of_payment $method_of_payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(method_of_payment $method_of_payment)
    {
        //
    }
}
