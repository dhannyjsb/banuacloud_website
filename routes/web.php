<?php

use App\Http\Controllers\SeoPageController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SeoPageController::class, 'sitemap'])
    ->name('sitemap');

Route::get('/', [SeoPageController::class, 'index'])
    ->name('home');

Route::get('/{any}', [SeoPageController::class, 'index'])
    ->where('any', '^(?!api|up).*$');
