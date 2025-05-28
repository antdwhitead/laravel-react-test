<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', PostController::class);

    // Comment routes - only the ones that are actually used
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
