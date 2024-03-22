<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\createConversation;



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


Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::get('/getchats', [createConversation::class, 'getConversations']);
    Route::post('/createconversation/{id}', [createConversation::class, 'createConversation']);
    Route::get('/searchuser/{query}', [createConversation::class, 'searchUser']);
    Route::post('/sendmessage', [createConversation::class, 'sendMessage']);
    Route::get('/showchat', [createConversation::class, 'showChat']);
    Route::post('/logout', [authController::class, 'logoutUser']);



});

Route::post('/login', [authController::class, 'loginUser']);
Route::post('/register', [authController::class, 'registerUser']);