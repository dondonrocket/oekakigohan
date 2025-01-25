<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function storeImageInSession(Request $request)
    {
        // リクエストが画像を含んでいるか確認
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // 画像を一時保存ディレクトリに保存
            $path = $file->store('temp', 'public'); // 'public/temp' に保存されます

            // URLに変換
            $publicPath = asset('storage/' . $path);

            // セッションに保存
            session(['image_path' => $publicPath]);

            return response()->json([
                'message' => '画像がセッションに保存されました',
                'image_path' => $publicPath,
            ]);
        } else {
            return response()->json([
                'error' => '画像がアップロードされていません',
            ], 400);
        }
    }
}
