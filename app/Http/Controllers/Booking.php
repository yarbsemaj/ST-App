<?php

namespace App\Http\Controllers;

use app\Http\Constants;
use app\Http\Utils;
use Illuminate\Http\Request;

class Booking extends Controller
{

    public function getForm(Request $request)
    {
        return Utils::response($this->getBaseFormData($request));
    }

    public function getBaseFormData(Request $request)
    {
        $url = Constants::$url . "/home";
        $response = Utils::get($url, $request->cookies);
        $returnData = array();

        $i = 0;
        $j = 0;

        $fieldNames = array("groupID", "jobTitle", "payType", "rateType", "locationName", "contact", "address", "sessionID", "projectID",
            "poNumber", "authPerson", "glCode", "departmentCode", "costCode", "dressCode", "repeats", "expires");
        foreach (Utils::getElementsByClass($response, "data") as $nodeValue) {

            switch ($i) {
                case 7:
                    $nodeValue = $nodeValue->ownerDocument->saveHTML($nodeValue);
                    $doc = new \DomDocument;
                    $doc->validateOnParse = true;
                    $doc->loadHTML($nodeValue);
                    $nodeValue = $doc->getElementById("job_title_wrapper");
                    $selectedItem = Utils::getSelectedOptions($nodeValue);
                    $allItems = Utils::getAllPayOptions($nodeValue);
                    $itemData = array("options" => $allItems, "selected" => $selectedItem);
                    $returnData[$fieldNames[$j]] = $itemData;
                    $j++;
                    break;
                case 5:
                case 8:
                case 9:
                case 11:
                case 13:
                case 14:
                case 17:
                case 18:
                case 19:
                case 21:
                    $selectedItem = Utils::getSelectedOptions($nodeValue);
                    $allItems = Utils::getAllOptions($nodeValue);
                    $itemData = array("options" => $allItems, "selected" => $selectedItem);
                    $returnData[$fieldNames[$j]] = $itemData;
                    $j++;
                    break;
                case 12:
                case 15:
                    $returnData[$fieldNames[$j]] = Utils::getInputValue($nodeValue)->item(0)->getAttribute('value');
                    $j++;
                    break;
                case 10:
                    $returnData[$fieldNames[$j]] = $nodeValue->nodeValue;
                    $j++;
                    break;
                case 16:
                    preg_match('/\$dialogContent\.find\("#authPerson"\)\.val\("(.*?)"\);/', $response, $matches);
                    $returnData[$fieldNames[$j]] = $matches[1];
                    $j++;
                    break;

            }
            $i++;
        }

        $root = Utils::getElementFromJavaScript('eventSourceOptions: ', $response);

        $selectedItem = Utils::getSelectedOptions($root);
        $allItems = Utils::getAllOptions($root);

        $locations = array();
        foreach ($allItems as $item) {
            if ($item["value"] != "all_locations" && $item["value"] != "my_bookings") {
                $locations[] = $item;
            }
        }
        $itemData = array("options" => $locations, "selected" => $selectedItem);
        $returnData["locationID"] = $itemData;

        $root = Utils::getElementFromJavaScript('bookingReminderDD=bookingReminderDD\+', $response, '\'');

        $selectedItem = Utils::getSelectedOptions($root);
        $allItems = Utils::getAllOptions($root);

        $itemData = array("options" => $allItems, "selected" => $selectedItem);
        $returnData["reminderType"] = $itemData;
        $returnData["searchType"] = true;
        $returnData["post"] = false;
        $returnData["quantity"] = "1";
        $returnData["days"] = "";
        $returnData["start"] = time() + (60 * 60 * 2);
        $returnData["end"] = time() + (60 * 60 * 3);
        $returnData["longDescription"] = "";
        $returnData["shortDescription"] = "";
        $returnData["repeat"] = "0";
        $returnData['expTime'] = "0";
        $returnData['reminderTime'] = "0";
        $returnData["requestStudents"] = true;
        return $returnData;
    }

    public function addBooking(Request $request)
    {
        $methord = "POST";
        $bookingID = $request->bookingID;
        if ($bookingID != "requests") {
            $bookingID = "requests/" . $bookingID;
            $methord = "PUT";
        }
        $url = Constants::$url . "/$bookingID.json";
        $employerID = explode('/', $request->location)[4];

        $startData = date(Constants::$dateFormat1, (int)$request->startTime);
        $endDate = date(Constants::$dateFormat1, (int)$request->endTime);

        $post = array(
            'request[expiryMinutes]' => $request->expieryTime,
            "request[expiryMinutesReminder]" => "undefined",
            "weekdays" => $request->weekDays,
            "gaps" => $request->gaps,
            "request[contract_id]" => "0",
            "request[contact_id]" => $request->contactID,
            "request[pay_type_id]" => $request->payType,
            "request[rate_type_id]" => $request->rateType,
            "request[project_id]" => $request->projectID,
            "request[booking_type_id]" => $request->bookingTypeID,
            "request[cost_code_id]" => $request->costCodeID,
            "request[gl_code_id]" => $request->glCodeID,
            "request[department_code_id]" => $request->departmentCodeID,
            "request[poNumber]" => $request->poNumber,
            "request[authPerson]" => $request->authPerson,
            "request[searchWithPref]" => $request->searchWithPref,
            "request[post_job]" => $request->postJob,
            "request[quantity]" => $request->quantity,
            "request[hourlyChargeRate]" => $request->hourlyChargeRate,
            "request[hourlyPayRate]" => $request->hourlyPayRate,
            "request[jobTitle_id]" => $request->jobTitleID,
            "request[location_id]" => $request->locationID,
            "request[notes]" => $request->shortNotes,
            "request[dressCode_id]" => $request->dressCodeID,
            "request[startTime]" => $startData,
            "repeats" => $request->repeats,
            "request[endTime]" => $endDate,
            "request[booking_reminder_type_id]" => $request->bookingReminderTypeID,
            "request[bookingReminderMinutes]" => $request->bookingReminderMinutes,
            "request[employer_id]" => $employerID,
            "request[student_group_ids]" => $request->studentGroupIDs,
            "request[text_only_details]" => $request->emailInfo,
            "authenticity_token" => $request->authToken[1]
        );

        if ($request->shortPost) {
            $post["request[status]"] = "U";
        }

        $data = Utils::post($url, $post, $request->cookies, $headers, $request->xssToken, $methord);

        $return = array();
        if ($bookingID != "requests") {
            $return['bookingID'] = $request->bookingID;
        } else {
            $data = json_decode($data, true);
            $return['bookingID'] = $data["id"];
        }
        return Utils::response($return);
    }

    public function getUnfilled(Request $request)
    {
        $returnData = array();
        if ($request->page == 1) {
            $data = array('type' => 'unfilled',
                'what' => 'rq',
                'paginate_size' => '1000',
                'page' => '',
                'search' => '',
                'authenticity_token' => $request->authToken[1]);

            $unfilledJobs = Utils::post(Constants::$url . "/showmorerq", $data, $request->cookies);

            $staffingData = array();
            Utils::getList($unfilledJobs, $staffingData);

            $baseFormData = $this->getBaseFormData($request);
            if ($request->endTime == null) $request->endTime = 2147480000;
            $bookingData = Utils::getAvailability(time(), $request->endTime, $request->cookies, "/requests.json?lc=all_locations&jt=-1&");
            $element = $baseFormData;
            foreach ($staffingData as $staff) {
                $id = trim(preg_replace('/\s\s+/', '', $staff->item(0)->nodeValue));
                $element['required'] = $staff->item(5)->nodeValue;
                $element['required'] = $staff->item(5)->nodeValue;
                $element['remaining'] = $staff->item(6)->nodeValue;
                $element["requestStudents"] = true;
                foreach ($bookingData['events'] as $booking) {
                    if ($booking['id'] == $id && !$booking['isChild']) {
                        if ($booking['student_group_ids'] != "false") {
                            $element['groupID']['selected'] = array();
                        }
                        $element['title'] = $booking["title"];
                        $element['jobTitle']['selected'] = array(array("value" => $booking["jobTitleId"]));
                        $element['payType']['selected'] = array(array("value" => $booking["rateTypeTitle"]));
                        $element['rateType']['selected'] = array(array("value" => $booking["eRateType_id"]));
                        $element['locationName'] = $booking['locationName'];
                        $element['contact']['selected'] = array(array("value" => $booking["contact_id"]));
                        $element['sessionID']['selected'] = array(array("value" => $booking["session_id"]));
                        $element['projectID']['selected'] = array(array("value" => $booking["project_id"]));
                        $element['poNumber'] = $booking['poNumber'];
                        $element['authPerson'] = $booking['authPerson'];
                        $element['departmentCode']['selected'] = array(array("value" => $booking["departmentCode_id"]));
                        $element['costCode']['selected'] = array(array("value" => $booking["costCode_id"]));
                        $element['dressCode']['selected'] = array(array("value" => $booking["dressCodeId"]));
                        $element['locationID']['selected'] = array(array("value" => $booking["locationId"]));
                        $element['reminderType']['selected'] = array(array("value" => $booking["bookingReminder"]));
                        $element['searchType'] = $booking['searchWithPref'];
                        $element['post'] = $booking['postJob'];
                        $element['quantity'] = $booking['quantity'];
                        $element['days'] = $booking['repeatedWeekDays'];
                        $element['longDescription'] = $booking['text_only_details'];
                        $element['shortDescription'] = $booking['notes'];
                        $element['repeat'] = $booking['repeatedWeeks'];
                        $element['bookingID'] = $booking['id'];

                        $element['start'] = Utils::getDateFromCal($booking['start']);
                        $element['end'] = Utils::getDateFromCal($booking['end']);
                        $element['editable'] = $booking['status'] == "U";
                        $element['status'] = $booking['status'];
                        $element['expTime'] = (string)$booking['expiryMins'];
                        $element['reminderTime'] = $booking['bookingReminderMinutes'];
                        $returnData[] = $element;
                        break;
                    }
                }
            }
            usort($returnData, Utils::build_sorter('start'));
        }
        return Utils::response($returnData);
    }

    public function getBasic(Request $request, $type)
    {
        $returnData = array();
        if ($request->page == 1) {
            $data = array('type' => 'unfilled',
                'what' => 'rq',
                'paginate_size' => '1000',
                'page' => '',
                'search' => '',
                'authenticity_token' => $request->authToken[1]);

            $unfilledJobs = Utils::post(Constants::$url . "/showmorerq", $data, $request->cookies);

            $staffingData = array();
            Utils::getList($unfilledJobs, $staffingData);

            $baseFormData = $this->getBaseFormData($request);
            if ($request->endTime == null) $request->endTime = 2147480000;
            $bookingData = Utils::getAvailability(time(), $request->endTime, $request->cookies, "/requests.json?lc=all_locations&jt=-1&");
            $element = $baseFormData;
            foreach ($bookingData['events'] as $booking) {
                if ($booking['status'] == $type && !$booking['isChild']) {
                    $element["requestStudents"] = false;
                    foreach ($staffingData as $staff) {
                        $id = trim(preg_replace('/\s\s+/', '', $staff->item(0)->nodeValue));
                        if ($id == $booking['id']) {
                            $element['required'] = $staff->item(5)->nodeValue;
                            $element['remaining'] = $staff->item(6)->nodeValue;
                            $element["requestStudents"] = true;
                            break;
                        }
                    }
                    if ($booking['student_group_ids'] != "false") {
                        $element['groupID']['selected'] = array();
                    }
                    $element['title'] = $booking["title"];
                    $element['jobTitle']['selected'] = array(array("value" => $booking["jobTitleId"]));
                    $element['payType']['selected'] = array(array("value" => $booking["rateTypeTitle"]));
                    $element['rateType']['selected'] = array(array("value" => $booking["eRateType_id"]));
                    $element['locationName'] = $booking['locationName'];
                    $element['contact']['selected'] = array(array("value" => $booking["contact_id"]));
                    $element['sessionID']['selected'] = array(array("value" => $booking["session_id"]));
                    $element['projectID']['selected'] = array(array("value" => $booking["project_id"]));
                    $element['poNumber'] = $booking['poNumber'];
                    $element['authPerson'] = $booking['authPerson'];
                    $element['departmentCode']['selected'] = array(array("value" => $booking["departmentCode_id"]));
                    $element['costCode']['selected'] = array(array("value" => $booking["costCode_id"]));
                    $element['dressCode']['selected'] = array(array("value" => $booking["dressCodeId"]));
                    $element['locationID']['selected'] = array(array("value" => $booking["locationId"]));
                    $element['reminderType']['selected'] = array(array("value" => $booking["bookingReminder"]));
                    $element['searchType'] = $booking['searchWithPref'];
                    $element['post'] = $booking['postJob'];
                    $element['quantity'] = $booking['quantity'];
                    $element['days'] = $booking['repeatedWeekDays'];
                    $element['longDescription'] = $booking['text_only_details'];
                    $element['shortDescription'] = $booking['notes'];
                    $element['repeat'] = $booking['repeatedWeeks'];
                    $element['bookingID'] = $booking['id'];
                    $element['small_element'] = true;
                    $element['start'] = Utils::getDateFromCal($booking['start']);
                    $element['end'] = Utils::getDateFromCal($booking['end']);
                    $element['editable'] = $booking['status'] == "U";
                    $element['status'] = $booking['status'];
                    $element['expTime'] = (string)$booking['expiryMins'];
                    $element['reminderTime'] = $booking['bookingReminderMinutes'];
                    $returnData[] = $element;
                }
            }
        }
        usort($returnData, Utils::build_sorter('start'));
        return Utils::response($returnData);
    }

    public function getStudentForBooking(Request $request)
    {
        $authToken = $request->authToken[0];
        $bookingID = $request->bookingID;
        $quantity = $request->quantity;

        $data = array('type' => 'default',
            '_status' => 'U',
            'authenticity_token' => $authToken,
            'request_id' => $bookingID);

        $data = Utils::buildQuery($data);

        $extras = array();

        $extras[] = $this->makeExtra(1, 1, 3, 5);
        $extras[] = $this->makeExtra(2, 4, 6, 10);
        $extras[] = $this->makeExtra(3, 7, 10, 15);
        $extras[] = $this->makeExtra(4, 11, 250, 20);

        $url = "/matches.json?$data";

        $students = json_decode(Utils::get(Constants::$url . $url, $request->cookies), true);

        $studentsReturn = array();
        foreach ($students['list'] as $student) {
            $studentData = $student['student'];

            $student['student'] = null;

            $student = array_merge($student, $studentData);


            if ($student['photo'][0] == "/") {
                $student['photo'] = Constants::$url . $student['photo'];
            }
            $student['selected'] = false;
            $studentsReturn[] = $student;
            $students['maxStudents'] = $this->getMaxStudents($quantity, $student['totalJobsLeft'], $extras);
        }

        $students['list'] = $studentsReturn;
        $students['schedule'][0]["schedule_can_before"] = strtotime($students['schedule'][0]["schedule_can_before"]);
        return Utils::response($students);

    }

    public function makeExtra($intervalNo, $from, $to, $noOfExtras)
    {

        return array('intervalNo' => $intervalNo,
            'from' => $from,
            'to' => $to,
            'noOfExtras' => $noOfExtras);
    }

    public function getMaxStudents($quantity, $taken, $extras)
    {
        if (!isset($quantity) || !isset($taken) || !isset($extras))
            return 0;
        $quantity = $quantity - $taken;
        for ($s = 0; $s < count($extras); $s++)
            if ($quantity >= $extras[$s]['from'] && $quantity <= $extras[$s]['to'])
                return $quantity + $extras[$s]['noOfExtras'];
        return $quantity + $extras[count($extras) - 1]['noOfExtras'];
    }

    public function cancel(Request $request)
    {
        $data = array();
        if (isset($request->reasonID)) {
            $data['reason_id'] = $request->reasonID;
            $data['ct'] = "CRAW";
        }
        $bookingID = $request->bookingID;
        $url = Constants::$url . "/requests/destroy/$bookingID.json";
        print Utils::post($url, $data, $request->cookies, $headers, $request->xssToken);

        return Utils::response(null);
    }

    public function getCancelOptions(Request $request)
    {
        $url = Constants::$url . "/home";
        $response = Utils::get($url, $request->cookies);
        $reasonsDropDown = Utils::getElementsByID($response, "cancellation_reason_id")->item(0);
        $selectedItem = Utils::getSelectedOptions($reasonsDropDown);
        $allItems = Utils::getAllOptions($reasonsDropDown);

        $itemData = array("options" => $allItems, "selected" => $selectedItem);
        return Utils::response($itemData);
    }

    public function bookStudents(Request $request)
    {
        $url = Constants::$url . "/bookings.json";
        $data = json_decode($request->data, true);

        foreach ($data as $datum) {
            $prams = array("offset" => $datum['shiftOffset'],
                "choice" => '1',
                "authenticity_token" => $request->authToken[1],
                "booking[startTime]" => $datum['startTime'],
                "booking[endTime]" => $datum['endTime'],
                "booking[status]" => "O",
                "booking[availability_id]" => $datum['availability_id'],
                "booking[request_id]" => $datum['request_id'],
                "booking[student_id]" => $datum['student_id'],
                "booking[pay_grade_title_id]" => $datum['payGradeTitleId'],
                "_status" => "U");
            if (isset($request->time)) {
                $prams["scheduled_at"] = date("d-m-Y H:i", $request->time);
            }

            Utils::post($url, $prams, $request->cookies);

        }
        return Utils::response(null);
    }

}
