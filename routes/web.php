<?php

use App\Http\Controllers\ProfileController;
//ADmin ni siya
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CounselorManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminFeedbackController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminOffenseController;
use App\Http\Controllers\Admin\AdminCounselingCategoryController;
//Counselor ni siya na Routes
use App\Http\Controllers\Counselor\CounselorAppointmentController;
use App\Http\Controllers\Counselor\CounselorFeedbackController;
use App\Http\Controllers\Counselor\CounselorController;
use App\Http\Controllers\Counselor\CounselingSessionController;
use App\Http\Controllers\Counselor\OffenseController;
use App\Http\Controllers\Counselor\CounselorCounselingCategoryController;
//Student ni siya na ROutes
use App\Http\Controllers\Student\StudentFeedbackController;
use App\Http\Controllers\Student\StudentAppointmentController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\CounselingHistoryController;

use Illuminate\Support\Facades\Route;
//Auth
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;

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
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])
    ->middleware(['auth', 'verified'])
    ->name('profile.update-photo');
    //Google OAuth ni siya
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.connect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::post('/auth/google/disconnect', [GoogleAuthController::class, 'disconnect'])
        ->name('google.disconnect');

    Route::get('/auth/google/status', [GoogleAuthController::class, 'status'])
        ->name('google.status');
        
    
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
    Route::resource('students', StudentManagementController::class);
    Route::resource('counselors', CounselorManagementController::class);

    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/calendar', [AppointmentController::class, 'calendar'])
        ->name('appointments.calendar');
    Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('appointments/{appointment}/assign', [AppointmentController::class, 'assignCounselor'])
         ->name('appointments.assign');
    Route::get('appointments/{appointment}/debug-calendar', [AppointmentController::class, 'debugCalendarEvent'])
    ->name('appointments.debug-calendar');
         

    Route::resource('counseling-sessions', AdminSessionController::class);

    Route::resource('offenses', AdminOffenseController::class);
    Route::post('offenses/{offense}/resolve', [AdminOffenseController::class, 'resolve'])->name('offenses.resolve');

    Route::resource('counseling-categories', AdminCounselingCategoryController::class);
    Route::patch('counseling-categories/{counseling_category}/approve', [AdminCounselingCategoryController::class, 'approve'])->name('counseling-categories.approve');
    Route::patch('counseling-categories/{counseling_category}/reject', [AdminCounselingCategoryController::class, 'reject'])->name('counseling-categories.reject');

    Route::get('feedback', [AdminFeedbackController::class, 'index'])
         ->name('feedback.index');
    Route::get('feedback/{feedback}', [AdminFeedbackController::class, 'show'])
         ->name('feedback.show'); 

});

// 🟡 Counselor routes
Route::middleware(['auth', 'role:counselor'])->prefix('counselor')->name('counselor.')->group(function () {
    Route::get('/dashboard', [CounselorController::class, 'dashboard'])->name('dashboard');
    Route::get('/sessions', [CounselorController::class, 'sessions'])->name('sessions');

    // Appointment routes
    Route::get('/appointments', [CounselorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [CounselorAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/status', [CounselorAppointmentController::class, 'updateStatus'])
        ->name('appointments.update-status');
    Route::get('/calendar', [CounselorAppointmentController::class, 'counselorCalendar'])
            ->name('calendar');
    
    Route::resource('counseling-sessions', CounselingSessionController::class);

    Route::resource('offenses', OffenseController::class);
    Route::patch('offenses/{offense}/resolve', [OffenseController::class, 'resolve'])
        ->name('offenses.resolve');
        
    Route::get('counseling-categories/{counseling_category}', [CounselingCategoryController::class, 'show'])
        ->name('counseling-categories.show');
    Route::resource('counseling-categories', CounselorCounselingCategoryController::class)
        ->except(['destroy']);

    Route::get('feedback', [CounselorFeedbackController::class, 'index'])
         ->name('feedback.index'); 
    Route::get('feedback/{feedback}', [CounselorFeedbackController::class, 'show'])
         ->name('feedback.show');
    });


    // 🔵 Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/feedback', [StudentController::class, 'feedback'])->name('feedback');

    Route::resource('appointments', StudentAppointmentController::class);
    Route::get('/student/calendar', [StudentAppointmentController::class, 'studentCalendar'])
        ->name('calendar');

    // Listahan nis counseling sessions
    Route::get('/counseling-history', [CounselingHistoryController::class, 'index'])
              ->name('counseling-history.index');
    Route::get('/counseling-history/{id}', [CounselingHistoryController::class, 'show'])
              ->name('counseling-history.show');


    Route::get('counseling-history/{session}/feedback/create', [StudentFeedbackController::class, 'createFeedbackForSession'])
     ->name('feedback.create');

    Route::post('counseling-history/{session}/feedback', [StudentFeedbackController::class, 'storeFeedbackForSession'])
     ->name('feedback.store');
    Route::get('feedback', [StudentFeedbackController::class, 'index'])
         ->name('feedback.index');
         

    });

Route::get('/debug/google-config', function() {
    return response()->json([
        'client_id_exists' => !empty(config('services.google.client_id')),
        'client_secret_exists' => !empty(config('services.google.client_secret')),
        'redirect_uri' => config('services.google.redirect'),
        'user_logged_in' => auth()->check(),
        'user_id' => auth()->id(),
    ]);
});

require __DIR__.'/auth.php';
