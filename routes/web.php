<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
use App\Http\Controllers\logscontroller;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/esp32/ponto', [logscontroller::class, 'receberPontoDoEsp32']);
Route::post('/esp32/enroll-status', [usercontroller::class, 'receberStatusEnroll']);

Route::post('/esp32/delete-finger-status', [usercontroller::class, 'receberStatusDeleteFinger']);



Route::middleware('auth')->group(function () {

    // Dashboard Principal
    Route::get('/dashboard', function () {
        return view('user/home');
    })->middleware(['verified'])->name('dashboard');

    // Gestão de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ROTAS DE UTILIZADOR (User) ---
    Route::prefix('user')->group(function () {
        Route::get('/logs', [logscontroller::class, 'userlogs'])->name('userlogs');
        Route::get('/home', [logscontroller::class, 'homepage'])->name('home');
        Route::post('/home/create', [logscontroller::class, 'userlogcreate'])->name('logcreate');
        Route::get('/clockfinish/{logs}', [logscontroller::class, 'userlogup'])->name('clockfinish');
        Route::put('/clockfinishupdate/{logs}', [logscontroller::class, 'userlogupdate'])->name('clockfinishupdate');
        Route::get('/looklog/{logs}', [logscontroller::class, 'looklog'])->name('userlooklog');
        Route::get('/editlog/{logs}', [logscontroller::class, 'editlog'])->name('usereditlog');
        Route::put('/editlog/{logs}/update', [logscontroller::class, 'updatelog'])->name('updateuserlog');
        Route::delete('/delete/{logs}', [logscontroller::class, 'deletelog'])->name('deleteuserlog');
    });

    // --- ROTAS DE ADMINISTRAÇÃO (Admin) ---
    Route::prefix('admin')->group(function () {
        // Gestão de Logs
        Route::get('/logs', [logscontroller::class, 'adminlogs'])->name('adminlogs');
        Route::get('/createlogview', [logscontroller::class, 'createlogview'])->name('createlogview');
        Route::post('/createlog', [logscontroller::class, 'createlog'])->name('createlog');
        Route::delete('/delete/{logs}', [logscontroller::class, 'deletelog'])->name('deletelog');
        Route::get('/looklog/{logs}', [logscontroller::class, 'looklog']);
        Route::get('/editlog/{logs}', [logscontroller::class, 'editlog']);
        Route::put('/editlog/{logs}/update', [logscontroller::class, 'updatelog'])->name('updatelog');

        Route::get('/admin-logs', [logscontroller::class, 'adminLogsAudit'])->name('admin.adminlogs');
        
        // Exportações
        Route::get('/export', [logscontroller::class, 'export'])->name('export');
        Route::get('/export/logs', [logscontroller::class, 'exportuserlog'])->name('exportuserlog');
        Route::get('/export/users', [usercontroller::class, 'exportusers'])->name('exportusers');

        // Gestão de Utilizadores
        Route::get('/users', [usercontroller::class, 'userlist'])->name('userlist');
        Route::post('/users/{id}/delete-finger', [usercontroller::class, 'deleteFinger'])->name('users.delete_finger');
        Route::get('/createuserview', [usercontroller::class, 'createuserview'])->name('createuserview');
        Route::post('/usercreate', [usercontroller::class, 'createuser'])->name('createuser');
        Route::put('/change/{user}', [usercontroller::class, 'changeusertype'])->name('changeusertype');

        Route::get('/approve-log/{id}', [App\Http\Controllers\logscontroller::class, 'approveLog'])->name('admin.approve_log');
        Route::get('/reject-log/{id}', [App\Http\Controllers\logscontroller::class, 'rejectLog'])->name('admin.reject_log');

        Route::get('/admin/users/{id}/finger-status', [usercontroller::class, 'checkFingerStatus'])->name('users.finger_status');

    });

    // Biometria (Enroll)
    Route::post('/users/{id}/enroll', [usercontroller::class, 'enroll'])->name('users.enroll');

});


require __DIR__.'/auth.php';