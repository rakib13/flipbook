<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AjaxController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/flipbook', fn() => view('flipbook'));

Route::get('/Modal', [AjaxController::class, 'loadModalContent']);
