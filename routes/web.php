<?php

use App\Domains\Shared\Http\Controllers\DocumentationController;
use App\Domains\Shared\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/docs', [DocumentationController::class, 'ui'])->name('docs');
Route::get('/docs/openapi.json', [DocumentationController::class, 'specification'])->name('docs.openapi');
