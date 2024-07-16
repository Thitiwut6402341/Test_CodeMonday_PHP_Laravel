<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
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

Route::get('/', function () {
    return response()->json(["message" => "Welcome to test Laravel"]);
});


Route::prefix('main-category')->controller(CategoryController::class)->group(function () {
    Route::post('/create-standalone', 'createCategoryStandAlone');
    Route::get('/get', 'getMainCategory');
    Route::get('/get-all', 'getAllMainCategory');
});


Route::prefix('sub-category')->controller(CategoryController::class)->group(function () {
    Route::post('/create-leaf', 'createSubCategory');
});


Route::delete('/delete/category', [CategoryController::class, 'deleteCategory']);
