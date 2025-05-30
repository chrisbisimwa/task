<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\SuiviController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/suivi/{token}', [SuiviController::class, 'show'])->name('suivi.show');
Route::post('/suivi/{token}', [SuiviController::class, 'update']);
Route::post('/suivi/{token}', [SuiviController::class, 'submit'])->name('suivi.submit');
Route::post('/tasks/{id}/update-progress', [SuiviController::class, 'updateProgress'])->name('tasks.updateProgress');



Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::resource('tasks', TaskController::class);
        Route::resource('users', UserController::class);
        Route::resource('access-tokens', AccessTokenController::class);
    });
