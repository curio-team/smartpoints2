<?php

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

Route::get('/', StudyPointMatrix::class)->middleware('auth')->name('home');
Route::get('/group/{group}', StudyPointMatrix::class)->middleware('auth');
Route::get('/school-weeks', SchoolWeekManager::class)->middleware('auth')->name('weeks.manage');

Route::get('/login', function(){
	return redirect('/amoclient/redirect');
})->name('login');

Route::get('/amoclient/ready', function(){
	return redirect('/');
});
