<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('image_analysis', 'ImageAnalysisController@index');
Route::post('image_analysis/extract', 'ImageAnalysisController@extract');
