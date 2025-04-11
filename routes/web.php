<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StickyNoteController;
use App\Http\Controllers\SharedContentController;
use App\Http\Controllers\XPostController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sticky-notes', [StickyNoteController::class, 'index'])->name('sticky-notes.index');
    Route::post('/sticky-notes', [StickyNoteController::class, 'store'])->name('sticky-notes.store');
    Route::put('/sticky-notes/{stickyNote}', [StickyNoteController::class, 'update'])->name('sticky-notes.update');
    Route::delete('/sticky-notes/{stickyNote}', [StickyNoteController::class, 'destroy'])->name('sticky-notes.destroy');
    Route::post('/sticky-notes/update-order', [StickyNoteController::class, 'updateOrder'])->name('sticky-notes.update-order');
    
    // Shared Content Routes
    Route::post('/shared-content', [SharedContentController::class, 'store'])->name('shared-content.store');
    Route::delete('/shared-content/{slug}', [SharedContentController::class, 'destroy'])->name('shared-content.destroy');

    // X Post routes
    Route::prefix('x-post')->name('x-post.')->group(function () {
        Route::get('/', [XPostController::class, 'index'])->name('index');
        Route::put('/', [XPostController::class, 'update'])->name('update');
        
        // Context management routes
        Route::post('/contexts', [XPostController::class, 'storeContext'])->name('contexts.store');
        Route::put('/contexts/{context}', [XPostController::class, 'updateContext'])->name('contexts.update');
        Route::delete('/contexts/{context}', [XPostController::class, 'destroyContext'])->name('contexts.destroy');
        
        // Tweet management routes
        Route::post('/generate', [XPostController::class, 'generateTweet'])->name('generate');
        Route::get('/tweets', [XPostController::class, 'listTweets'])->name('tweets');
        Route::post('/tweets/{tweet}/mark-sent', [XPostController::class, 'markAsSent'])->name('tweets.mark-sent');
        Route::delete('/tweets/{tweet}', [XPostController::class, 'destroy'])->name('tweets.destroy');
    });
});

// Public route for shared content
Route::get('/shared/{slug}', [SharedContentController::class, 'show'])->name('shared-content.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
