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
	Route::get('/school-weeks', SchoolWeekManager::class)->name('weeks.manage');
	Route::get('/groups', GroupManager::class)->name('groups.manage');
	Route::get('/groups/{group}', StudyPointMatrix::class)->name('groups.show');
});

// Student routes:
Route::middleware(['auth'])->prefix('student')->group(function () {
	Route::get('/', [StudentController::class, 'show'])->name('student.home');
});

Route::get('/login', function(){
	return redirect('/amoclient/redirect');
})->name('login');

Route::get('/amoclient/ready', function(){
	return redirect('/');
});
