<?php

namespace App\Http\Controllers;

use app\Http\Constants;
use app\Http\Utils;
use Illuminate\Http\Request;

class JobOffers extends Controller
{
    public function offered(Request $request)
    {
        $url = Constants::$url . '/showmoreof';
        $post = array(
            'type' => 'offered',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );
        $result = Utils::post($url, $post, $request->cookies);

        $data = array();
        $allData = array();
        $list = Utils::getList($result, $allData);
        if ($request->page <= $list['pageNumber']) {
            $i = 0;
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];
                $barData = $allData[$i];
                $results = Utils::get(Constants::$url . "/bookings/$detailsID?type=offered", $request->cookies);
                $fieldNames = array("ID", "Type", "Pay", "Building", "Supervisor", "Contact", "Address", "DressCode", "Info",
                    "StartDate", "EndDate", "StartTime", "EndTime", "Repeat", "Expiry", "Event", "EventInfo", "CostCode",
                    "BudgetCode", "PONo", "AuthPerson", "GLCode");
                $element = $this->getOfferedData(Utils::getElementsByClass($results, 'data'), $job['DetailsID'], $fieldNames);
                $element['Student'] = $barData->item(3)->nodeValue;
                $data[] = $element;
                $i++;
            }
        }
        return Utils::response($data);
    }

    function getOfferedData($jobData, $detailsID, $fieldNames)
    {
        $return = array();
        $i = 0;
        $j = 0;
        foreach ($jobData as $job) {
            switch ($i) {
                case 4:
                    $supervisorPhone = explode(" - ", $job->nodeValue);
                    $return[$fieldNames[$j]] = $supervisorPhone[0];
                    $j++;
                    $return[$fieldNames[$j]] = $supervisorPhone[1];
                    break;
                case 2:
                    $return[$fieldNames[$j]] = preg_replace("/[^0-9,.£]/", "", $job->nodeValue);
                    break;
                default:
                    $return[$fieldNames[$j]] = $job->nodeValue;
            }
            $i++;
            $j++;
        }
        $return['DetailsID'] = $detailsID;
        return $return;
    }

    public function scheduled(Request $request)
    {
        $url = Constants::$url . '/showmoreof';
        $post = array(
            'type' => 'scheduled',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );
        $result = Utils::post($url, $post, $request->cookies);

        $data = array();
        $allData = array();
        $list = Utils::getList($result, $allData);
        if ($request->page <= $list['pageNumber']) {
            $i = 0;
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];
                $barData = $allData[$i];
                $results = Utils::get(Constants::$url . "/bookings/$detailsID?type=scheduled", $request->cookies);
                $fieldNames = array("ID", "Type", "Pay", "Building", "Supervisor", "Contact", "Address", "DressCode", "Info",
                    "StartDate", "EndDate", "StartTime", "EndTime", "Repeat", "Expiry", "Schedule", "Event", "EventInfo", "CostCode",
                    "BudgetCode", "PONo", "AuthPerson", "GLCode");
                $element = $this->getOfferedData(Utils::getElementsByClass($results, 'data'), $job['DetailsID'], $fieldNames);
                $element['Student'] = $barData->item(3)->nodeValue;
                $data[] = $element;
                $i++;
            }
        }
        return Utils::response($data);
    }

    public function rejected(Request $request)
    {
        $url = Constants::$url . '/showmoreof';
        $post = array(
            'type' => 'rejected',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );
        $result = Utils::post($url, $post, $request->cookies);

        $data = array();
        $allData = array();
        $list = Utils::getList($result, $allData);
        if ($request->page <= $list['pageNumber']) {
            $i = 0;
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];
                $barData = $allData[$i];
                $results = Utils::get(Constants::$url . "/bookings/$detailsID?type=rejected", $request->cookies);
                $fieldNames = array("ID", "Type", "Pay", "Building", "Supervisor", "Contact", "Address", "DressCode", "Info",
                    "StartDate", "EndDate", "StartTime", "EndTime", "Repeat", "Event", "EventInfo", "CostCode",
                    "BudgetCode", "PONo", "AuthPerson", "GLCode");
                $element = $this->getOfferedData(Utils::getElementsByClass($results, 'data'), $job['DetailsID'], $fieldNames);
                $element['Student'] = $barData->item(3)->nodeValue;
                $data[] = $element;
                $i++;
            }
        }
        return Utils::response($data);
    }

    public function expired(Request $request)
    {
        $url = Constants::$url . '/showmoreof';
        $post = array(
            'type' => 'expired',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );
        $result = Utils::post($url, $post, $request->cookies);

        $data = array();
        $allData = array();
        $list = Utils::getList($result, $allData);
        if ($request->page <= $list['pageNumber']) {
            $i = 0;
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];
                $barData = $allData[$i];
                $results = Utils::get(Constants::$url . "/bookings/$detailsID?type=expired", $request->cookies);
                $fieldNames = array("ID", "Type", "Pay", "Building", "Supervisor", "Contact", "Address", "DressCode", "Info",
                    "StartDate", "EndDate", "StartTime", "EndTime", "Repeat", "Event", "EventInfo", "CostCode",
                    "BudgetCode", "PONo", "AuthPerson", "GLCode");
                $element = $this->getOfferedData(Utils::getElementsByClass($results, 'data'), $job['DetailsID'], $fieldNames);
                $element['Student'] = $barData->item(3)->nodeValue;
                $data[] = $element;
                $i++;
            }
        }
        return Utils::response($data);
    }

    public function accepted(Request $request)
    {
        $url = Constants::$url . '/showmorebk';
        $post = array(
            'type' => 'upcoming',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );
        $result = Utils::post($url, $post, $request->cookies);

        $data = array();
        $allData = array();
        $list = Utils::getList($result, $allData);
        if ($request->page <= $list['pageNumber']) {
            $i = 0;
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];
                $barData = $allData[$i];
                $results = Utils::get(Constants::$url . "/bookings/$detailsID?type=upcoming", $request->cookies);
                $fieldNames = array("ID", "Type", "Pay", "Building", "Supervisor", "Contact", "Address", "DressCode", "Info",
                    "StartDate", "EndDate", "StartTime", "EndTime", "Repeat", "Event", "EventInfo", "CostCode",
                    "BudgetCode", "PONo", "AuthPerson", "GLCode");
                $element = $this->getOfferedData(Utils::getElementsByClass($results, 'data'), $job['DetailsID'], $fieldNames);
                $element['Student'] = $barData->item(3)->nodeValue;
                $data[] = $element;
                $i++;
            }
        }
        return Utils::response($data);
    }

    public function cancelled(Request $request)
    {
        $url = Constants::$url . '/showmorebk';
        $post = array(
            'type' => 'cancelled',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );
        $result = Utils::post($url, $post, $request->cookies);

        $data = array();
        $allData = array();
        $list = Utils::getList($result, $allData);
        if ($request->page <= $list['pageNumber']) {
            $i = 0;
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];
                $barData = $allData[$i];
                $results = Utils::get(Constants::$url . "/bookings/$detailsID?type=cancelled", $request->cookies);
                $fieldNames = array("ID", "Type", "Pay", "Building", "Supervisor", "Contact", "Address", "DressCode", "Info",
                    "StartDate", "EndDate", "StartTime", "EndTime", "Repeat", "CancellationReason", "Event", "EventInfo", "CostCode",
                    "BudgetCode", "PONo", "AuthPerson", "GLCode");
                $element = $this->getOfferedData(Utils::getElementsByClass($results, 'data'), $job['DetailsID'], $fieldNames);
                $element['Student'] = $barData->item(3)->nodeValue;
                $data[] = $element;
                $i++;
            }
        }
        return Utils::response($data);
    }

}
