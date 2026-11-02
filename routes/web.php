<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // One landing address per role. The permission middleware decides who may open it, so a
    // student typing the administrator address is refused even though they are signed in.
    Route::get('/administrator', DashboardController::class)->middleware('permission:manage students')->name('administrator');
    Route::get('/lecturer', DashboardController::class)->middleware('permission:view assigned courses')->name('lecturer');
    Route::get('/student', DashboardController::class)->middleware('permission:view available courses')->name('student');

    // Student records. Reading one record is decided by the policy inside the controller because a
    // student may open their own; writing any record is an administrator task.
    Route::get('students', [StudentController::class, 'index'])->name('students.index');

    Route::middleware('permission:manage students')->group(function () {
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');

    // The catalogue is read by every role but written by an administrator only.
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');

    Route::middleware('permission:manage courses')->group(function () {
        Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // A student registers for their own courses; an administrator may register any student.
    Route::get('registrations', [RegistrationController::class, 'index'])->name('registrations.index');

    Route::middleware('permission:register for courses|manage registrations')->group(function () {
        Route::get('registrations/create', [RegistrationController::class, 'create'])->name('registrations.create');
        Route::post('registrations', [RegistrationController::class, 'store'])->name('registrations.store');
    });

    Route::delete('registrations/{registration}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
