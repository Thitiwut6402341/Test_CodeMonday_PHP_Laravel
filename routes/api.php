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


Route::prefix('category')->controller(CategoryController::class)->group(function () {
    Route::post('/create-standalone', 'createCategoryStandAlone');
    Route::post('/create-leaf', 'createSubCategory');
    Route::get('/get-stand-alone', 'getStanAloneCategory');
    Route::get('/get-tree', 'getTreeCategory');
    Route::get('/get-all', 'getAllMainCategory');
    Route::get('/get-array', 'getArrayCategory');
});


Route::delete('/delete/category', [CategoryController::class, 'deleteCategory']);
