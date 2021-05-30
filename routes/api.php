<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\AuditoriesController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::group([
    'middleware' => [''],
], function ($router) {
    Route::get('me', [UserController::class, 'api_userinfo']);
});

Route::get('schedule/{model}/{model_id}', [ScheduleController::class, 'api_schedule'])
    ->where(['model' => 'group|teacher|auditory'])
    ->where(["model_id" => "[0-9]+"]);;

Route::get('teachers', [UserController::class, 'api_teachers']);

Route::get('groups', [GroupsController::class, 'api_groups']);

Route::get('auditories', [AuditoriesController::class, 'api_auditories']);
