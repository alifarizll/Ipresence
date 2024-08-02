<?php

use App\Http\Controllers\Api\activitiescontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;


Route::get('/', function () {
    return '.:: Skeleton Services v1.0 ::.';
});

Route::apiResource('tasks', TaskController::class);
Route::apiResource('activities', activitiescontroller::class);
Route::patch('/activities/{id}/status', [ActivitiesController::class, 'updateStatus']);