<template>
    <div class="main-image">
        <div class="contact-form">
            <h1>お問い合わせ</h1>
            <p v-if="successMessage" class="success">{{ successMessage }}</p>
            <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
            <form @submit.prevent="submitForm">
                <div>
                    <label for="name">
                        お名前: <span class="required">必須</span>
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="氏名を記入して下さい"
                    />
                </div>
                <div>
                    <label for="email">メールアドレス:</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="メールアドレスを記入して下さい。"
                    />
                </div>
                <div>
                    <label for="message">
                        お問い合わせ内容: <span class="required">必須</span>
                    </label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        required
                        placeholder="お問い合わせ内容をここに記入して下さい"
                    ></textarea>
                </div>
                <!-- フォームの入力項目など -->
                <button type="submit">送信</button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            form: {
                name: "",
                email: "",
                message: "",
            },
            successMessage: "",
            errorMessage: "",
        };
    },
    methods: {
        async submitForm() {
            try {
                await axios.post("/api/contact", this.form);
                this.successMessage =
                    "送信が完了しました。お問い合わせありがとうございました！";
                this.errorMessage = "";
                this.form = { name: "", email: "", message: "" }; // フォームをリセット
            } catch (error) {
                console.error(error);
                this.errorMessage =
                    "送信に失敗しました。もう一度お試しください。";
                this.successMessage = "";
            }
        },
    },
};
</script>

<style scoped>
.main-image {
    padding-bottom: 50px;
    padding-top: 20px;
    width: 100%;
    height: 100%;
    background-image: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1)),
        /* 黒いオーバーレイ */ url("/images/top_image.png");
    background-size: cover;
    background-repeat: no-repeat;
}

.contact-form {
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    border-radius: 10px;
    background-color: #f9f9f9;
}
.contact-form label {
    display: block;
    margin-bottom: 5px;
}
.contact-form input,
.contact-form textarea {
    width: 100%;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 8px; /* 丸みを追加 */
    border: 1px solid #ddd; /* デフォルトのボーダー */
    box-sizing: border-box;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}
.contact-form input:focus,
.contact-form textarea:focus {
    border-color: #007bff; /* フォーカス時に青色のボーダー */
    outline: none; /* デフォルトのアウトラインを無効化 */
}
.contact-form button {
    width: 100%;
    padding: 12px;
    border: none;
    background-color: #007bff;
    color: white;
    font-size: 1rem;
    border-radius: 8px; /* 丸みを追加 */
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.contact-form button:hover {
    background-color: #0056b3; /* ホバー時に濃い青色 */
}
.contact-form button:focus {
    outline: none; /* ボタンのフォーカス時にアウトラインを無効化 */
}
.required {
    color: white;
    background-color: red;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8rem;
    margin-left: 5px;
}

.success {
    color: green;
    font-weight: bold;
    margin-top: 10px;
}

.error {
    color: red;
    font-weight: bold;
    margin-top: 10px;
}
</style>
