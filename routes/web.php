<?php

declare(strict_types=1);

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoController::class, 'index'])->name('home');
Route::get('/blade-directive', [DemoController::class, 'bladeDirective'])->name('blade_directive');
Route::get('/facade', [DemoController::class, 'facade'])->name('facade');
Route::get('/service', [DemoController::class, 'service'])->name('service');
Route::get('/form', [DemoController::class, 'form'])->name('form');
Route::post('/form', [DemoController::class, 'formSubmit'])->name('form.submit');
Route::get('/safe-mode', [DemoController::class, 'safeMode'])->name('safe_mode');
Route::get('/static-mode', [DemoController::class, 'staticMode'])->name('static_mode');
Route::get('/plain-text', [DemoController::class, 'plainText'])->name('plain_text');
Route::get('/extensions', [DemoController::class, 'extensions'])->name('extensions');
