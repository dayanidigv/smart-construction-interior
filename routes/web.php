<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\ScheduleController;
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

     Route::prefix('admin')->middleware(\App\Http\Middleware\RoleAdminMiddleware::class)->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/', "index")->name('admin.index');

            // Users
            Route::get('/add-user', "add_user")->name('admin.add-user');

            // Quantity Units
            Route::get('/quantity-units', "QuantityUnits")->name('admin.quantity-units');

            // Reminder
            Route::get('/reminder', "reminder")->name('admin.reminder');
            Route::get('/reminder/list', "reminder_list")->name('admin.reminder.list');

             // Order
             Route::get('/new/order', "newOrder")->name('admin.new.order');
             Route::get('/list/order', "listOrder")->name('admin.list.order');

              // Design
            Route::get('/gallery', "Gallery")->name('admin.gallery');
            Route::get('/new/design', "newDesign")->name('admin.new.design');
            Route::get('/list/design', "listDesign")->name('admin.list.design');

             // Report
             Route::get('/report', "Report")->name('admin.report');
        });

        Route::controller(CustomerController::class)->group(function(){
            Route::get('/customer/add', "admin_add")->name('admin.customer.add');
            Route::get('/customer/list', "admin_list")->name('admin.customer.list');
            Route::get('/customer/{encodedId}/view', "admin_view")->name('admin.customer.view');
            Route::get('/customer/list/all', "admin_list_all")->name('admin.customer.list-all');
            Route::get('/customer/overall/{encodedId}/view/', "admin_view_all")->name('admin.customer.all.view');
            Route::get('/customer/{encodedId}/edit', "admin_edit")->name('admin.customer.edit');
        });
     });

     Route::controller(ScheduleController::class)->group(function () {
         Route::post('/schedule/store', "store")->name('schedule.store');
         Route::post('/schedule/{id}/update', "update")->name('schedule.update');
     });

     Route::prefix('dev')->middleware(\App\Http\Middleware\DeveloperOnly::class)->group(function () {
        Route::get('/',function () {return Redirect()->route('dev.logs');});
        Route::controller(DeveloperController::class)->group(function () {
            Route::get('/logs', "Logs")->name('dev.logs');
            Route::get('/view/log/{id}', "viewLog")->name('dev.log.view');
            Route::delete('/delete/log/{id}', "deleteLog")->name('dev.log.delete');
            Route::get('/clear-cache', "clearCache");
        });
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});



// Routes for guest users
Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login',  'loginPost')->name('login.post');
    });
});