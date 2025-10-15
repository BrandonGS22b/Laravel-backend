<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContribuyenteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//  Rutas protegidas (solo usuarios autenticados)
Route::middleware('auth')->group(function () {

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //  CRUD de Contribuyentes
    Route::resource('contribuyentes', ContribuyenteController::class)
        ->names([
            'index' => 'contribuyentes.index',
            'create' => 'contribuyentes.create',
            'store' => 'contribuyentes.store',
            'show' => 'contribuyentes.show',
            'edit' => 'contribuyentes.edit',
            'update' => 'contribuyentes.update',
            'destroy' => 'contribuyentes.destroy', // este es el delete
        ]);

        //metodos para el cud de gestion de usuarios
       Route::middleware(['auth'])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/data', [UserController::class, 'getData'])->name('usuarios.data');
        Route::get('/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });


 
    Route::get('/usuarios/data', [UserController::class, 'getData'])->name('usuarios.data');
});

require __DIR__.'/auth.php';
