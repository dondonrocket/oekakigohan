<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\JsonResponse;

use Exception;

class ImageAnalysisController extends Controller
{
    public function analyzeImage(Request $request)
    {
        try {
            // 画像ファイルがアップロードされているか確認
            if (!$request->hasFile('image')) {
                return response()->json(['error' => '画像ファイルがアップロードされていません。'], 400);
            }

            $image = $request->file('image');

            // ファイルが画像であることを確認
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // レシピ画像保存用に画像をセッションに保存
            $imagePath = $image->store('images', 'public');
            session(['temporary_image' => $imagePath]);

            // 画像ファイルをBase64エンコード
            $base64Image = base64_encode(file_get_contents($image->getPathname()));

            // OpenAI APIへのリクエスト（新しいメッセージ構造を使用）
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ["type" => "text", "text" => "この画像に描かれている料理名を特定して１つ挙げて下さい。条件は以下の通りです。1.回答は必ず料理名でお願いします。2.色で味付け、形で特徴を判断し、具体的な料理名にすること。（例：明太子スパゲッティ、醤油ラーメン）3.もし特定できない場合は、見た目や形状から最も適切だと思われる料理名を色で味付け、形で特徴を判断して具体的な料理名を一つ挙げて下さい。回答は必ず料理名でお願いします。"],
                            ["type" => "image_url", "image_url" => ["url" => "data:image/jpeg;base64,{$base64Image}"]],
                        ],
                    ],
                ],
                'max_tokens' => 300,
            ]);
            //初期化
            $analysis = null;
            // 解析結果を取得（オブジェクトプロパティに正しくアクセス）
            $analysis = $result->choices[0]->message->content ?? '解析結果がありません。';

            // カテゴリー照合処理に全カテゴリーを渡す
            $matchedCategory = $this->matchCategoryWithAnalysis($analysis);

            // 照合結果がない場合
            if (!$matchedCategory) {
                Log::warning('Category not found for analysis:', ['analysis' => $analysis]);
                return response()->json(['error' => 'ごめんなさい。レシピを作成できませんでした。もう一度お試し下さい。'], 404);
            }

            // 楽天APIを使ってカテゴリー別ランキングを取得
            $rakutenApiResponse = $this->fetchRakutenCategoryRanking($matchedCategory);

            // 追加のレシピ生成リクエスト
            $recipeResult = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "「{$analysis}」という料理を親子で作れるレシピにしてください。条件は以下の通りです：\n
                        1. 材料は日常的に手に入るもの。\n
                        2. 作業を子どもと分担できるステップを説明文も含めて明示。\n
                        3. 必ず説明文を入れ、説明文は簡潔で、手順を分かりやすく表記してください。\n
                        4. #材料、#手順のように、材料と手順の前には#を入れて区切って下さい。 \n
                        例: 材料: ～ 手順: ～",
                    ],
                ],
                'max_tokens' => 1000, // 必要に応じて調整
            ]);

    // レシピ結果を取得
            $recipeContent = $recipeResult->choices[0]->message->content ?? 'レシピ生成ができませんでした。';
            // 材料と手順を分離
            $recipeData = $this->parseRecipe($recipeContent);

            // 結果をJSONで返す前にログを出力
            Log::info('最終結果:', [
                'analysis' => $analysis,
                'matched_category' => $matchedCategory,
                'category_ranking' => $rakutenApiResponse,
                'recipe' => $recipeData,
            ]);

            // 結果をJSONで返す
            return response()->json([
                'analysis' => $analysis,
                'matched_category' => $matchedCategory,
                'categoryRanking' => $rakutenApiResponse,
                'recipe' => $recipeData,
            ], 200);


        } catch (\Exception $e) {
            // エラーのログを記録
            Log::error('画像解析エラー: ' . $e->getMessage());

            // エラーレスポンスを返す
            return response()->json([
                'error' => '画像解析中にエラーが発生しました。',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

/**
     * 画像解析結果とカテゴリー名を照合する
     *
     * @param  string  $analysis
     * @return array
     */
    private function matchCategoryWithAnalysis(string $analysis): ?string
    {
        // トリムして余計な空白や改行を削除
        $analysis = trim($analysis);

        // categories.jsonを事前に取得
        $categoriesJson = Storage::disk('local')->get('categories.json');
        $categories = json_decode($categoriesJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('categories.json の読み込みに失敗しました: ' . json_last_error_msg());
            throw new Exception('categories.json の形式が無効です。');
        }
        // カテゴリを検索
        $allCategories = array_merge(
            $categories['result']['large'] ?? [],
            $categories['result']['medium'] ?? [],
            $categories['result']['small'] ?? []
        );

        foreach ($allCategories as $category) {
            if ($category['categoryName'] === $analysis) {
                // 正規表現でcategoryUrlからcategoryIdを抽出
                if (preg_match('/category\/([\d\-]+)\//', $category['categoryUrl'], $matches)) {
                    Log::info('Extracted Category ID:', ['categoryId' => $matches[1]]);
                    return $matches[1]; // categoryId (例: "17-159-2155")
                }
            }
        }
        Log::warning('一致するカテゴリーが見つかりませんでした:', ['analysis' => $analysis]);
        return null;
    }




    /**
     * 楽天レシピカテゴリー別ランキングを取得
     *
     * @param  int  $matcheCategoryId
     * @return array
     */
    private function fetchRakutenCategoryRanking($matchedCategory): array
    {
        // 楽天API URL
        $rakutenApiUrl = "https://app.rakuten.co.jp/services/api/Recipe/CategoryRanking/20170426?format=json&applicationId=" . env('RAKUTEN_APPLICATION_ID') . "&categoryId={$matchedCategory}";
        // 楽天APIからデータを取得
        $response = Http::get($rakutenApiUrl);

        // エラーチェック
        if ($response->failed()) {
            Log::error('楽天APIエラー: ' . $response->status());
            return ['error' => '楽天APIの呼び出しに失敗しました。'];
        }
        // レスポンスからランキングデータを取得

        $responseData = $response->json();
        $categoryRanking = collect($responseData['result'])->map(function ($recipe) {
            return [
                'title' => $recipe['recipeTitle'],
                'image' => $recipe['foodImageUrl'],
                'url' => $recipe['recipeUrl'],
            ];
        });

        // コレクションを配列形式に変換して返す
        return $categoryRanking->toArray();


    }

/**
     * レシピを材料と手順に分離
     */
    private function parseRecipe(string $content): array
    {
        // テキストを行ごとに分割
        $lines = explode("\n", $content);

        $materials = [];
        $steps = [];
        $currentSection = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue; // 空行をスキップ
            }

            // セクションの判別
            if ($this->isSectionStart($line, $currentSection)) {
                continue;
            }

            // セクション内容の解析
            if ($currentSection === 'materials') {
                $this->addMaterial($line, $materials);
            } elseif ($currentSection === 'steps') {
                $this->addStep($line, $steps);
            }
        }

        // 必要ならデフォルト値を設定
        $this->setDefaultValues($materials, $steps);

        return [
            'materials' => $materials,
            'steps' => $steps,
        ];
    }

    // セクションの開始を判別し、現在のセクションを設定する
    private function isSectionStart(string $line, ?string &$currentSection): bool
    {
        if (preg_match('/#\s*材料/u', $line)) {
            $currentSection = 'materials';
            return true;
        }

        if (preg_match('/#\s*手順/u', $line)) {
            $currentSection = 'steps';
            return true;
        }

        return false;
    }

    // 材料の行を追加
    private function addMaterial(string $line, array &$materials): void
    {
        if (preg_match('/^-\s*(.+)$/u', $line, $matches)) {
            $materials[] = $matches[1];
        }
    }

    // 手順の行を追加
    private function addStep(string $line, array &$steps): void
    {
        // 手順番号を抽出
        if (preg_match('/^\s*(\d+)\.\s*(.+)$/u', $line, $matches)) {
            $step_number = $matches[1];
            $step_description = $matches[2];

            // メインの手順（例: "材料を準備する"）を追加
            $steps[] = [
                'step_number' => $step_number,
                'description' => $step_description,
                'sub_steps' => [] // サブ項目を保持するための配列
            ];
        }

        // 役割分担の行（例: - 大人が豚肉を一口大に切る）を処理
        elseif (preg_match('/^\s*-\s*(.+)$/u', $line, $roleMatches)) {
            // サブ項目として追加
            if (count($steps) > 0) {
                // 最後に追加した手順のサブ項目として追加
                $steps[count($steps) - 1]['sub_steps'][] = $roleMatches[1];
            }
        }
    }



    // デフォルト値を設定
    private function setDefaultValues(array &$materials, array &$steps): void
    {
        if (empty($materials)) {
            $materials[] = '材料情報が見つかりませんでした。';
        }

        if (empty($steps)) {
            $steps[] = ['step_number' => 1, 'description' => '手順情報が見つかりませんでした。'];
        }
    }


}
