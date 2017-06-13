<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Token;

class TokenGenerator extends Controller
{
    public function newToken(Request $request){
        $token = new Token;
        $dt = new \DateTime;
        $dt->add(new \DateInterval('P1Y'));

        $tokenString = $this->generateRandomString(20);

        $token->user_id = $request->user_id;
        $token->token = $tokenString;
        $token->expiry = $dt->format('y-m-d H:i:s');

        $token->save();

        return response(array("success"=>true, "errorCode"=>0, "token"=>$tokenString));
    }

     public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
