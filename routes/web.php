<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class);

        Route::resource('categories', CategoryController::class);
        Route::resource('sub-categories', SubCategoryController::class);
        Route::get('/value', [ValueController::class, 'index'])->name('value');
        Route::post('/value/import', [ValueController::class, 'import'])->name('value.import');

        Route::get('/komponen-darah/{id}', [ValueController::class, 'komponenDarah'])->name('komponen.darah');
        Route::get('/kimia-darah/{id}', [ValueController::class, 'kimiaDarah'])->name('kimia.darah');
        Route::get('/urin-rutin/{id}', [ValueController::class, 'urinRutin'])->name('urin.rutin');
        Route::get('/pemeriksaan-diagnostik/{id}', [ValueController::class, 'pemeriksaanDiagnostik'])->name('pemeriksaan.diagnostik');
        Route::get('/annual-tanda-vital/{id}', [ValueController::class, 'annualTandaVital'])->name('tanda.vital');
        Route::get('/kesimpulan-saran/{id}', [ValueController::class, 'kesimpulanSaran'])->name('kesimpulan.saran');
    });
