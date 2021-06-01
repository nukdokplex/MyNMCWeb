<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\AuditoriesController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SpecializationsController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Auth::routes();

Route::get('news/{page}', [App\Http\Controllers\NewsController::class, 'index'])->name("news.page")
    ->where(["page" => "[0-9]+"]);

Route::get('news', [NewsController::class, 'index'])
    ->name("news");

//Posts
Route::get('posts', [function(){
    return view('posts.index');
}])->name('posts');

Route::get('schedule/', [ScheduleController::class, 'index'])
    ->name('schedule');

Route::get('schedule/{model_type}', [ScheduleController::class, 'models'])
    ->name('schedule.models')
    ->where(['model_type' => 'group|teacher|auditory']);

Route::get('schedule/{model_type}/{model_id}', [ScheduleController::class, 'schedule'])
    ->name('schedule.model')
    ->where(['model_type' => 'group|teacher|auditory'])
    ->where(["model_id" => "[0-9]+"]);

//Mobile

Route::get('mobile_login', [\App\Http\Controllers\Auth\MobileLoginController::class, 'login']);

Route::group(['middleware' => 'can:manage groups'], function () {
    Route::get('groups', [GroupsController::class, "index"])
        ->name('groups');

    Route::get('groups/{id}/users', [GroupsController::class, "index"])
        ->name('group.users')
        ->where(["id" => "[0-9]+"]);

    Route::get("ajax/groups", [GroupsController::class, 'groups_ajax'])
        ->name('ajax.groups');

    Route::get("ajax/groups/{group}/users", [GroupsController::class, 'group_users_ajax'])
        ->name('ajax.group.users')
        ->where(["group" => "[0-9]+"]);

    Route::put("ajax/groups", [GroupsController::class, 'create_group_ajax'])
        ->name('ajax.groups.put');

    Route::post("ajax/groups", [GroupsController::class, 'update_group_ajax'])
        ->name('ajax.groups.post');

    Route::delete("ajax/groups", [GroupsController::class, 'delete_group_ajax'])
        ->name('ajax.groups.delete');

    Route::post("ajax/groups/{group}/users", [GroupsController::class, 'update_group_users_ajax'])
        ->name('ajax.group.users.post')
        ->where(["group" => "[0-9]+"]);
});

Route::group(['middleware' => 'can:manage specializations'], function () {
    Route::get('specializations', [SpecializationsController::class, 'index'])
        ->name('specializations');

    Route::get('specializations/{specialization}/groups', [SpecializationsController::class, 'index'])
        ->name('specialization.groups')
        ->where(["specialization" => "[0-9]+"]);

    Route::get('ajax/specializations', [SpecializationsController::class, 'specializations_ajax'])
        ->name('ajax.specializations');

    Route::get('ajax/specializations/{specialization}', [SpecializationsController::class, 'specialization_ajax'])
        ->name('ajax.specialization')
        ->where(["specialization" => "[0-9]+"]);

    Route::get('ajax/specializations/{specialization}/groups', [SpecializationsController::class, 'specialization_groups_ajax'])
        ->name('ajax.specialization.groups')
        ->where(["specialization" => "[0-9]+"]);

    Route::put('ajax/specializations', [SpecializationsController::class, 'create_specialization_ajax'])
        ->name('ajax.specializations.put');

    Route::post('ajax/specializations', [SpecializationsController::class, 'update_specialization_ajax'])
        ->name('ajax.specializations.post');

    Route::delete('ajax/specializations', [SpecializationsController::class, 'delete_specialization_ajax'])
        ->name('ajax.specializations.delete');

    Route::post('ajax/specializations/{specialization}/groups', [SpecializationsController::class, 'update_specialization_groups_ajax'])
        ->name('ajax.specialization.groups.post')
        ->where(["specialization" => "[0-9]+"]);
});

Route::group(['middleware' => 'can:manage subjects'], function () {
    Route::get('subjects', [SubjectsController::class, 'index'])
        ->name('subjects');

    Route::get('ajax/subjects', [SubjectsController::class, 'subjects_ajax'])
        ->name('ajax.subjects');

    Route::put('ajax/subjects', [SubjectsController::class, 'create_subject_ajax'])
        ->name('ajax.subjects.create');

    Route::delete('ajax/subjects', [SubjectsController::class, 'delete_subject_ajax'])
        ->name('ajax.subjects.delete');

    Route::post('ajax/subjects', [SubjectsController::class, 'update_subject_ajax'])
        ->name('ajax.subjects.update');
});

Route::group(['middleware' => 'can:manage auditories'], function () {
    Route::get('auditories', [AuditoriesController::class, 'index'])
        ->name('auditories');

    Route::get('ajax/auditories', [AuditoriesController::class, 'auditories_ajax'])
        ->name('ajax.auditories');

    Route::put('ajax/auditories', [AuditoriesController::class, 'create_auditory_ajax'])
        ->name('ajax.auditories.create');

    Route::post('ajax/auditories', [AuditoriesController::class, 'update_auditory_ajax'])
        ->name('ajax.auditories.update');

    Route::delete('ajax/auditories', [AuditoriesController::class, 'delete_auditory_ajax'])
        ->name('ajax.auditories.delete');
});

Route::group(['middleware' => 'can:manage users'], function () {
    Route::get('users', [UserController::class, 'index'])
        ->name('users');

    Route::get('ajax/users', [UserController::class, 'users_ajax'])
        ->name('ajax.users');

    Route::get('ajax/users/detailed', [UserController::class, 'users_detailed_ajax'])
        ->name('ajax.users.detailed');

    Route::get('ajax/users/by_role', [UserController::class, 'users_by_role_ajax'])
        ->name('ajax.users.by_role');

    Route::put('ajax/users', [UserController::class, 'users_create_ajax'])
        ->name('ajax.users.create');

    Route::delete('ajax/users', [UserController::class, 'user_delete_ajax'])
        ->name('ajax.users.delete');

    Route::post('ajax/users', [UserController::class, 'user_update_ajax'])
        ->name('ajax.users.update');
});

Route::group(['middleware' => 'can:manage schedule'], function () {
    //Schedule Edit
    Route::get('schedule/edit', [ScheduleController::class, 'edit_index'])
        ->name('schedule.edit');

    Route::get('schedule/edit/{model}/{id}/{week}', [ScheduleController::class, 'edit_schedule'])
        ->name('schedule.edit.edit')
        ->where(["model" => "group|teacher|auditory"])
        ->where(["id" => "[0-9]+"])
        ->where(["week" => "[0-9]+"]);

    Route::get('ajax/schedule/edit', [ScheduleController::class, 'ajax_current_schedule'])
        ->name('ajax.schedule.edit');

    Route::get('ajax/schedule/edit', [ScheduleController::class, 'ajax_current_schedule'])
        ->name('ajax.schedule.edit');

    Route::put('ajax/schedule/edit', [ScheduleController::class, 'ajax_current_schedule_create'])
        ->name('ajax.schedule.edit.create');

    Route::post('ajax/schedule/edit', [ScheduleController::class, 'ajax_current_schedule_edit'])
        ->name('ajax.schedule.edit.update');

    Route::delete('ajax/schedule/edit', [ScheduleController::class, 'ajax_current_schedule_delete'])
        ->name('ajax.schedule.edit.delete');

    Route::post('ajax/schedule/edit/synchronize', [ScheduleController::class, 'ajax_sync_schedule'])
        ->name('ajax.schedule.edit.sync');

//Primary Schedule
    Route::get('schedule/edit/primary', [ScheduleController::class, 'primary_schedule_groups'])
        ->name('schedule.edit.primary.groups');

    Route::get('schedule/edit/primary/{group}/{week_number}/{day_of_week}', [ScheduleController::class, 'primary_schedule'])
        ->name('schedule.edit.primary')
        ->where('week_number', 'odd|even')
        ->where('day_of_week', 'mon|tue|wed|thu|fri|sat|sun');

    Route::post('schedule/edit/primary/{group}/{week_number}/{day_of_week}', [ScheduleController::class, 'edit_primary_schedule_ajax'])
        ->name('ajax.schedule.edit.primary.update')
        ->where('week_number', 'odd|even')
        ->where('day_of_week', 'mon|tue|wed|thu|fri|sat|sun');

//Rings Schedule
    Route::get('schedule/edit/rings', [ScheduleController::class, 'rings_edit'])
        ->name('schedule.edit.rings');

    Route::get('schedule/edit/rings/{rings}', [ScheduleController::class, 'rings_edit'])
        ->name('schedule.edit.ring')
        ->where(["rings" => "[0-9]+"]);

    Route::get('ajax/schedule/rings', [ScheduleController::class, 'rings_ajax'])
        ->name('ajax.schedule.rings');

    Route::get('ajax/schedule/rings/{rings}/groups', [ScheduleController::class, 'rings_groups_ajax'])
        ->name('ajax.schedule.rings.groups')
        ->where(["rings" => "[0-9]+"]);

    Route::put('ajax/schedule/rings/', [ScheduleController::class, 'create_rings_ajax'])
        ->name('ajax.schedule.rings.create');

    Route::delete('ajax/schedule/rings/{rings}', [ScheduleController::class, 'delete_rings_ajax'])
        ->name('ajax.schedule.rings.delete')
        ->where(["rings" => "[0-9]+"]);

    Route::post('ajax/schedule/rings/{rings}', [ScheduleController::class, 'update_rings_ajax'])
        ->name('ajax.schedule.rings.update')
        ->where(["rings" => "[0-9]+"]);
});

Route::group(['middleware' => 'can:manage app settings'], function () {

});



Route::group(['middleware' => 'auth'], function () {
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
});

