<?php

namespace App\Http\Middleware;

use app\Http\Constants;
use app\Http\Utils;
use Closure;

class GetAuthToken
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
        $url=Constants::$url."/timesheets?#tosubmit";
        $result = Utils::get($url,$request->cookies);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($result);
        $finder = new \DomXPath($doc);
        $scripts = $finder->query("//script");
        $regxp = "/var \\\$auth_token = '(.*)';/";

        $return = array();
        foreach ($scripts as $s) {
            if (preg_match($regxp, $s->nodeValue, $matches)) {
                $return[] = urldecode($matches[1]);
            }
        }
        $request->authToken= $return;
        return $next($request);
    }
}
