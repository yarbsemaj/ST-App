<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Utils;

class Jobs extends Controller
{
    public function active(Request $request)
    {
        $jobs = array();
        if($request->page==1) {
            $cookies = Utils::getCookies($request->cookies);

            if($request->endTime == null) $request->endTime = 2147480000;

            $jobData = Utils::getFromAvailability($cookies, strtotime("midnight"), $request->endTime, "A");

            usort($jobData, Utils::build_sorter('timeStamp'));

            foreach ($jobData as $job) {

                $location = explode(" - ", $job['location'])[1];

                $supervisorPhone = explode (" - ",$job['locationContact']);

                $jobs [] = $returnData = array(
                    "DetailsID" => (string)$job['id'],
                    "ID" => (string)$job['ref'],
                    "Type" => $job['jobTitle'],
                    "Pay" => "£" . $job['payRate'],
                    "Building" => $location,
                    "Supervisor" => $supervisorPhone[0],
                    "Contact" => $supervisorPhone[1],
                    "Address" => $job['address'],
                    "DressCode" => $job['dressCode'],
                    "Info" => $job['notes'],
                    "StartDate" => $job['date'],
                    "EndDate" => $job['date'],
                    "StartTime" => $job['start'],
                    "EndTime" => $job['end'],
                    "Repeat" => $job['repeatInfo'],
                    "Event" => $job['bookingType']
                );
            }
        }

        return Utils::response($jobs);
    }

    public function cancelled(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/bookings#upcoming',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmorebk';
        $post = array(
            'type' => 'cancelled',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $tokens[1]
        );

        $result=Utils::post($url,$post,$cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get("https://www.studenttemp.co.uk/bookings/$detailsID?type=cancelled",$cookies);
                $data[] = $this->getJobData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function offered(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/bookings#upcoming',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmoreof';
        $post = array(
            'type' => 'offered',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $tokens[1]
        );

        $result=Utils::post($url,$post,$cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get("https://www.studenttemp.co.uk/bookings/$detailsID?type=offered",$cookies);
                $data[] = $this->getJobData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function rejected(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/bookings#upcoming',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmoreof';
        $post = array(
            'type' => 'rejected',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $tokens[1]
        );

        $result=Utils::post($url,$post,$cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get("https://www.studenttemp.co.uk/bookings/$detailsID?type=offered",$cookies);
                $data[] = $this->getJobData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function expired(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/bookings#upcoming',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmoreof';
        $post = array(
            'type' => 'expired',
            'what' => 'bk',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $tokens[1]
        );

        $result=Utils::post($url,$post,$cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get("https://www.studenttemp.co.uk/bookings/$detailsID?type=cancelled",$cookies);
                $data[] = $this->getJobData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    function getJobData($jobData, $detailsID)
    {
        $return = array();
        $fieldNames = array("ID", "Type", "Pay", "Building", "Supervisor","Contact", "Address", "DressCode", "Info", "StartDate", "EndDate", "StartTime", "EndTime", "Repeat", "Event", "Event");
        $i = 0;
        $j=0;
        foreach ($jobData as $job) {
            switch ($i) {
                case 4:
                    $supervisorPhone = explode (" - ",$job->nodeValue);
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

}
