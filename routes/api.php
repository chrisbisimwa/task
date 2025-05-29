<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AccessTokenController;
use App\Http\Controllers\Api\EmployeeTasksController;
use App\Http\Controllers\Api\EmployeeAccessTokensController;

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

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');

Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('employees', EmployeeController::class);

        // Employee Tasks
        Route::get('/employees/{employee}/tasks', [
            EmployeeTasksController::class,
            'index',
        ])->name('employees.tasks.index');
        Route::post('/employees/{employee}/tasks', [
            EmployeeTasksController::class,
            'store',
        ])->name('employees.tasks.store');

        // Employee Access Tokens
        Route::get('/employees/{employee}/access-tokens', [
            EmployeeAccessTokensController::class,
            'index',
        ])->name('employees.access-tokens.index');
        Route::post('/employees/{employee}/access-tokens', [
            EmployeeAccessTokensController::class,
            'store',
        ])->name('employees.access-tokens.store');

        Route::apiResource('tasks', TaskController::class);

        Route::apiResource('users', UserController::class);

        Route::apiResource('access-tokens', AccessTokenController::class);
    });
