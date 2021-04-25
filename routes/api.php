<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationsController;

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

Route::middleware('auth:api')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/invitations/{id}', [InvitationsController::class, 'show']);
    Route::post('/invitations', [InvitationsController::class, 'create']);
    Route::post('/invitations/{id}/cancel', [InvitationsController::class, 'cancel']);
    Route::post('/invitations/{id}/accept', [InvitationsController::class, 'accept']);
    Route::post('/invitations/{id}/reject', [InvitationsController::class, 'reject']);

});

