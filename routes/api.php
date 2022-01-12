<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VarController;
use App\Http\Controllers\ImageUploadController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1'], function () {
    Route::post('/register', [UsersController::class, 'register']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::get('/logout', [UsersController::class, 'logout'])->middleware('auth:api');
    Route::get('/user', [UsersController::class, 'user'])->middleware('auth:api');
});
Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('/create', [PasswordResetController::class, 'create']);
    Route::get('/find/{token}', [PasswordResetController::class, 'find']);
    Route::post('/reset', [PasswordResetController::class, 'reset']);
});
Route::post('/sendOtp', [UsersController::class, 'sendOtp']);
Route::post('/verifyOtp', [UsersController::class, 'verifyOtp']);
Route::post('/change', [UsersController::class, 'changeUserDetail']);
Route::post('/role', [RoleController::class, 'getRole']);
Route::post('/employees', [RoleController::class, 'getAllemployees']);
Route::post('/search', [UsersController::class, 'searchEmployee']);
Route::post('/getDetails', [UsersController::class, 'getEmployeeDetails']);
Route::post('/delete', [UsersController::class, 'deleteUser']);
Route::post('/getimg', [UsersController::class, 'insertImg']);
Route::get('image/{id}', [VarController::class, 'image']);

Route::group(['middleware' => ['web']], function () {
    Route::get('image-upload', [ImageUploadController::class, 'imageUpload'])->name('image.upload');
    Route::post('image-upload', [ImageUploadController::class, 'imageUploadPost'])->name('image.upload.post');
});
