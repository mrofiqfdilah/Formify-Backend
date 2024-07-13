<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// 1.	Authentication Endpoint

Route::post('v1/auth/login', [AuthController::class, 'login']);

Route::post('v1/auth/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);

// 2.   Forms Endpoint

Route::post('v1/forms', [FormController::class, 'create_form'])->middleware(['auth:sanctum']);

Route::get('v1/forms', [FormController::class, 'all_form'])->middleware(['auth:sanctum']);

Route::get('v1/forms/{slug}', [FormController::class, 'detail_form'])->middleware(['auth:sanctum']);

// 3.   Questions Endpoint

Route::post('v1/forms/{form_slug}/questions', [QuestionController::class, 'add_question'])->middleware(['auth:sanctum']);

Route::delete('v1/forms/{form_slug}/questions/{id}', [QuestionController::class, 'remove_question'])->middleware(['auth:sanctum']);

// 4.   Answers Endpoint

Route::post('v1/forms/{slug}/responses', [ResponseController::class, 'submit_response'])->middleware(['auth:sanctum']);

Route::get('v1/forms/{slug}/responses', [ResponseController::class, 'all_response'])->middleware(['auth:sanctum']);