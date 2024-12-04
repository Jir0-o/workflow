<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AsignTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginDetailsController;
use App\Http\Controllers\ManageWorkController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectTitleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPlanController;
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
    Route::resource('notice', NoticeController::class);
    Route::resource('work_plan', WorkPlanController::class);
    Route::resource('manage_work', ManageWorkController::class);
    Route::resource('application', ApplicationController::class);


    Route::get('details_login' ,[LoginDetailsController::class, 'detailsLogin'])->name('details_login.edit');
    Route::PUT('details_login' ,[LoginDetailsController::class, 'detailsLoginUpdate'])->name('details_login.update');
    Route::post('/login/report/create', [LoginDetailsController::class, 'loginReport'])->name('loginReport.report');
    Route::get('/login/report/view', [LoginDetailsController::class, 'loginReportView'])->name('loginReport.view');

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::put('/tasks/{task}/extend', [TaskController::class, 'extend'])->name('tasks.extend');
    Route::patch('/tasks/{task}/redo', [TaskController::class, 'redo'])->name('tasks.redo');
    Route::patch('/tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('tasks.cancel');

    //workplan route
    Route::patch('/work_plan/{task}/complete', [WorkPlanController::class, 'complete'])->name('work_plan.complete');
    Route::put('/work_plan/{task}/extend', [WorkPlanController::class, 'extend'])->name('work_plan.extend');
    Route::patch('/work_plan/{task}/redo', [WorkPlanController::class, 'redo'])->name('work_plan.redo');
    Route::patch('/work_plan/{task}/cancel', [WorkPlanController::class, 'cancel'])->name('work_plan.cancel');
    Route::get('/work_plan/{task}/show', [WorkPlanController::class, 'getTask'])->name('work_plan.show');

    //manage work route
    Route::patch('/manage_work/{task}/completed', [ManageWorkController::class, 'completed'])->name('manage_work.complete');
    Route::patch('/manage_work/{task}/pendingdate', [ManageWorkController::class, 'pendingdate'])->name('manage_work.pendingdate');
    Route::patch('/manage_work/{task}/requested', [ManageWorkController::class, 'requested'])->name('manage_work.requested');
    Route::patch('/manage_work/{task}/incomplete', [ManageWorkController::class, 'incomplete'])->name('manage_work.incomplete');

    
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

    //Notification Route
    Route::get('/notifications/count', [NotificationController::class, 'notificationCount'])->name('notifications.count');
    // In your routes/web.php
    Route::delete('/notifications/delete/{id}', [NotificationController::class, 'deleteNotification'])->name('notifications.delete');
    Route::post('/notifications/clear', [NotificationController::class, 'clearNotifications'])->name('notifications.clear');
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::post('/activity', [ActivityController::class, 'store']);

    Route::post('/update-nev-login-time', [ActivityController::class, 'updateNeverLoginTime'])->name('updateNevLoginTime');
    Route::post('/update-login-time', [ActivityController::class, 'updateLoginTime'])->name('updateLoginTime');
    Route::get('/getActiveSession/{id}', [ActivityController::class, 'getAllActiveSessions'])->name('get.login.hour');
    Route::post('/update-logout-time', [ActivityController::class, 'updateLogoutTime'])->name('updateLogoutTime');


    //notice route
    Route::PATCH('/notice/{notice}/end', [NoticeController::class, 'noticeEnd'])->name('notice.end');
    Route::PATCH('/notice/{notice}/start', [NoticeController::class, 'noticeStart'])->name('notice.start');

    //application route
    Route::post('/applications/{id}/accept', [ApplicationController::class, 'accept'])->name('application.accept');
    Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject'])->name('application.reject');
    Route::delete('/applications/{id}/cancel', [ApplicationController::class, 'cancel'])->name('application.cancel');
    Route::post('/application/{id}/return', [ApplicationController::class, 'return'])->name('application.return');
    Route::post('/application/{id}/send', [ApplicationController::class, 'send'])->name('application.send');

    
});
