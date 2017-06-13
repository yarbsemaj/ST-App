<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Utils;

class TimeSheet extends Controller
{
    public function approved(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/timesheets?#tosubmit',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmorets';
        $post = array(
            'type' => 'agreed',
            'what' => 'wts',
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

                $timesheetInfo = Utils::get(
                    "https://www.studenttemp.co.uk/timesheets/pending?type=t&ts=$detailsID", $cookies);
                $data[] = $this->getTimeSheetData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function submitted(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/timesheets?#tosubmit',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmorets';
        $post = array(
            'type' => 'pending',
            'what' => 'wts',
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

                $timesheetInfo = Utils::get(
                    "https://www.studenttemp.co.uk/timesheets/pending?type=t&ts=$detailsID", $cookies);
                $data[] = $this->getTimeSheetData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function disputed(Request $request)
    {

        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/timesheets?#tosubmit',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmorets';
        $post = array(
            'type' => 'notagreed',
            'what' => 'wts',
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

                $timesheetInfo = Utils::get(
                    "https://www.studenttemp.co.uk/timesheets/disputed?type=t&ts=$detailsID", $cookies);
                $data[] = $this->getTimeSheetData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function toSubmit(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/timesheets?#tosubmit',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmorets';
        $post = array(
            'type' => 'tosubmit',
            'what' => 'wts',
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

                $timesheetInfo = Utils::get(
                    "https://www.studenttemp.co.uk/timesheets/tosubmit?type=b&ts=$detailsID", $cookies);
                $data[] = $this->getTimeSheetToSubmit(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function canceled(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/timesheets?#tosubmit',$cookies);

        $url = 'https://www.studenttemp.co.uk/showmorets';
        $post = array(
            'type' => 'cancelled',
            'what' => 'wts',
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

                $timesheetInfo = Utils::get(
                    "https://www.studenttemp.co.uk/timesheets/cancelled?type=t&ts=$detailsID", $cookies);
                $data[] = $this->getTimeSheetData(Utils::getElementsByClass($timesheetInfo,'data'), $job['DetailsID']);
            }
        }
        return Utils::response($data);
    }

    public function submitTimeSheet(Request $request)
    {
        $cookies = Utils::getCookies($request->cookies);
        $tokens = Utils::getAuthTokens('https://www.studenttemp.co.uk/timesheets?#tosubmit',$cookies);

        $url = 'https://www.studenttemp.co.uk/timesheets.json';
        $post = array(
            'timesheet[status]' => 'S',
            'timesheet[startTime]' => $request->startTime,
            'timesheet[endTime]' => $request->endTime,
            'timesheet[breakTime]' => $request->breakTime,
            'timesheet[booking_id]' => $request->bookingID,

            'timesheet_comments_comment' => $request->comment,
            'rating' => $request->rating,
            'authenticity_token' => $tokens[1]
        );

        if($post['timesheet_comments_comment']==null){
            $post['timesheet_comments_comment']="";
        }


       Utils::post($url,$post,$cookies);

        return Utils::response($post);
    }

    function getTimeSheetData($jobData, $detailsID)
    {
        $return = array();
        $fieldNames = array("ID", "Company", "Type", "Location", "Supervisor","Contact", "Pay", "StartDate", "StartTime", "EndDate", "EndTime", "Break", "Event", "EventInfo", "TotalPay", "Hours");
        $i = 0;
        $j = 0;
        foreach ($jobData as $job) {
            switch ($i) {
                case 0:
                case 5:
                case 14:
                    $return[$fieldNames[$j]] = preg_replace("/[^0-9,.£]/", "", $job->nodeValue);
                    break;
                case 4:
                    $supervisorPhone = explode (" - ",$job->nodeValue);
                    $return[$fieldNames[$j]] = $supervisorPhone[0];
                    $j++;
                    $return[$fieldNames[$j]] = $supervisorPhone[1];
                    break;
                case 6:
                case 8:
                    $value = Utils::getInputValue($job);
                    $return[$fieldNames[$j]] = $value->item(0)->getAttribute('value');
                    break;
                case 7:
                case 9:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue . ":" . $selectedOption->item(1)->nodeValue;
                    break;
                case 10:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue;
                    break;
                case 13:
                case 16:
                    $j--;
                    break;
                default:
                    $return[$fieldNames[$j]] = $job->nodeValue;
                    break;
            }
            $i++;
            $j++;
        }
        $return['DetailsID'] = $detailsID;
        return $return;
    }

    function getTimeSheetToSubmit($jobData, $detailsID)
    {
        $return = array();
        $fieldNames = array("ID", "Company", "Type", "Location", "Supervisor","Contact", "Pay", "StartDate", "StartTime", "EndDate", "EndTime", "Break", "Event", "EventInfo", "TotalPay", "Hours");
        $i = 0;
        $j = 0;
        foreach ($jobData as $job) {
            switch ($i) {
                case 0:
                case 5:
                case 16:
                case 15:
                    $return[$fieldNames[$j]] = preg_replace("/[^0-9,.£]/", "", $job->nodeValue);
                    break;
                case 4:
                    $supervisorPhone = explode (" - ",$job->nodeValue);
                    $return[$fieldNames[$j]] = $supervisorPhone[0];
                    $j++;
                    $return[$fieldNames[$j]] = $supervisorPhone[1];
                    break;
                case 6:
                case 8:
                    $value = Utils::getInputValue($job);
                    $return[$fieldNames[$j]] = $value->item(0)->getAttribute('value');
                    break;
                case 7:
                case 9:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue . ":" . $selectedOption->item(1)->nodeValue;
                    break;
                case 10:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue;
                    break;
                case 14:
                case 17:
                case 13:
                    $j--;
                    break;
                default:
                    $return[$fieldNames[$j]] = $job->nodeValue;
                    break;
            }
            $i++;
            $j++;
        }
        $return['DetailsID'] = $detailsID;
        return $return;
    }

}
