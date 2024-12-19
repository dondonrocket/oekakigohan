<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageAnnotatorController;

// デフォルトのwelcomeビュー
Route::get('/', function () {
    return view('welcome');
});

// image_annotator へのアクセスで Vue.js コンポーネントを表示
Route::get('image_annotator', function () {
    return view('index');  // 'resources/views/index.blade.php' を返す
});

// 画像解析処理を POST リクエストで実行
Route::post('image_annotator/extract', [ImageAnnotatorController::class, 'extract']); // 画像解析処理
