<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/jobs', [JobController::class, 'create']);
Route::get('/jobs', [JobController::class, 'getAllJobs']);
Route::get('/jobs/{id}', [JobController::class, 'getJobDetails']);

Route::delete('/jobs/{id}', [JobController::class, 'deleteJob']);
