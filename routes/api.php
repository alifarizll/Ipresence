<?php

use App\Http\Resources\PostResource;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\activitiescontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

//posts


Route::apiResource('/roles', RolesController::class); 
Route::apiResource('/users', UsersController::class); //ini bisa buat nambah foto user , bisa juga hapus user
Route::post('/createUser', [UsersController::class, 'createUser']);   // ini untuk menambahkan user
Route::apiResource('tasks', TaskController::class);
Route::apiResource('activities', activitiescontroller::class);  // ini untuk menambahkan aktivitas
Route::patch('/activities/{id}/status', [ActivitiesController::class, 'updateStatus']);
Route::post('/activities/{id}/updatetask', [ActivitiesController::class, 'updatetask']);  //ini untuk update aktivitas



Route::get('/activities/{id}/showtable', [ActivitiesController::class, 'showtable']); // table aktivitas halaman user
Route::get('/activities/{id}/showtableadmin', [ActivitiesController::class, 'showtableadmin']); // table aktivitas halaman admin

Route::post('/loginUser', [AuthController::class, 'loginUser']);  //ini untuk login






