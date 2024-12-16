<?php

namespace App\Http\Controllers;

use Google\Cloud\Vision\V1\ImageAnnotatorClient; // Google Cloud Vision APIのクライアント
use Illuminate\Http\Request;

class ImageAnnotatorController extends Controller
{
    /**
     * 画像アップロード用のページを表示
     */
    public function index()
    {
        return view('image_annotator.index'); // ビューを表示 (例: フォーム)
    }

    /**
     * 子どもの絵を解析して、結果を返す
     */
    public function extract(Request $request)
    {
        // 1. Google Vision APIの認証キーのパスを設定 (環境変数で設定済み)
        // 追加の設定不要

        // 2. Google Vision API クライアントの作成
        $client = new ImageAnnotatorClient([
            'projectId' => env('GOOGLE_CLOUD_PROJECT'), // .envからプロジェクトIDを取得
        ]);

        // 3. アップロードされた画像ファイルを取得
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['result' => false, 'message' => '画像が無効です。']);
        }

        $imagePath = $request->file('image')->path(); // ファイルパスを取得

        // 4. Google Vision APIで画像データを解析
        try {
            // ファイルデータを読み込んで画像オブジェクトを作成
            $imageData = file_get_contents($imagePath);
            $image = $client->createImageObject($imageData);

            // 物体検出 (LocalizedObjectAnnotations) と文字検出 (TextAnnotations)
            $objectResponse = $client->objectLocalization($image); // 物体検出
            $textResponse = $client->textDetection($image);        // 文字検出

            // エラーがある場合は処理を中止
            if (!is_null($objectResponse->getError()) || !is_null($textResponse->getError())) {
                return response()->json(['result' => false, 'message' => '画像解析に失敗しました。']);
            }

            // 物体検出の結果を取得
            $objects = [];
            foreach ($objectResponse->getLocalizedObjectAnnotations() as $annotation) {
                $objects[] = [
                    'name' => $annotation->getName(),                 // 検出された物体の名前
                    'confidence' => $annotation->getScore(),          // 確信度
                    'boundingPoly' => $annotation->getBoundingPoly(), // 枠線情報
                ];
            }

            // 文字検出の結果を取得
            $texts = [];
            foreach ($textResponse->getTextAnnotations() as $index => $annotation) {
                if ($index === 0) continue; // 最初の要素は全文なのでスキップ
                $texts[] = [
                    'text' => $annotation->getDescription(),          // 検出された文字
                    'boundingPoly' => $annotation->getBoundingPoly(), // 枠線情報
                ];
            }

            // 結果をJSON形式で返す
            return response()->json([
                'result' => true,
                'objects' => $objects,
                'texts' => $texts,
            ]);

        } catch (\Exception $e) {
            // エラーが発生した場合はエラーメッセージを返す
            return response()->json([
                'result' => false,
                'message' => '解析中にエラーが発生しました: ' . $e->getMessage(),
            ]);
        } finally {
            $client->close(); // クライアントを閉じる
        }
    }
}
