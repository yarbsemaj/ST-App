<?php

namespace App\Http\Controllers;

use App\Http\Constants;
use Illuminate\Http\Request;

class RequestTest extends Controller
{
    public $cookies = array();
    private $valid = false;

    public function get(Request $request)
    {
        $username = $request->email;
        $password = $request->password;
        $url = Constants::$url.'/';
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => "User-Agent: Hi, its james, pls dont block me\r\n"
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        print $result;
    }

    public function post(Request $request){
        return $request->all();
    }
}
