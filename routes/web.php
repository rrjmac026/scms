<?php

use App\Http\Controllers\ProfileController;
//ADmin ni siya
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CounselorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminFeedbackController;
use App\Http\Controllers\Admin\AppointmentController;
//Counselor ni siya na Routes
use App\Http\Controllers\Counselor\CounselorAppointmentController;
use App\Http\Controllers\Counselor\CounselorFeedbackController;
//Student ni siya na ROutes
use App\Http\Controllers\Student\StudentFeedbackController;
use Illuminate\Support\Facades\Route;
//Auth
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //2FA shit
Route::post('/profile/2fa/enable', [ProfileController::class, 'enableTwoFactor'])->name('profile.enableTwoFactor');
    Route::post('/profile/2fa/verify', [ProfileController::class, 'verifyTwoFactor'])->name('profile.verifyTwoFactor');
    Route::delete('/profile/2fa/disable', [ProfileController::class, 'disableTwoFactor'])->name('profile.disableTwoFactor');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/two-factor-challenge', [AuthenticatedSessionController::class, 'showTwoFactorChallenge'])
         ->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [AuthenticatedSessionController::class, 'twoFactorChallenge'])
         ->name('two-factor.verify');
});

/*
|--------------------------------------------------------------------------
| Role-based Routes
|--------------------------------------------------------------------------
*/

// 🟢 Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    

    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('/users/template', [UserController::class, 'downloadTemplate'])->name('users.template');

    Route::resource('users', UserController::class);
    Route::resource('students', StudentController::class);
    Route::resource('counselors', CounselorController::class);

    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');

    Route::get('feedback', [AdminFeedbackController::class, 'index'])
         ->name('feedback.index');
    Route::get('feedback/{feedback}', [AdminFeedbackController::class, 'show'])
         ->name('feedback.show'); 
});

// 🟡 Counselor routes
Route::middleware(['auth', 'role:counselor'])->prefix('counselor')->name('counselor.')->group(function () {
    Route::get('/dashboard', [CounselorController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [CounselorController::class, 'appointments'])->name('appointments');
    Route::get('/sessions', [CounselorController::class, 'sessions'])->name('sessions');

    Route::get('appointments', [CounselorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [CounselorAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('appointments/{appointment}/status', [CounselorAppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

    Route::resource('offenses', OffenseController::class);
    Route::patch('offenses/{offense}/resolve', [OffenseController::class, 'resolve'])
        ->name('offenses.resolve');

    Route::get('feedback', [CounselorFeedbackController::class, 'index'])
         ->name('feedback.index'); 
    Route::get('feedback/{feedback}', [CounselorFeedbackController::class, 'show'])
         ->name('feedback.show');
    });

// 🔵 Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'index'])->name('dashboard');
    Route::get('/feedback', [StudentController::class, 'feedback'])->name('feedback');

    Route::resource('appointments', StudentAppointmentController::class);


    Route::get('appointments/{appointment}/feedback/create', [StudentFeedbackController::class, 'create'])
         ->name('feedback.create');
    Route::post('appointments/{appointment}/feedback', [StudentFeedbackController::class, 'store'])
         ->name('feedback.store');
    Route::get('feedback', [StudentFeedbackController::class, 'index'])
         ->name('feedback.index');

    });

require __DIR__.'/auth.php';
