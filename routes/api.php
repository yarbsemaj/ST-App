<?php

use App\Http\Middleware\ValidateLogin;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('test/login',"RequestTest@get");
Route::post('test/post',"RequestTest@post");
Route::get('test/get', "RequestTest@post");


Route::post('login',function (Request $request){
    return response(array("success"=>true,"type"=>explode('/',$request->location)[3]));
})->middleware([ValidateLogin::class]);
Route::post('user/get',"User@get")->middleware('jsonRequest');
Route::post('coordinators/get', "User@coordinators")->middleware('jsonRequest');

//Student Routs

Route::post('jobs/accepted',"Jobs@active")->middleware('jsonRequest');
Route::post('jobs/cancelled',"Jobs@cancelled")->middleware('basicRequest');
Route::post('jobs/offered',"Jobs@offered")->middleware('basicRequest');
Route::post('jobs/rejected',"Jobs@rejected")->middleware('basicRequest');
Route::post('jobs/expired',"Jobs@expired")->middleware('basicRequest');

Route::post('timesheet/approved',"TimeSheet@approved")->middleware('basicRequest');
Route::post('timesheet/submitted',"TimeSheet@submitted")->middleware('basicRequest');
Route::post('timesheet/disputed',"TimeSheet@disputed")->middleware('basicRequest');
Route::post('timesheet/tosubmit',"TimeSheet@toSubmit")->middleware('basicRequest');
Route::post('timesheet/cancelled',"TimeSheet@canceled")->middleware('basicRequest');
Route::post('timesheet/submit/approve',"TimeSheet@submitTimeSheet")->middleware('basicRequest');

//Coordinator routs
Route::post('booking/add/form',"Booking@getForm")->middleware('basicRequest');
Route::post('booking/add',"Booking@addBooking")->middleware('basicRequest');
Route::post('booking/get/unfilled',"Booking@getUnfilled")->middleware('basicRequest');
Route::post('booking/get/basic/{type}',"Booking@getBasic")->middleware('basicRequest');

Route::post('booking/cancel',"Booking@cancel")->middleware('basicRequest');
Route::post('booking/cancel/options',"Booking@getCancelOptions")->middleware('jsonRequest');

Route::post('booking/students/get',"Booking@getStudentForBooking")->middleware('basicRequest');
Route::post('booking/students/book',"Booking@bookStudents")->middleware('basicRequest');

Route::post('jobOffers/offered',"JobOffers@offered")->middleware('basicRequest');
Route::post('jobOffers/scheduled',"JobOffers@scheduled")->middleware('basicRequest');
Route::post('jobOffers/rejected',"JobOffers@rejected")->middleware('basicRequest');
Route::post('jobOffers/expired',"JobOffers@expired")->middleware('basicRequest');
Route::post('jobOffers/accepted',"JobOffers@accepted")->middleware('basicRequest');
Route::post('jobOffers/cancelled',"JobOffers@cancelled")->middleware('basicRequest');

Route::post('coordinatorTimesheet/toApprove',"CoordinatorTimeSheet@toApprove")->middleware('basicRequest');
Route::post('coordinatorTimesheet/disputed',"CoordinatorTimeSheet@disputed")->middleware('basicRequest');
Route::post('coordinatorTimesheet/approved',"CoordinatorTimeSheet@approved")->middleware('basicRequest');
Route::post('coordinatorTimesheet/withStudent',"CoordinatorTimeSheet@withStudent")->middleware('basicRequest');
Route::post('coordinatorTimesheet/cancelled',"CoordinatorTimeSheet@cancelled")->middleware('basicRequest');
Route::post('coordinatorTimesheet/warning', "CoordinatorTimeSheet@getWarnings")->middleware('basicRequest');
Route::post('coordinatorTimesheet/admenBooking', "CoordinatorTimeSheet@amendBooking")->middleware('basicRequest');
Route::post('coordinatorTimesheet/admenBookings', "CoordinatorTimeSheet@amendBookings")->middleware('basicRequest');
Route::post('coordinatorTimesheet/cancelTimeSheet', "CoordinatorTimeSheet@cancelTimeSheet")->middleware('basicRequest');

