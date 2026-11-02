<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // One landing address per role. The permission middleware decides who may open it, so a
    // student typing the administrator address is refused even though they are signed in.
    Route::view('/administrator', 'dashboard')->middleware('permission:manage students')->name('administrator');
    Route::view('/lecturer', 'dashboard')->middleware('permission:view assigned courses')->name('lecturer');
    Route::view('/student', 'dashboard')->middleware('permission:view available courses')->name('student');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
