<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ</title>
</head>
<body>
    <h1>お問い合わせがありました</h1>
    <p><strong>お名前:</strong> {{ $name }}</p>
    <p><strong>メールアドレス:</strong> {{ $email }}</p>
    <p><strong>メッセージ:</strong></p><p> {{ $user_message }}</p>
</body>
</html>
