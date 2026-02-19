<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\usercontroller;
use \App\Http\Controllers\logscontroller;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


route::get('/userlist', [usercontroller::class, 'index'])->name('userlist');
route::get('/dashboard', [logscontroller::class, 'index'])->name('dashboard');

route::get('/createpost',[logscontroller::class, 'create'])->name('createpost');
route::get('/createuser',[usercontroller::class, 'create'])->name('createuser');


Route::post('usercreate', [usercontroller::class, 'usercreate'])->name('usercreate');

Route::post('postcreate', [logscontroller::class, 'postcreate'])->name('postcreate');

Route::get('/editlog/{logs}', [logscontroller::class, 'editlog']);
require __DIR__.'/auth.php';
