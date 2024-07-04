<?php

use App\Http\Controllers\StudentController;
use App\Livewire\GroupManager;
use App\Livewire\SchoolWeekManager;
use App\Livewire\StudyPointMatrix;
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

// Teacher routes:
Route::middleware(['auth', 'teacher'])->group(function () {
	Route::get('/', StudyPointMatrix::class)->name('home');
	Route::get('/groups', GroupManager::class)->name('groups.manage');
	Route::get('/groups/{group}', StudyPointMatrix::class)->name('groups.show');
	Route::get('/student/{id}', [StudentController::class, 'show'])->name('student.show');
});

// Student routes:
Route::middleware(['auth'])->group(function () {
	Route::get('/student', [StudentController::class, 'show'])->name('student.home');
});

// Auth routes for SdClient
Route::get('/login', function(){
	return redirect('/sdclient/redirect');
})->name('login');

Route::get('/sdclient/ready', function(){
	return redirect('/');
});

Route::get('/sdclient/error', function() {
    $error = session('sdclient.error');
    $error_description = session('sdclient.error_description');

    return 'There was an error signing in: ' . $error_description . ' (' . $error . ')<br><a href="/login">Try again</a>';
});
