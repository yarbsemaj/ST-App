<?php

namespace App\Http\Middleware;

use Closure;

class GetCookieString
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
        $string = "Cookie: ";
        foreach ($request->cookies as $index => $cookie) {
            $string = $string . $index . "=" . $cookie . ";";
        }
        $request->cookies = substr($string, 0, -1);
        return $next($request);
    }
}
