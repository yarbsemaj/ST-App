<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;
use App\User;
use Mockery\Exception;
use App\STCookies;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

class ValidateLogin
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
            $stCookie->get($request->email,$request->password);
            $request->location = $stCookie->getLocation();
            $request->cookies=$stCookie->getCookies();
            $request->xssToken=$stCookie->getXSS();
            if($stCookie->isValid()) {
                return $next($request);
            } else return response(array("success"=>false));
    }
}
