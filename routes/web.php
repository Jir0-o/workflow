<?php

use App\Http\Controllers\AsignTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginDetailsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectTitleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::resource('dashboard', DashboardController::class);
    Route::resource('user', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('asign_tasks', AsignTaskController::class);
    Route::resource('project_title', ProjectTitleController::class);
    Route::resource('report', ReportController::class);
    Route::resource('login_details', LoginDetailsController::class);


    Route::get('details_login' ,[LoginDetailsController::class, 'detailsLogin'])->name('details_login.edit');
    Route::PUT('details_login' ,[LoginDetailsController::class, 'detailsLoginUpdate'])->name('details_login.update');
    Route::post('/login/report/create', [LoginDetailsController::class, 'loginReport'])->name('loginReport.report');
    Route::get('/login/report/view', [LoginDetailsController::class, 'loginReportView'])->name('loginReport.view');

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/extend', [TaskController::class, 'extend'])->name('tasks.extend');
    Route::patch('/tasks/{task}/redo', [TaskController::class, 'redo'])->name('tasks.redo');
    Route::patch('/tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('tasks.cancel');


    Route::patch('/project_title/{project_title}/complete', [ProjectTitleController::class, 'complete'])->name('project.complete');
    Route::patch('/project_title/{project_title}/drop', [ProjectTitleController::class, 'drop'])->name('project.drop');
    Route::patch('/project_title/{project_title}/running', [ProjectTitleController::class, 'running'])->name('project.running');
    Route::get('/project_title/{project_title}/edit', [ProjectTitleController::class, 'newEdit'])->name('edit.project_title');
 

    Route::patch('/tasks/{task}/completed', [AsignTaskController::class, 'completed'])->name('asign_tasks.complete');
    Route::patch('/tasks/{task}/pendingdate', [AsignTaskController::class, 'pendingdate'])->name('asign_tasks.pendingdate');
    Route::patch('/tasks/{task}/requested', [AsignTaskController::class, 'requested'])->name('asign_tasks.requested');
    Route::patch('/tasks/{task}/incomplete', [AsignTaskController::class, 'incomplete'])->name('asign_tasks.incomplete');

    Route::post('/report/create', [ReportController::class, 'create'])->name('report.create');
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

    Route::get('/settings',[SettingsController::class, 'index'])->name('settings');

});
