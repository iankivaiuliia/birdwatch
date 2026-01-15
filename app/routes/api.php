<?php

use App\Http\Controllers\Api\BirdCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
Route::apiResource('categories', BirdCategoryController::class);
