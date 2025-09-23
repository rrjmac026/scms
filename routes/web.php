<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CounselorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CounselorAppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Common dashboard (only logged in & verified users can see)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Shared profile routes for all logged-in users
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Role-based Routes
|--------------------------------------------------------------------------
*/

// 🟢 Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management Routes
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('/users/template', [UserController::class, 'downloadTemplate'])->name('users.template');
    Route::resource('users', UserController::class);
    Route::resource('students', StudentController::class);
    Route::resource('counselors', CounselorController::class);
});

// 🟡 Counselor routes
Route::middleware(['auth', 'role:counselor'])->prefix('counselor')->name('counselor.')->group(function () {
    Route::get('/dashboard', [CounselorController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [CounselorController::class, 'appointments'])->name('appointments');
    Route::get('/sessions', [CounselorController::class, 'sessions'])->name('sessions');

    Route::get('appointments', [CounselorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [CounselorAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('appointments/{appointment}/status', [CounselorAppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
});

// 🔵 Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'index'])->name('dashboard');
    Route::get('/feedback', [StudentController::class, 'feedback'])->name('feedback');

    Route::resource('appointments', StudentAppointmentController::class);

    });

require __DIR__.'/auth.php';
