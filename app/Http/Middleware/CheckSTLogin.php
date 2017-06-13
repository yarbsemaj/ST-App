<?php

namespace App\Http\Middleware;

use Closure;
use App\STCookies;


class CheckSTLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $stCookie = new STCookies();
        $stCookie->get($request->email, $request->password);
            if ($stCookie->isValid()) {
                $request->cookies = $stCookie->cookies;
                return $next($request);
            } else {
                return response(array("success" => false, "errorCode" => 1, "errorMessage" => "Your Student Temp username or password is invalid"));
            }

    }



}
