<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255', // 必須ではなく、メールアドレス形式を許可
            'message' => 'required|string',
        ]);

        // ログまたはデータベース保存（例: ログに記録する）
        Log::info('お問い合わせ', $validated);

        return response()->json(['message' => 'お問い合わせを受け付けました。'], 200);
    }

    public function sendEmail(Request $request)
    {
        // データを配列にして渡す
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'user_message' => $request->input('message'),
        ];

        Mail::send('emails.contact', $data, function ($message) use ($data, $request) {
            $message->to(env('MAIL_FROM_ADDRESS')) // 環境変数から宛先を取得
                    ->subject($request->input('name') . 'さんからお問い合わせがありました'); // 件名
        });

        return back()->with('success', 'お問い合わせありがとうございます。');
    }
}
