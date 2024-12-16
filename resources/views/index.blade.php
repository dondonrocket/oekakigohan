<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>子どもの絵を解析</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>子どもの絵を解析する</h1>
    <p>画像をアップロードして、Google Cloud Vision APIで解析結果を確認しましょう。</p>

    <!-- アップロードフォーム -->
    <form action="/image_annotator/extract" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="image">画像ファイルを選択してください:</label>
        <input type="file" name="image" id="image" accept="image/*" required>
        <button type="submit">解析を実行</button>
    </form>
</body>
</html>
