<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VideoController;
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

Route::group(
    [
        'prefix' => 'v1'
    ],
    function () {
        Route::group(
            [
                'prefix'     => 'auth',
                'middleware' => ['throttle:api']
            ],
            function () {
                Route::controller(AuthController::class)
                    ->group(
                        function () {
                            Route::post('register', 'register');
                            Route::post('login', 'login');
                        }
                    );
            }
        );

        Route::middleware('auth:sanctum')->group(
            function () {
                Route::middleware(['throttle:api'])->group(function () {
                    Route::controller(AuthController::class)->group(
                        function () {
                            Route::post('auth/logout', 'logout');
                        }
                    );

                    Route::apiResource('videos', VideoController::class);
                });

                Route::group(
                    [
                        'prefix'     => 'videos/{video}',
                        'middleware' => ['throttle:video']
                    ],
                    function () {
                        Route::controller(VideoController::class)->group(
                            function () {
                                Route::get('stream/{quality}', 'stream');
                                Route::get('thumbnail', 'thumbnail');
                                Route::get('preview', 'preview');
                            }
                        );
                    }
                );
            }
        );
    }
);
