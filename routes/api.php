<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\ResumeMatchController;

Route::post('resume-match', [ResumeMatchController::class, 'analyze']);

Route::apiResource('job-applications', JobApplicationController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
