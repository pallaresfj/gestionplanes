<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/* Route::get('/', function () {
    return view('welcome');
}); */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/planes', [HomeController::class, 'planes'])->name('planes');
Route::get('/plan/{id}', [HomeController::class, 'plandetail'])->name('plan');
Route::get('/subject/{id}', [HomeController::class, 'subjectdetail'])->name('subject');
Route::get('/centers', [HomeController::class, 'centers'])->name('centers');
Route::get('/center/{id}', [HomeController::class, 'centerdetail'])->name('center');
