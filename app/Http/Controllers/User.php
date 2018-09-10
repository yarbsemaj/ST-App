<?php

namespace App\Http\Controllers;

use app\Http\Constants;
use App\Http\Utils;
use Illuminate\Http\Request;

class User extends Controller
{
    function get(Request $request){

        $url = Constants::$url.'/profile';

        $result=Utils::get($url,$request->cookies);
        $totalPay=$this->getTotalPay($result);

        $id ='profile';
        $tabs = Utils::getElementsByID($result,$id);
        $userID= explode ( '_' ,$tabs[0]->attributes['id']->value)[1];

        $url = Constants::$url."/student_profile_sections/1/edit.html?view=worker&worker_id=$userID";
        $result=Utils::get($url,$request->cookies);
        $list = Utils::getElementsByClass($result,'data');
        $data = $this->getUserInfo($list);

        $data['UserID'] = $userID;
        $data['TotalPay']= $totalPay;
        
        return Utils::response($data);
    }

    function getIMG(Request $request){

        $url = Constants::$url.'/profile';

        $result=Utils::get($url,$request->cookies);

        $id ='profile';
        $tabs = Utils::getElementsByID($result,$id);
        $userID= explode ( '_' ,$tabs[0]->attributes['id']->value)[1];


        $data['UserID'] = $userID;
        $headers = array();

        $image = Utils::get("https://www.studenttemp.co.uk/docdownload?dcode=profile_attachments&type=photoproofs&viewno=$userID&view=original",$request->cookies,$headers);

        return response($image,200,
            ["Content-Type"=>$headers[4]]);
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
                    $return[$fieldNames[$i]] = route("user.get.img",["email"=>request()->input("email"),"password"=>request()->input("password")]);
                        break;
                default:
                    $return[$fieldNames[$i]] = $dataElement->nodeValue;
                    break;
            }
            $i++;
        }
        return $return;
    }

    function coordinators(Request $request)
    {

        $url = Constants::$url . '/profile';

        $result = Utils::get($url, $request->cookies);


        $list = Utils::getElementsByClass($result, 'data');
        $data = $this->getCoordinatorInfo($list);


        return Utils::response($data);
    }

    function getCoordinatorInfo($userData)
    {
        $return = array();
        $fieldNames = array("Title", "Name", "JobTitle", "Number", "Email");
        $i = 0;

        foreach ($userData as $dataElement) {
            switch ($i) {
                case 1:
                case 2:
                case 3:
                case 4:
                    $value = Utils::getInputValue($dataElement);
                    $return[$fieldNames[$i]] = $value->item(0)->getAttribute('value');
                    break;
                case 0:
                    $selectedOption = Utils::getSelectedOptionDisplay($dataElement);
                    $return[$fieldNames[$i]] = $selectedOption->item(0)->nodeValue;
                    break;
                case 5:
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
