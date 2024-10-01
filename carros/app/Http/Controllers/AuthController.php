<?php declare(strict_types=1); 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Claims\JwtId;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
     $credenciais = $request->only('email','password');//[]
         //autenticaçao (email e senha)
         $token = auth('api')->attempt($credenciais);
         if($token){//Usuario autenticado com sucesso
            return response()->json(['token'=> $token], 200);
         }else{// erro de usuário ou senha 
        return response()->json(['erro'=>'Usuário ou senha inválido!'], 403);
         }

        //retornar um jwt
    }

    public function logout()
    {
       auth('api')->logout();
       return response()->json(['msg'=>'logout foi realizado com sucesso!']);
    }

    public function refresh()
    {
       $token = JWTAuth::refresh();
       return response()->json(['token'=> $token]);
    }

    public function me()
    {
        return response()->json(auth()->user());
        
    }
}
