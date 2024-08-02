-- Active: 1721289528824@@127.0.0.1@3306@magang_tasks
<?php

use App\Http\Resources\PostResource;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\activitiescontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Api\TaskController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

//posts


Route::apiResource('/roles', RolesController::class);
Route::apiResource('/users', UsersController::class);





Route::apiResource('tasks', TaskController::class);
Route::apiResource('activities', activitiescontroller::class);
Route::patch('/activities/{id}/status', [ActivitiesController::class, 'updateStatus']);