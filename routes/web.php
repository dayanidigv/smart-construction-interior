<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeveloperController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () { 
        $role = Auth::user()->role;
        if($role == "admin"){
            return Redirect("/admin");
        }elseif($role == "manager"){
            return Redirect("/manager");
        }elseif($role == "developer"){
            return Redirect()->route('dev.logs');
        }
     })->name('index');



     Route::prefix('dev')->middleware(\App\Http\Middleware\DeveloperOnly::class)->group(function () {
        Route::get('/',function () {return Redirect()->route('dev.logs');});
        Route::controller(DeveloperController::class)->group(function () {
            Route::get('/logs', "Logs")->name('dev.logs');
            Route::get('/view/log/{id}', "viewLog")->name('dev.log.view');
            Route::delete('/delete/log/{id}', "deleteLog")->name('dev.log.delete');
            Route::get('/clear-cache', "clearCache");
        });
    });
});



// Routes for guest users
Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login',  'loginPost')->name('login.post');
    });
});