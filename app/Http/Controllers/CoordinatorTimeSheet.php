<?php

namespace App\Http\Controllers;

use app\Http\Constants;
use App\Http\Utils;
use Illuminate\Http\Request;

class CoordinatorTimeSheet extends Controller
{
    public function toApprove(Request $request)
    {
        $url = Constants::$url . '/showmorets';
        $post = array(
            'type' => 'tosubmit',
            'what' => 'ets',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );

        $result = Utils::post($url, $post, $request->cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get(
                    Constants::$url . "/timesheets/tosubmit?type=t&ts=$detailsID", $request->cookies);
                $mainData = $this->getTimeSheetData(Utils::getElementsByClass($timesheetInfo, 'data'), $job['DetailsID']);
                $mainData['UserID'] = Utils::getInputValue(Utils::getElementsByID($timesheetInfo, 'user_id')
                    ->item(0))->item(0)->getAttribute('value');
                preg_match("/renderStars_[0-9]{5}\(([0-9]), ([0-9]), ([0-9]), ([a-z]*)\)/", $timesheetInfo, $stars);
                $mainData['Punctuality'] = $stars[1];
                $mainData['Communication'] = $stars[2];
                $mainData['Instructions'] = $stars[3];
                $mainData['editable'] = true;
                $mainData['cancel'] = 2;
                $data[] = $mainData;
            }
        }
        return Utils::response($data);
    }

    function getTimeSheetData($jobData, $detailsID)
    {
        $return = array();
        $fieldNames = array("ID", "Student", "Job", "Location", "Address", "ContactName", "ContactPhone", "Charge", "Pay",
            "StartDate", "StartTime", "EndDate", "EndTime", "Break", "SubmittedOn", "Event", "EventInfo", "CostCode",
            "BudgetCode", "PONo", "AuthPerson", "GLCode", "TotalPay",
            "Hours", "TotalCharge");
        $i = 0;
        $j = 0;
        foreach ($jobData as $job) {

            //print "<h1>$i</h1>";
            //print $job->ownerDocument->saveHTML($job);


            switch ($i) {
                case 0:
                case 7:
                case 8:
                case 26:
                case 28:
                    $return[$fieldNames[$j]] = preg_replace("/[^0-9,.£]/", "", $job->nodeValue);
                    break;
                case 9:
                case 11:
                    $value = Utils::getInputValue($job);
                    $return[$fieldNames[$j]] = $value->item(0)->getAttribute('value');
                    break;
                case 12:
                case 10:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue . ":" . $selectedOption->item(1)->nodeValue;
                    break;
                case 13:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue;
                    break;
                case 22:
                case 23:
                case 24:
                case 25:
                case 29:
                    $j--;
                    break;
                case 14:
                    $return[$fieldNames[$j]] = preg_replace("/\s+/", " ", $job->nodeValue);
                    break;
                default:
                    $return[$fieldNames[$j]] = $job->nodeValue;
                    break;
            }
            $j++;
            $i++;
        }
        $format = "d/m/Y H:i";
        $dateobj = \DateTime::createFromFormat($format, $return["StartDate"] . $return["StartTime"]);
        $return["Start"] = $dateobj->getTimestamp();
        $dateobj = \DateTime::createFromFormat($format, $return["EndDate"] . $return["EndTime"]);
        $return["End"] = $dateobj->getTimestamp();
        $return['DetailsID'] = $detailsID;
        return $return;
    }

    public function disputed(Request $request)
    {
        $url = Constants::$url . '/showmorets';
        $post = array(
            'type' => 'pending',
            'what' => 'ets',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );

        $result = Utils::post($url, $post, $request->cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get(
                    Constants::$url . "/timesheets/pending?type=t&ts=$detailsID", $request->cookies);
                $mainData = $this->getTimeSheetData(Utils::getElementsByClass($timesheetInfo, 'data'), $job['DetailsID']);
                $mainData['UserID'] = Utils::getInputValue(Utils::getElementsByID($timesheetInfo, 'user_id')
                    ->item(0))->item(0)->getAttribute('value');
                preg_match("/renderStars_[0-9]{5}\(([0-9]), ([0-9]), ([0-9]), ([a-z]*)\)/", $timesheetInfo, $stars);
                $mainData['Punctuality'] = $stars[1];
                $mainData['Communication'] = $stars[2];
                $mainData['Instructions'] = $stars[3];
                $mainData['editable'] = true;
                $mainData['cancel'] = 2;
                $data[] = $mainData;
            }
        }
        return Utils::response($data);
    }

    public function approved(Request $request)
    {
        $url = Constants::$url . '/showmorets';
        $post = array(
            'type' => 'agreed',
            'what' => 'ets',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );

        $result = Utils::post($url, $post, $request->cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get(
                    Constants::$url . "/timesheets/agreed?type=t&ts=$detailsID", $request->cookies);
                $mainData = $this->getApprovedTimeSheetData(Utils::getElementsByClass($timesheetInfo, 'data'), $job['DetailsID']);
                $mainData['UserID'] = Utils::getInputValue(Utils::getElementsByID($timesheetInfo, 'user_id')
                    ->item(0))->item(0)->getAttribute('value');
                preg_match("/renderStars_[0-9]{5}\(([0-9]), ([0-9]), ([0-9]), ([a-z]*)\)/", $timesheetInfo, $stars);
                $mainData['Punctuality'] = $stars[1];
                $mainData['Communication'] = $stars[2];
                $mainData['Instructions'] = $stars[3];
                $mainData['editable'] = false;
                $mainData['cancel'] = 0;
                $data[] = $mainData;
            }
        }
        return Utils::response($data);
    }

    function getApprovedTimeSheetData($jobData, $detailsID)
    {
        $return = array();
        $fieldNames = array("ID", "Student", "Job", "Location", "Address", "ContactName", "ContactPhone", "Charge", "Pay",
            "StartDate", "StartTime", "EndDate", "EndTime", "Break", "SubmittedOn", "SubmittedBy", "SubmittedOn",
            "ApprovedBy", "ApprovedOn", "Event", "EventInfo", "CostCode", "BudgetCode", "PONo", "AuthPerson", "GLCode",
            "TotalPay", "Hours", "TotalCharge");
        $i = 0;
        $j = 0;
        foreach ($jobData as $job) {

            //print "<h1>$i</h1>";
            //print $job->ownerDocument->saveHTML($job);


            switch ($i) {
                case 0:
                case 7:
                case 8:
                case 30:
                case 32:
                    $return[$fieldNames[$j]] = preg_replace("/[^0-9,.£]/", "", $job->nodeValue);
                    break;
                case 9:
                case 11:
                    $value = Utils::getInputValue($job);
                    $return[$fieldNames[$j]] = $value->item(0)->getAttribute('value');
                    break;
                case 12:
                case 10:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue . ":" . $selectedOption->item(1)->nodeValue;
                    break;
                case 13:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue;
                    break;
                case 26:
                case 27:
                case 28:
                case 29:
                case 33:
                    $j--;
                    break;
                case 14:
                    $return[$fieldNames[$j]] = preg_replace("/\s+/", " ", $job->nodeValue);
                    break;
                default:
                    $return[$fieldNames[$j]] = $job->nodeValue;
                    break;
            }
            $j++;
            $i++;
        }
        $return['DetailsID'] = $detailsID;

        $format = "d/m/Y H:i";
        $dateobj = \DateTime::createFromFormat($format, $return["StartDate"] . $return["StartTime"]);
        $return["Start"] = $dateobj->getTimestamp();
        $dateobj = \DateTime::createFromFormat($format, $return["EndDate"] . $return["EndTime"]);
        $return["End"] = $dateobj->getTimestamp();
        return $return;
    }

    public function withStudent(Request $request)
    {
        $url = Constants::$url . '/showmorets';
        $post = array(
            'type' => 'withstudent',
            'what' => 'ets',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );

        $result = Utils::post($url, $post, $request->cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get(
                    Constants::$url . "/timesheets/withstudent?type=b&ts=$detailsID", $request->cookies);
                $mainData = $this->getStudentTimeSheetData(Utils::getElementsByClass($timesheetInfo, 'data'), $job['DetailsID']);
                $mainData = $this->getStudentTimeSheetData(Utils::getElementsByClass($timesheetInfo, 'data'), $job['DetailsID']);
                $mainData['editable'] = false;
                $mainData['cancel'] = 1;
                $data[] = $mainData;
            }
        }
        return Utils::response($data);
    }

    function getStudentTimeSheetData($jobData, $detailsID)
    {
        $return = array();
        $fieldNames = array("ID", "Student", "Job", "Location", "Address", "ContactName", "ContactPhone", "Charge", "Pay",
            "StartDate", "StartTime", "EndDate", "EndTime", "Break", "Event", "EventInfo", "CostCode", "BudgetCode", "PONo", "AuthPerson", "GLCode",
            "TotalPay", "Hours", "blar");
        $i = 0;
        $j = 0;
        foreach ($jobData as $job) {

            //print "<h1>$i</h1>";
            //print $job->ownerDocument->saveHTML($job);


            switch ($i) {
                case 0:
                case 7:
                case 8:
                case 23:
                case 22:
                    $return[$fieldNames[$j]] = preg_replace("/[^0-9,.£]/", "", $job->nodeValue);
                    break;
                case 9:
                case 11:
                    $value = Utils::getInputValue($job);
                    $return[$fieldNames[$j]] = $value->item(0)->getAttribute('value');
                    break;
                case 12:
                case 10:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue . ":" . $selectedOption->item(1)->nodeValue;
                    break;
                case 13:
                    $selectedOption = Utils::getSelectedOptionValue($job);
                    $return[$fieldNames[$j]] = $selectedOption->item(0)->nodeValue;
                    break;
                case 21:
                case 24:
                    $j--;
                    break;
                case 14:
                    $return[$fieldNames[$j]] = preg_replace("/\s+/", " ", $job->nodeValue);
                    break;
                default:
                    $return[$fieldNames[$j]] = $job->nodeValue;
                    break;
            }
            $j++;
            $i++;
        }
        $return['DetailsID'] = $detailsID;
        $format = "d/m/Y H:i";
        $dateobj = \DateTime::createFromFormat($format, $return["StartDate"] . $return["StartTime"]);
        $return["Start"] = $dateobj->getTimestamp();
        $dateobj = \DateTime::createFromFormat($format, $return["EndDate"] . $return["EndTime"]);
        $return["End"] = $dateobj->getTimestamp();
        return $return;
    }

    public function cancelled(Request $request)
    {
        $url = Constants::$url . '/showmorets';
        $post = array(
            'type' => 'cancelled',
            'what' => 'ets',
            'paginate_size' => $request->pageSize,
            'page' => $request->page,
            'search' => '',
            'authenticity_token' => $request->authToken[1]
        );

        $result = Utils::post($url, $post, $request->cookies);

        $data = array();

        $list = Utils::getList($result);
        if ($request->page <= $list['pageNumber']) {
            foreach ($list['jobs'] as $job) {
                $detailsID = $job['DetailsID'];

                $timesheetInfo = Utils::get(
                    Constants::$url . "/timesheets/cancelled?type=t&ts=$detailsID", $request->cookies);
                $mainData = $this->getTimeSheetData(Utils::getElementsByClass($timesheetInfo, 'data'), $job['DetailsID']);
                $mainData['editable'] = false;
                $mainData['cancel'] = 0;
                preg_match("/renderStars_[0-9]{5}\(([0-9]), ([0-9]), ([0-9]), ([a-z]*)\)/", $timesheetInfo, $stars);
                $mainData['Punctuality'] = $stars[1];
                $mainData['Communication'] = $stars[2];
                $mainData['Instructions'] = $stars[3];
                $mainData['editable'] = false;
                $mainData['cancel'] = 0;
                $data[] = $mainData;
                $data[] = $mainData;
            }
        }
        return Utils::response($data);
    }

    function getWarnings(Request $request)
    {
        $startData = date(Constants::$dateFormat1, (int)$request->startTime);
        $endDate = date(Constants::$dateFormat1, (int)$request->endTime);
        $breakTime = $request->breakTime;
        $timeSheetID = $request->timesheetID;

        $data = array("timesheet[startTime]" => $startData,
            "timesheet[endTime]" => $endDate,
            "timesheet[id]" => $timeSheetID,
            "timesheet[breakTime]" => $breakTime,
            "timesheet[recType]" => "t",
            "timesheet[override_times]" => "true");

        $data = Utils::buildQuery($data);
        $return = array();

        $url = Constants::$url . '/totalCharge.json?';
        $result = Utils::get($url . $data, $request->cookies);
        $return = array_merge(json_decode($result, true), $return);

        $url = Constants::$url . '/hhex.json?';
        $result = Utils::get($url . $data, $request->cookies);
        $return = array_merge(json_decode($result, true), $return);

        return Utils::response($return);
    }

    function amendBookings(Request $request)
    {
        $data = json_decode($request->data, true);
        $type = $request->type;
        $comment = $request->comment;

        foreach ($data as $datum) {
            $individualRequest = new Request();
            $individualRequest->authToken = $request->authToken;
            $individualRequest->cookies = $request->cookies;
            $individualRequest->type = $type;
            $individualRequest->startTime = $datum['Start'];
            $individualRequest->endTime = $datum['End'];
            $individualRequest->breakTime = $datum['Break'];
            $individualRequest->comment = $comment;
            $individualRequest->timeSheetID = $datum['DetailsID'];
            $individualRequest->userID = @$datum['UserID'];
            $individualRequest->punctuality = @$datum['Punctuality'];
            $individualRequest->communication = @$datum['Communication'];
            $individualRequest->initiative = @$datum['Instructions'];
            if ($datum['cancel'] == 1) {
                $individualRequest->type = "Simple";
            }
            $this->amendBooking($individualRequest);
        }

        return Utils::response(null);
    }

    function amendBooking(Request $request)
    {
        if ($request->type == "Simple") return $this->cancelTimeSheet($request);
        $url = "https://www.studenttemp.co.uk/timesheets/" . $request->timeSheetID . ".json";
        $data = array(
            "timesheet[status]" => $request->type,
            "timesheet[startTime]" => date(Constants::$dateFormat1, $request->startTime),
            "timesheet[endTime]" => date(Constants::$dateFormat1, $request->endTime),
            "timesheet[breakTime]" => $request->breakTime,
            "timesheet[timesheet_comments_attributes][1][comment]" => $request->comment,
            "timesheet[timesheet_comments_attributes][1][timesheet_id]" => $request->timeSheetID,
            "timesheet[timesheet_comments_attributes][1][user_id]" => $request->userID,
            "commrating" => $request->communication,
            "punctrating" => $request->punctuality,
            "initrating" => $request->initiative,
            "authenticity_token" => $request->authToken[0]
        );
        $result = Utils::post($url, $data, $request->cookies, $headers, null, "PUT");
        //print_r ($data);
        return Utils::response($data);
    }

    function cancelTimeSheet(Request $request)
    {
        $url = "https://www.studenttemp.co.uk/timesheets.json";
        $data = array(
            "timesheet[status]" => "E",
            "timesheet[startTime]" => date(Constants::$dateFormat1, $request->startTime),
            "timesheet[endTime]" => date(Constants::$dateFormat1, $request->endTime),
            "timesheet[breakTime]" => $request->breakTime,
            "timesheet[booking_id]" => $request->timeSheetID,
            "timesheet_comments_comment" => $request->comment,
            "authenticity_token" => $request->authToken[0]
        );
        $result = Utils::post($url, $data, $request->cookies);
        //print_r ($data);
        return Utils::response($data);
    }
}
