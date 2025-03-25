<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Home page
    Route::get('/', [UserController::class, 'index'])->name('home');
    
    // User profiles
    Route::get('/profile/{user}', [UserController::class, 'show'])->name('profile.show');
    Route::put('/profile/{user}', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Messages
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');
    Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{user}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    
    // Assignments
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}/download', [AssignmentController::class, 'download'])->name('assignments.download');
    
    // Submissions
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/my-submissions', [SubmissionController::class, 'mySubmissions'])->name('submissions.my');
    Route::get('/assignments/{assignment}/submit', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download'])->name('submissions.download');
    
    // Challenges
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/create', [ChallengeController::class, 'create'])->name('challenges.create');
    Route::post('/challenges', [ChallengeController::class, 'store'])->name('challenges.store');
    Route::post('/challenges/{challenge}/check', [ChallengeController::class, 'checkAnswer'])->name('challenges.check');
    Route::get('/challenges/{challenge}/download', [ChallengeController::class, 'download'])->name('challenges.download');
    Route::get('/challenges/{challenge}/content', [ChallengeController::class, 'getContent'])->name('challenges.content');
    
    // Secure file downloads và Challenge content - update để sử dụng đúng controller
    Route::get('/file/{type}/{id}', [FileController::class, 'download'])->name('file.download');
    Route::get('/challenge/{id}/content', [FileController::class, 'getChallengeContent'])->name('file.challengeContent');
    
    // Student management (teachers only)
    Route::get('/students', [UserController::class, 'students'])->name('students.index');
});
