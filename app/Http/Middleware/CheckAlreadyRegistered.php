<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class CheckAlreadyRegistered
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
       $user = User::updateOrCreate(
            ['username' => $request->email],
            ['password'=>$request->password, 'cookies'=>json_encode($request->cookies)]
        );
       $request -> user_id = $user->id;
       return $next($request);
    }
}
