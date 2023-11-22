<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\user_type;
use Exception;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = user_type::get();
        return $data;
    }
    public function changeLevel(Request $request)
    {
        try {
            //code...
            $user = User::where('ID_User', $request->input('idUser'))->first();
            $user->ID_UT = $request->input('idType');
            $user->save();
    
            return json_encode([
                'status' => 200,
                'message' => 'Thay đổi thành công !',
                'object' => User::with('User_UStatus')->with('user_type')->where('ID_User', $request->input('idUser'))->first()
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 400,
                'message' => 'Thay đổi thất bại !',
                'detail' => $e->getMessage()
            ]);
            
        }
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
    public function show(user_type $user_type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user_type $user_type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, user_type $user_type)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user_type $user_type)
    {
        //
    }
}
