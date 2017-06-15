<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckSTLogin;
use App\Http\Middleware\CheckAlreadyRegistered;
use App\Http\Middleware\ValidateLogin;

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

Route::post('login',function (){
    return response(array("success"=>true));
})->middleware([ValidateLogin::class]);

Route::post('jobs/accepted',"Jobs@active")->middleware([ValidateLogin::class]);
Route::post('jobs/cancelled',"Jobs@cancelled")->middleware([ValidateLogin::class]);


Route::post('jobs/offered',"Jobs@offered")->middleware([ValidateLogin::class]);
Route::post('jobs/rejected',"Jobs@rejected")->middleware([ValidateLogin::class]);
Route::post('jobs/expired',"Jobs@expired")->middleware([ValidateLogin::class]);

Route::post('timesheet/approved',"TimeSheet@approved")->middleware([ValidateLogin::class]);
Route::post('timesheet/submitted',"TimeSheet@submitted")->middleware([ValidateLogin::class]);
Route::post('timesheet/disputed',"TimeSheet@disputed")->middleware([ValidateLogin::class]);
Route::post('timesheet/tosubmit',"TimeSheet@toSubmit")->middleware([ValidateLogin::class]);
Route::post('timesheet/cancelled',"TimeSheet@canceled")->middleware([ValidateLogin::class]);

Route::post('user/get',"User@get")->middleware([ValidateLogin::class]);

Route::post('test/login',"RequestTest@get");


Route::post('timesheet/submit/approve',"TimeSheet@submitTimeSheet")->middleware([ValidateLogin::class]);