<template>
    <div class="image-annotator">
        <h1>子どもの絵を解析する</h1>
        <p>
            画像をアップロードして、Google Cloud Vision
            APIで解析結果を確認しましょう。
        </p>

        <!-- アップロードフォーム -->
        <form @submit.prevent="uploadImage">
            <label for="image">画像ファイルを選択してください:</label>
            <input
                type="file"
                id="image"
                @change="onFileChange"
                accept="image/*"
            />
            <button type="submit" :disabled="!imageFile">解析を実行</button>
        </form>

        <!-- 結果表示 -->
        <div v-if="results">
            <h2>解析結果</h2>
            <div v-if="results.objects.length > 0">
                <h3>検出された物体:</h3>
                <ul>
                    <li v-for="(object, index) in results.objects" :key="index">
                        {{ object.name }} (確信度:
                        {{ (object.confidence * 100).toFixed(2) }}%)
                    </li>
                </ul>
            </div>
            <div v-if="results.texts.length > 0">
                <h3>検出された文字:</h3>
                <ul>
                    <li v-for="(text, index) in results.texts" :key="index">
                        {{ text.text }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- エラー表示 -->
        <div v-if="error" class="error">
            <p>エラー: {{ error }}</p>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            imageFile: null, // アップロードされた画像
            results: null, // APIからの解析結果
            error: null, // エラーメッセージ
        };
    },
    methods: {
        // 画像ファイルを選択時にセット
        onFileChange(event) {
            this.imageFile = event.target.files[0];
        },
        // 画像をアップロードして解析を実行
        async uploadImage() {
            this.results = null;
            this.error = null;

            if (!this.imageFile) {
                this.error = "画像ファイルを選択してください。";
                return;
            }

            // フォームデータの作成
            const formData = new FormData();
            formData.append("image", this.imageFile);

            try {
                // APIリクエスト
                const response = await axios.post(
                    "/image_annotator/extract",
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );

                // ★ デバッグ用にレスポンスをログに出力 ★
                console.log("API Response:", response.data);

                if (response.data.result) {
                    this.results = response.data;
                } else {
                    this.error =
                        response.data.message || "解析に失敗しました。";
                }
            } catch (err) {
                this.error = "通信エラーが発生しました: " + err.message;
                // ★ エラー内容をコンソールに出力 ★
                console.error("Error occurred:", err);
            }
        },
    },
};
</script>

<style>
.image-annotator {
    font-family: Arial, sans-serif;
    margin: 20px;
}
label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}
button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
.error {
    color: red;
    margin-top: 20px;
}
</style>
