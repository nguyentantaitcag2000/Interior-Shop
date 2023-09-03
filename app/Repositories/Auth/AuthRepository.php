<?php 
namespace App\Repositories\Auth;

use Illuminate\Config\Repository;

class AuthRepository extends Repository implements AuthRepositoryInterface {
    public function CheckLogin()
    {
        if(session()->has('id_user') == false)
        {
            die(json_encode([
                'status' => 401,
                'message' => 'Required Login'
            ]));
        }
    }
}
?>