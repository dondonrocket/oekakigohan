<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

// Vue.js アプリケーションを表示するビューを一つのルートにまとめる
Route::get('/{any}', function () {
    return view('vue-app'); // Vue.js アプリケーションのエントリポイントを表示
})->where('any', '.*'); // 任意のパスをキャッチ

//セッション作成
Route::post('/session/set-image', [ImageController::class, 'storeImageInSession']);

//セッション保持
Route::get('/session/get-image', function () {
    $imagePath = session('image_path'); // セッションから画像パスを取得

    if ($imagePath) {
        return response()->json([
            'image_path' => $imagePath, // セッションに画像が保存されていればそのパスを返す
        ]);
    }

    return response()->json([
        'image_path' => null, // 画像が保存されていなければnullを返す
    ]);
});

// セッション削除
Route::get('/session/delete-image', function () {
    // セッションから画像パスを削除
    session()->forget('temporary_image');

    return response()->noContent();
});
