<?php

namespace App\Http\Controllers;

use Google\Cloud\Vision\V1\ImageAnnotatorClient; // Google Cloud Vision APIのクライアント
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImageAnnotatorController extends Controller
{
    /**
     * 画像アップロード用のページを表示
     */
    public function index()
    {
        return view('index');
    }

    /**
     * 子どもの絵を解析して、結果を返す
     */
    public function extract(Request $request)
    {
        // デバッグ用ログ: リクエスト情報
        Log::info('ImageAnnotatorController: Request received', [
            'files' => $request->allFiles(),
        ]);

        // 1. アップロード画像のバリデーション
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            Log::error('Invalid image file');
            return response()->json(['result' => false, 'message' => '画像が無効です。']);
        }

        // アップロードされた画像のパス
        $imagePath = $request->file('image')->path();

        // 2. Google Vision API クライアントを作成
        $client = new ImageAnnotatorClient();

        try {
            // 3. 画像データを取得
            $imageData = file_get_contents($imagePath);

            // 4. ラベル検出 (labelDetection) の実行
            $response = $client->labelDetection($imageData);
            $labels = $response->getLabelAnnotations();

            // ラベル解析結果を整形
            $results = [];
            if ($labels) {
                foreach ($labels as $label) {
                    $results[] = [
                        'description' => $label->getDescription(), // ラベルの説明
                        'score' => $label->getScore(),             // 確信度
                    ];
                }
            } else {
                Log::info('No labels found in the image.');
            }

            // デバッグ用ログ: API結果を記録
            Log::info('ImageAnnotatorController: Analysis results', ['labels' => $results]);

            // 5. 結果をJSON形式で返す
            return response()->json([
                'result' => true,
                'labels' => $results,
            ]);

        } catch (\Exception $e) {
            // エラーハンドリング
            Log::error('ImageAnnotatorController: Error during analysis', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'result' => false,
                'message' => '解析中にエラーが発生しました: ' . $e->getMessage(),
            ]);
        } finally {
            $client->close(); // クライアントを閉じる
        }
    }
}
