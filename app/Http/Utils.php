<?php
/**
 * Created by PhpStorm.
 * User: yarbs
 * Date: 09/06/2017
 * Time: 06:50 PM
 */

namespace app\Http;

use App\Http\Constants;

class Utils
{
    static function getAllOptions($node)
    {
        $options = $node->getElementsByTagName('option');
        $optionInfo = array();
        foreach ($options as $option) {
            $value = $option->getAttribute('value');
            $text = $option->textContent;

            $optionInfo[] = array(
                'value' => $value,
                'text' => $text,
            );
        }
        return $optionInfo;
    }

    static function buildQuery($input, $numeric_prefix = '',
                               $arg_separator = '&', $enc_type = 2,
                               $keyvalue_separator = '=', $prefix = '')
    {
        if (is_array($input)) {
            $arr = array();
            foreach ($input as $key => $value) {
                $name = $prefix;
                if (strlen($prefix)) {
                    $name .= '[';
                    if (!is_numeric($key)) {
                        $name .= $key;
                    }
                    $name .= ']';
                } else {
                    if (is_numeric($key)) {
                        $name .= $numeric_prefix;
                    }
                    $name .= $key;
                }
                if ((is_array($value) || is_object($value)) && count($value)) {
                    $arr[] = buildQuery($value, $numeric_prefix,
                        $arg_separator, $enc_type,
                        $keyvalue_separator, $name);
                } else {
                    if ($enc_type === 2) {
                        $arr[] = rawurlencode($name)
                            . $keyvalue_separator
                            . rawurlencode($value);
                    } else {
                        $arr[] = urlencode($name)
                            . $keyvalue_separator
                            . urlencode($value);
                    }
                }
            }
            return implode($arg_separator, $arr);
        } else {
            if ($enc_type === 2) {
                return rawurlencode($input);
            } else {
                return urlencode($input);
            }
        }
    }

    static function getAllPayOptions($node)
    {
        $options = $node->getElementsByTagName('option');
        $optionInfo = array();
        foreach ($options as $option) {
            $value = $option->getAttribute('value');
            $payRate = $option->getAttribute('payrate');
            $rate = $option->getAttribute('rate');
            $text = $option->textContent;

            $optionInfo[] = array(
                'value' => $value,
                'text' => $text,
                'additionalInfo' => array(
                    'payRate' => preg_replace("/[^0-9,.]/", "", $payRate),
                    'rate' => preg_replace("/[^0-9,.]/", "", $rate)
                )
            );
        }
        return $optionInfo;
    }

    static function getSelectedOptionValue($node)
    {
        $nodeValue = $node->ownerDocument->saveHTML($node);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($nodeValue);
        $finder = new \DomXPath($doc);
        return $finder->query('//option[@selected="selected"]/@value');
    }

    static function getSelectedOptions($node)
    {
        $nodeValue = $node->ownerDocument->saveHTML($node);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($nodeValue);
        $finder = new \DomXPath($doc);
        $options = $finder->query('//option[@selected="selected"]');

        $optionInfo = array();
        foreach ($options as $option) {
            $value = $option->getAttribute('value');
            $text = $option->textContent;

            $optionInfo[] = array(
                'value' => $value,
                'text' => $text
            );
        }
        return $optionInfo;
    }

    static function getSelectedOptionDisplay($node)
    {
        $nodeValue = $node->ownerDocument->saveHTML($node);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($nodeValue);
        $finder = new \DomXPath($doc);
        return $finder->query('//option[@selected="selected"]');
    }

    static function getInputValue($node)
    {
        $nodeValue = $node->ownerDocument->saveHTML($node);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($nodeValue);
        $finder = new \DomXPath($doc);
        return $finder->query('//input');
    }

    static function response($data, $startTime = 0)
    {
        $respose = array(
            'success' => true,
            'size' => count($data),
            //'timestamp' => $startTime,
            'data' => $data
        );

        return response($respose);
    }

    static function getCookies($cookies)
    {
        $string = "Cookie: ";
        foreach ($cookies as $index => $cookie) {
            $string = $string . $index . "=" . $cookie . ";";
        }
        return substr($string, 0, -1);
    }

    static function post($url, $data, $cookies, &$header = null, $xssToken = null, $method = "POST")
    {
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                    . $cookies . "\r\n" . Constants::$urlHeader . "X-CSRF-Token: " . $xssToken . "\r\n",
                'method' => $method,
                'content' => self::buildQuery($data)
            )
        );

        $context = stream_context_create($options);
        $return = file_get_contents($url, false, $context);
        $header = $http_response_header;
        return ($return);
    }

    static function get($url, $cookies, &$header = null)
    {
        $options = array(
            'http' => array(
                'header' => $cookies . "\r\n" . Constants::$urlHeader,
                'method' => 'GET'
            )
        );

        $context = stream_context_create($options);
        $return = file_get_contents($url, false, $context);
        $header = $http_response_header;
        return ($return);
    }

    static function getFromAvailability($cookies, $startTime, $endTime, $type, $data = array())
    {
        $events = self::getAvailability($startTime, $endTime, $cookies)['events'];
        foreach ($events as $event) {
            if ($event['status'] == $type) {
                $booking = $event['booking'];

                $booking['timeStamp'] = strtotime($booking['date'] . " " . $booking['start']);

                $data[] = $booking;
            }
        }
        return $data;

//        $daysToTry = 30;
//
//        if ($reverse) {
//            $endTime = $startTime;
//            $startTime = strtotime("- $daysToTry days", $startTime);
//
//        } else {
//            $endTime = strtotime("+ $daysToTry days", $startTime);
//        }
//
//
//        $events = self::getAvailability($startTime,$endTime,$cookies)['events'];
//
//        $count = count($events);
//
//        if ($count == 0) {
//            if($reverse){
//                $endTime = $startTime;
//                $startTime = 0;
//            }else{
//                $startTime= $endTime;
//                $endTime = 2147480000;
//            }
//            $events = self::getAvailability($startTime,$endTime,$cookies)['events'];
//        }
//
//        $runningTotal = 0;
//
//        foreach ($events as $event) {
//            if ($event['status'] == $type) {
//                $data[] = $event['booking'];
//                $runningTotal++;
//            }
//            if ($runningTotal == $pageSize) {
//                break;
//            }
//        }
//        if ($runningTotal == $pageSize||$count==0) {
//            return $data;
//        } else {
//            return self::getFromAvailability($cookies, $endTime, $pageSize - $runningTotal, $type, $reverse, $data);
//        }

    }

    static function getDateFromCal($date){
        $components = explode(", ",$date);
        return mktime($components[3],$components[4],0,$components[1]+1,$components[2],$components[0]);
    }

    static function getAvailability($startTime, $endTime, $cookies,$url ="/availabilities.json?")
    {
        $url = Constants::$url . $url."start=$startTime&end=$endTime";

        $str = self::get($url, $cookies);

        $str = str_replace('new Date(', '"', $str);
        $str = str_replace('),', '",', $str);
        $str = str_replace('""}', '"}', $str);
        $str = str_replace('events', '"events"', $str);

        return json_decode($str, true);
    }

    static function build_sorter($key)
    {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    static function getAuthTokens($url, $cookies)
    {
        $result = self::get($url, $cookies);
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($result);
        $finder = new \DomXPath($doc);
        $scripts = $finder->query("//script");
        $regxp = "/var \\\$auth_token = '(.*)';/";

        $return = array();
        foreach ($scripts as $s) {
            # see if there are any matches for var datePickerDate in the script node's contents
            if (preg_match($regxp, $s->nodeValue, $matches)) {
                # the date itself (captured in brackets) is in $matches[1]
                $return[] = urldecode($matches[1]);
            }
        }
        return $return;
    }

    static function getList($result,&$allData=array())
    {
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($result);
        $finder = new \DomXPath($doc);
        $classname = "rw ui-accordion-header ui-helper-reset ui-state-default";
        $jobs = $finder->query("//*[contains(@class, '$classname')]");
        $finder = new \DomXPath($doc);
        $pageNumber = $finder->query("//*[contains(@class, 'current')]");
        if ($pageNumber->length == 0) {
            $currentPage = 1;
        } else {
            $currentPage = $finder->query("//*[contains(@class, 'current')]")->item(0)->nodeValue;
        }

        $return = array();
        foreach ($jobs as $job) {
            $docs = new \DOMDocument();
            $docs->appendChild($docs->importNode($job, true));
            $docs->strictErrorChecking = true;
            $xpath = new \DOMXPath($docs);
            $table_rows = $xpath->query("//*[starts-with(@id, 'expand_')]");
            $idList = explode("_", $table_rows->item(0)->getAttribute("id"));
            if (is_numeric($idList[1])) {
                $detailsID = $idList[1];
            } else {
                $detailsID = $idList[2];
            }
            $basicInfo = $xpath->query("//div[starts-with(@class, 'small-cell')]");
            $allData[]=$basicInfo;
            $jobID = trim(preg_replace('/\s\s+/', '', $basicInfo->item(0)->nodeValue));
            $data = array("DetailsID" => $detailsID, "JobID" => $jobID);
            $return[] = $data;
        }
        return array('jobs' => $return, 'pageNumber' => $currentPage);
    }

    static function getElementsByClass($result, $class)
    {
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($result);
        $finder = new \DomXPath($doc);
        $jobData = $finder->query("//*[contains(@class, '$class')]");
        return $jobData;
    }

    static function getElementsByID($result, $id)
    {
        $doc = new \DomDocument;
        $doc->validateOnParse = true;
        @$doc->loadHTML($result);
        $finder = new \DomXPath($doc);
        $jobData = $finder->query("//*[contains(@id, '$id')]");
        return $jobData;
    }

    static function getElementFromJavaScript($tag, $data, $delimiter = "\""){
        $regXp = '/'.$tag.$delimiter.'([^'.$delimiter.'\\\\]*(?:\\\\.[^'.$delimiter.'\\\\]*)*)'.$delimiter.'/';
        preg_match_all($regXp, $data, $matches);
        $match = "";
        foreach ($matches[1] as $rawMatch){
            $match .= str_replace("\\","",$rawMatch);
        }
        $dom=new \DomDocument;
        @$dom->loadHTML($match);
        return $dom->documentElement;
    }


}