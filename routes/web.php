<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/* Route::get('/', function () {
    return view('welcome');
}); */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/plan/{id}', [HomeController::class, 'plandetail'])->name('plan');
Route::get('/subject/{id}', [HomeController::class, 'subjectdetail'])->name('subject');
