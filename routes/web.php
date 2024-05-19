<?php

use App\Http\Controllers\AsignTaskController;
use App\Http\Controllers\ProjectTitleController;
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

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);

    Route::resource('tasks', TaskController::class);
    Route::resource('asign_tasks', AsignTaskController::class);

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/extend', [TaskController::class, 'extend'])->name('tasks.extend');
    
    Route::patch('/tasks/{task}/incomplete', [AsignTaskController::class, 'incomplete'])->name('asign_tasks.incomplete');

    Route::resource('project_title', ProjectTitleController::class);


});
