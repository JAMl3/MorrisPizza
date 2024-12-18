<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DiscountCodeController;

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

Route::middleware([
    'auth:api'
])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::post('discount-codes/validate', [DiscountCodeController::class, 'validateDiscountCode']); 