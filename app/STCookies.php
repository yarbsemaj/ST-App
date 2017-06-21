<?php
/**
 * Created by PhpStorm.
 * User: yarbs
 * Date: 03/06/2017
 * Time: 08:37 PM
 */

namespace app;

use App\Http\Utils;
use App\Http\Constants;

class STCookies
{
    private $cookies = array();
    private $location = array();
    public function get($username,$password)
    {
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML(Utils::get(Constants::$url.'/login',null,$header));
        $xp = new \DOMXpath($doc);
        $inputs = $xp->query('//input[@name="authenticity_token"]');
        $input = $inputs->item(0);
        $at = $input->getAttribute('value');
        foreach ($header as $hdr) {
            if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
                parse_str($matches[1], $tmp);
                $this->cookies += $tmp;
            }
        }
        $data = array('user_session[email]' => $username,
            'user_session[password]' => $password,
            'authenticity_token' => $at,
            'inviscap'=>'true',
            'commit'=>'Login',
            'utf8'=>'✓'
            );
        $url = Constants::$url.'/user_sessions';
        Utils::post($url,$data,Utils::getCookies($this->cookies),$header);
        foreach ($header as $hdr) {
            if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
                parse_str($matches[1], $tmp);
                $this->cookies += $tmp;
            }elseif (preg_match('/^Location:\s*([^"]+)/', $hdr, $matches)) {
                $this->location[] = $matches[1];
            }
        }
    }
    public function isValid(){
        return count($this->cookies) ==2;
    }

    public function getType(){
        return explode('/',$this->location[0])[3];
    }

    public function getCookies(){
        return $this->cookies;
    }

}