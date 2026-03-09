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

route::get('/user/logs', [logscontroller::class, 'userlogs'])->name('userlogs');
route::get('/user/home', [logscontroller::class, 'homepage'])->name('home');
route::post('/user/home/create', [logscontroller::class, 'userlogcreate'])->name('logcreate');
route::get('/user/clockfinish/{logs}', [logscontroller::class, 'userlogup'])->name('clockfinish');
route::put('/user/clockfinishupdate/{logs}', [logscontroller::class, 'userlogupdate'])->name('clockfinishupdate');



route::get('/admin/logs', [logscontroller::class, 'adminlogs'])->name('adminlogs');
route::get('/admin/createlogview',[logscontroller::class, 'createlogview'])->name('createlogview');
Route::post('/admin/createlog', [logscontroller::class, 'createlog'])->name('createlog');
Route::get('/admin/export', [logscontroller::class, 'export'])->name('export');
Route::get('/admin/export/logs', [logscontroller::class, 'exportuserlog'])->name('exportuserlog');

route::get('/admin/users', [usercontroller::class, 'userlist'])->name('userlist');
Route::get('/admin/export/users', [usercontroller::class, 'exportusers'])->name('exportusers');

route::get('/admin/createuserview',[usercontroller::class, 'createuserview'])->name('createuserview');
Route::post('/admin/usercreate', [usercontroller::class, 'createuser'])->name('createuser');
Route::put('/admin/change/{user}', [usercontroller::class, 'changeusertype'])->name('changeusertype');

Route::DELETE('/admin/delete/{logs}', [logscontroller::class, 'deletelog'])->name('deletelog');
Route::get('/admin/looklog/{logs}', [logscontroller::class, 'looklog']);
Route::get('/admin/editlog/{logs}', [logscontroller::class, 'editlog']);
Route::put('/admin/editlog/{logs}/update', [logscontroller::class, 'updatelog'])->name('updatelog');





require __DIR__.'/auth.php';
