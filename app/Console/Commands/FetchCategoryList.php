<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchCategoryList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-category-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches category list from Rakuten API and saves it to a JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
               // Rakuten APIのURL、RAKUTEN_APPLICATION_IDをenv()で取得
        $applicationId = env('RAKUTEN_APPLICATION_ID');
        $apiUrl = 'https://app.rakuten.co.jp/services/api/Recipe/CategoryList/20170426?format=json&applicationId=' . $applicationId;

        // APIにリクエストを送信
        $response = Http::get($apiUrl);

        // レスポンスが正常かどうかを確認
        if ($response->successful()) {
            // JSONデータを取得
            $categories = $response->json();

            // categories.json ファイルにデータを保存
            Storage::disk('local')->put('categories.json', json_encode($categories, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE));

            // 成功メッセージ
            $this->info('カテゴリー一覧を更新しました。');
        } else {
            // エラーメッセージ
            $this->error('カテゴリー一覧の取得に失敗しました。');
        }
    }
}
