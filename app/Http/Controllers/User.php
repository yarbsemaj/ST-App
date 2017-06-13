<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Utils;

class User extends Controller
{
    function get(Request $request){
        $cookies = Utils::getCookies($request->cookies);

        $url = 'https://www.studenttemp.co.uk/profile';

        $result=Utils::get($url,$cookies);
        $totalPay=$this->getTotalPay($result);

        $id ='profile';
        $tabs = Utils::getElementsByID($result,$id);
        $userID= explode ( '_' ,$tabs[0]->attributes['id']->value)[1];

        $url = "https://www.studenttemp.co.uk/student_profile_sections/1/edit.html?view=worker&worker_id=$userID";
        $result=Utils::get($url,$cookies);
        $list = Utils::getElementsByClass($result,'data');
        $data = $this->getUserInfo($list);

        $data['UserID'] = $userID;
        $data['TotalPay']= $totalPay;
        
        return Utils::response($data);
    }

    function getTotalPay($result){
        $class='sideDashboard-header ui-accordion-header ui-helper-reset ui-state-default';

        $nodes = Utils::getElementsByClass($result,$class);

        return preg_replace("/[^0-9,.£]/", '',explode ( ':' , $nodes[0]->nodeValue)[1]);
    }

    function getUserInfo($userData)
    {
        $return = array();
        $fieldNames = array("Title", "FirstName", "SirName", "DOB", "Gender", "PictureURL");
        $i = 0;

        foreach ($userData as $dataElement) {
            switch ($i) {
                case 1:
                case 2:
                case 3:
                    $value = Utils::getInputValue($dataElement);
                    $return[$fieldNames[$i]] = $value->item(0)->getAttribute('value');
                    break;
                case 0:
                case 4:
                    $selectedOption = Utils::getSelectedOptionDisplay($dataElement);
                    $return[$fieldNames[$i]] = $selectedOption->item(0)->nodeValue;
                    break;
                case 5:
                    $return[$fieldNames[$i]] = Utils::getElementsByID(
                        $dataElement->ownerDocument->saveHTML($dataElement),
                        'photoproof_link')[0]->getAttribute('href');
                        break;
                default:
                    $return[$fieldNames[$i]] = $dataElement->nodeValue;
                    break;
            }
            $i++;
        }
        return $return;
    }
}
