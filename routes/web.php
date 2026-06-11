<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login'); 
});

Route::post('/shops/{slug}/increment-view', [ShopController::class, 'incrementView']);
Route::post('/products/{id}/increment-view', [ProductController::class, 'incrementView']);