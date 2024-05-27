<?php

use App\Http\Controllers\AsignTaskController;

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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/settings',[SettingsController::class, 'index'])->name('settings');

    Route::resource('user', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permission', PermissionController::class);

    Route::resource('tasks', TaskController::class);
    Route::resource('asign_tasks', AsignTaskController::class);
    Route::resource('project_title', ProjectTitleController::class);
    Route::resource('report', ReportController::class);
    Route::resource('project_title', ProjectTitleController::class);

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/extend', [TaskController::class, 'extend'])->name('tasks.extend');
    Route::patch('/tasks/{task}/redo', [TaskController::class, 'redo'])->name('tasks.redo');
    Route::patch('/tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('tasks.cancel');

 

    Route::patch('/tasks/{task}/completed', [AsignTaskController::class, 'completed'])->name('asign_tasks.complete');
    Route::patch('/tasks/{task}/pendingdate', [AsignTaskController::class, 'pendingdate'])->name('asign_tasks.pendingdate');
    Route::patch('/tasks/{task}/requested', [AsignTaskController::class, 'requested'])->name('asign_tasks.requested');
    Route::patch('/tasks/{task}/incomplete', [AsignTaskController::class, 'incomplete'])->name('asign_tasks.incomplete');

    Route::post('/report/create', [ReportController::class, 'create'])->name('report.create');

    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

});
