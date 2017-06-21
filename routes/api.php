<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckSTLogin;
use App\Http\Middleware\CheckAlreadyRegistered;
use App\Http\Middleware\ValidateLogin;
use App\Http\Middleware\GetCookieString;
use App\Http\Middleware\GetAuthToken;

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

Route::post('login',function (Request $request){
    return response(array("success"=>true,"type"=>$request->type));
})->middleware([ValidateLogin::class]);

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

Route::post('user/get',"User@get")->middleware('jsonRequest');

Route::post('test/login',"RequestTest@get");

Route::post('timesheet/submit/approve',"TimeSheet@submitTimeSheet")->middleware('basicRequest');