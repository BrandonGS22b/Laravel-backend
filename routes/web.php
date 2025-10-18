<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContribuyenteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::group([], function () {

   
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

   
    Route::get('/contribuyentes/data', [ContribuyenteController::class, 'getData'])->name('contribuyentes.data');
   

    Route::resource('contribuyentes', ContribuyenteController::class)
        ->names([
            'index' => 'contribuyentes.index',
            'create' => 'contribuyentes.create',
            'store' => 'contribuyentes.store',
            'show' => 'contribuyentes.show',
            'edit' => 'contribuyentes.edit',
            'update' => 'contribuyentes.update',
            'destroy' => 'contribuyentes.destroy',
        ]);


   
    
    Route::prefix('usuarios')->group(function () {
        Route::get('/get', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/data', [UserController::class, 'getData'])->name('usuarios.data');
        Route::get('/create', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/{usuario}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
});

require __DIR__.'/auth.php';
