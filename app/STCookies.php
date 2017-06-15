<?php
/**
 * Created by PhpStorm.
 * User: yarbs
 * Date: 03/06/2017
 * Time: 08:37 PM
 */

namespace app;


class STCookies
{
    public $cookies = array();
    private $valid = false;
    public function get($username,$password)
    {
        $url = 'https://www.studenttemp.co.uk/login';
        $options = array(
            'http' => array(
                'method'  => 'GET'
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($result);
        $xp = new \DOMXpath($doc);
        $inputs = $xp->query('//input[@name="authenticity_token"]');
        $input = $inputs->item(0);
        $at = $input->getAttribute('value');
        foreach ($http_response_header as $hdr) {
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
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                    . $this->getCookies()."\r\n"
            ,
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $url = 'https://www.studenttemp.co.uk/user_sessions';
        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
        foreach ($http_response_header as $hdr) {
            if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
                parse_str($matches[1], $tmp);
                $this->cookies += $tmp;
            }
        }
        if(count($this->cookies) ==2){
            $this->valid=true;
        }
    }
    public function isValid(){
        return $this->valid;
    }

    function getCookies(){
        $string = "Cookie: ";
        foreach ($this->cookies as $index => $cookie)
        {
            $string= $string.$index."=".$cookie.";";
        }
        return substr($string,0,-1);
    }

}