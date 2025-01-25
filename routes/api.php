<?php
use App\Http\Controllers\ImageAnalysisController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::post('/contact', [ContactController::class, 'sendEmail']);


Route::post('/analyze-image', [ImageAnalysisController::class, 'analyzeImage']);


