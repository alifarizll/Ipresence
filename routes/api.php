-- Active: 1721289528824@@127.0.0.1@3306@magang_tasks
<?php

use App\Http\Resources\PostResource;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

//posts


Route::apiResource('/roles', RolesController::class);


