<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ID_UT != 2 (loại trừ admin ra)
        $data = User::with('User_UStatus')->with('user_type')->where('ID_UT','!=', 2 )->get();
        return $data;
    }
    public function getUserByUserType(string $idUserType)
    {
        $data = User::with('User_UStatus')->with('user_type')->where('ID_UT', $idUserType )->get();
        return $data;
    }
    public function blockUser(Request $request)
    {
        try{
            $user = User::where('ID_User', $request->input('idUser'))->first();
            $user->ID_UStatus = 2;
            $user->save();
        }
        catch(Exception $e)
        {
            return json_encode([
                'status' => 400,
                'message' => "Có lỗi",
                'detail' => $e->getMessage()
            ]);    
        }
        

        return json_encode([
            'status' => 200,
            'object' => User::with('User_UStatus')->with('user_type')->where('ID_User', $request->input('idUser'))->first()
        ]);
    }
    public function unBlockUser(Request $request)
    {
        try{
            $user = User::where('ID_User', $request->input('idUser'))->first();
            $user->ID_UStatus = 1;
            $user->save();
        }
        catch(Exception $e)
        {
            return json_encode([
                'status' => 400,
                'message' => "Có lỗi",
                'detail' => $e->getMessage()
            ]);    
        }
        return json_encode([
            'status' => 200,
            'object' => User::with('User_UStatus')->with('user_type')->where('ID_User', $request->input('idUser'))->first()
        ]);
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
        //Check existed email
        $isExisted = User::where('Email', request('email'))->exists();
        if($isExisted)
        {
            return json_encode([
                'status' => 400,
                'message' => 'Email existed'
            ]);
        }
        User::create([
            'Email' => request('email'),
            'password' => Hash::make(request('password')),
        ]);

        return json_encode([
            'status' => 200,
            'message' => 'Đăng kì thành công'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = User::where('ID_User', $request->input('idUser'))->first();

        return json_encode([
            'status' => 200,
            'object' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = User::where('ID_User', $request->input('idUser'))->first();
        try {
            $user->Email =  $request->input('user')['Email'];
            $user->Name_User = $request->input('user')['Name_User'];
            $user->Phone = $request->input('user')['Phone'];
            $user->Address =  $request->input('user')['Address'];
            $user->save();
            return json_encode([
                'status' => 200,
                'message' => 'Cập nhật thành công !',
                'object' => $user
            ]);
            //code...
        } catch (\Throwable $th) {
            return json_encode([
                'status' => 400,
                'message' => 'Cập nhật thất bại !',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
