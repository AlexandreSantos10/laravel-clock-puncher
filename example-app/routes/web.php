<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\usercontroller;
use \App\Http\Controllers\logscontroller;
use Illuminate\Support\Facades\Auth;
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


route::get('/home', [logscontroller::class, 'homepage'])->name('home');
route::post('/home/create', [logscontroller::class, 'logcreate'])->name('logcreate');
route::get('/clockfinish/{logs}', [logscontroller::class, 'logup'])->name('clockfinish');
route::put('clockfinishupdate/{logs}', [logscontroller::class, 'logupdate'])->name('clockfinishupdate');

route::get('/mylogs', [logscontroller::class, 'indexuser'])->name('mylogs');
route::get('/userlist', [usercontroller::class, 'index'])->name('userlist');
route::get('/dashboard', [logscontroller::class, 'index'])->name('dashboard');

route::get('/createpost',[logscontroller::class, 'create'])->name('createpost');
route::get('/createuser',[usercontroller::class, 'create'])->name('createuser');

route::get('/createpost',[usercontroller::class, 'indexa'])->name('getusers');

Route::post('usercreate', [usercontroller::class, 'usercreate'])->name('usercreate');

Route::post('postcreate', [logscontroller::class, 'postcreate'])->name('postcreate');

Route::get('/editlog/{logs}', [logscontroller::class, 'editlog']);
Route::get('/look/{logs}', [logscontroller::class, 'look']);

Route::put('/editlog/{logs}/update', [logscontroller::class, 'update'])->name('update');

Route::DELETE('/delete/{logs}', [logscontroller::class, 'delete'])->name('delete');

require __DIR__.'/auth.php';
