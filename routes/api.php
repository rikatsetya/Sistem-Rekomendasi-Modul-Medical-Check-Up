<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\SubCategoryValuesController;
use App\Http\Controllers\Api\CategorySubCategoriesController;

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

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');

Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::apiResource('categories', CategoryController::class);

        // Category Sub Categories
        Route::get('/categories/{category}/sub-categories', [
            CategorySubCategoriesController::class,
            'index',
        ])->name('categories.sub-categories.index');
        Route::post('/categories/{category}/sub-categories', [
            CategorySubCategoriesController::class,
            'store',
        ])->name('categories.sub-categories.store');

        Route::apiResource('sub-categories', SubCategoryController::class);

        // SubCategory Values
        Route::get('/sub-categories/{subCategory}/values', [
            SubCategoryValuesController::class,
            'index',
        ])->name('sub-categories.values.index');
        Route::post('/sub-categories/{subCategory}/values', [
            SubCategoryValuesController::class,
            'store',
        ])->name('sub-categories.values.store');
    });
