<template>
    <div class="description" id="recipe-result">
        <!-- 結果が未表示の場合にのみ表示 -->
        <div v-if="!hasResult">
            <div class="sp-description">
                <h2 class="description-h">
                    <span class="first-letter">子</span>どもの想像力が、<br
                        class="responsive-br"
                    />
                    <span class="first-letter">そ</span
                    >のままおいしいごはんになる！
                </h2>
            </div>
            <div class="flex-responsive">
                <div class="main_left">
                    <div class="main_image"></div>
                </div>
                <div class="main_right">
                    <div class="custom-bg-color">
                        <p class="description-p">
                            子どもの絵から親子で作れる料理レシピを作成するアプリです。<br /><br />下の"画像を選ぶ"のボタンから画像を選んだ後に、"レシピを作成する"のボタンを押して下さい。
                        </p>
                        <div class="flex">
                            <div class="flex-left">
                                <label for="fileInput" class="custom-label">
                                    <input
                                        type="file"
                                        id="fileInput"
                                        @change="handleFileUpload"
                                        name="file"
                                        style="display: none"
                                    />
                                    1.画像を選ぶ
                                </label>
                            </div>
                            <div class="flex-right">
                                <button
                                    class="btn-circle-stitch"
                                    @click="analyzeImage"
                                >
                                    2.レシピを<br class="responsive-br" />作る
                                </button>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="flex-left custom_center">
                                <span class="fileName" id="fileName">{{
                                    fileName
                                }}</span>
                            </div>
                            <div class="flex-right"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 解析結果の表示 -->
        <div v-else>
            <div id="display_content">
                <div v-if="analysis">
                    <p class="analysis">{{ analysis }}</p>
                </div>
                <div v-if="recipe">
                    <div>
                        <!-- Swiperコンポーネントを使用 -->
                        <Swiper :categoryRanking="categoryRanking" />
                        <!-- <img v-bind:src="categoryRanking[0].image" alt="Image" /> -->
                    </div>

                    <p style="margin-left: 10px; margin-right: 10px">
                        ※上の画像は参考資料です。楽天レシピへのリンクになっています。こっちのレシピでも美味しい料理が作れます♪
                    </p>

                    <!-- Recipe コンポーネントを使用 -->
                    <Recipe
                        :materials="recipe.materials"
                        :steps="recipe.steps"
                    />
                    <!-- 画像保存ボタン -->
                    <div class="button-container">
                        <button
                            type="button"
                            @touchstart="saveAsImage('recipe-result')"
                            @click="() => saveAsImage('recipe-result')"
                            class="save-button"
                        >
                            レシピを保存
                        </button>
                    </div>
                </div>
            </div>
            <div id="capture_content" style="display: none">
                <div v-if="analysis">
                    <p class="analysis">{{ analysis }}</p>
                </div>
                <div v-if="recipe">
                    <div class="imagePath">
                        <img :src="imagePath" alt="Uploaded Image" />
                    </div>

                    <!-- Recipe コンポーネントを使用 -->
                    <Recipe
                        :materials="recipe.materials"
                        :steps="recipe.steps"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Swiper from "./Swiper.vue";
import Recipe from "./Recipe2.vue";

import html2canvas from "html2canvas";

import Swal from "sweetalert2"; // SweetAlert2をインポート

export default {
    name: "ImageAnalyzer",
    components: {
        Swiper, // Swiper コンポーネントを登録
        Recipe, // Recipe コンポーネントを登録
    },
    data() {
        return {
            file: null, // アップロードされた画像ファイル
            analysis: null, // 解析結果
            recipe: null, // レシピ結果
            matched_category: null,
            categoryRanking: [],
            errorMessage: null, // エラーメッセージ

            imagePath: null, // 初期状態は画像がない

            fileName: "画像の名前がここに表示されます。", // 初期表示
        };
    },
    computed: {
        hasResult() {
            return this.analysis !== null; // 解析結果がある場合true
        },
    },
    methods: {
        // 画像ファイルが選択された時に呼ばれるメソッド
        handleFileUpload(event) {
            this.file = event.target.files[0]; // 最初のファイルを取得
            if (this.file) {
                this.fileName = this.file.name; // ファイル名を表示用に更新
            } else {
                this.fileName = "選択されたファイル名がここに表示されます";
            }
            this.analysis = null; // 新しい画像選択時に解析結果をクリア
            this.recipe = null;
            this.matched_category = null;
            this.categoryRanking = [];
            this.imagePath = null;
            this.errorMessage = null; // エラーメッセージをリセット
        },

        // 画像解析を実行するメソッド
        async analyzeImage() {
            if (!this.file) {
                this.errorMessage = "画像を選択してください。";
                return;
            }

            this.$root.startLoading(); // ローディング開始

            const formData = new FormData();
            formData.append("image", this.file); // フォームデータに画像を追加

            for (let pair of formData.entries()) {
                if (pair[1] instanceof File) {
                    console.log(`${pair[0]}: File name - ${pair[1].name}`);
                } else {
                    console.log(`${pair[0]}: ${pair[1]}`);
                }
            }

            try {
                const response = await axios.post(
                    "https://oekakigohan.com/api/analyze-image",
                    formData,
                    { headers: { "Content-Type": "multipart/form-data" } } // 重要
                );

                // レスポンス全体をコンソールに出力
                console.log("API Response:", response);

                // レスポンスデータ部分を出力
                console.log("Response Data:", response.data);

                // 解析結果が返された場合
                if (response.data && response.data.analysis) {
                    this.analysis = response.data.analysis;
                    this.recipe = response.data.recipe; // レシピを保存
                    this.matched_category = response.data.matched_category;
                    this.categoryRanking = response.data.categoryRanking;
                    this.errorMessage = null; // エラーメッセージをリセット
                } else {
                    this.errorMessage = "解析結果がありません。";
                    return; // セッション取得は行わない
                }
                // セッションから画像パスを取得
                // サーバーに画像を送信してセッションに保存
                const sessionResponse = await axios.post(
                    "/session/set-image",
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );
                console.log(
                    "セッション保存成功:",
                    sessionResponse.data.image_path
                );
                // 保存した画像のパスをVueのデータに保存
                this.imagePath = sessionResponse.data.image_path;
            } catch (error) {
                // エラーが発生した場合の処理
                this.analysis = null; // 解析結果をクリア
                this.recipe = null;
                this.matche_category = null;
                this.categoryRanking = [];
                this.imagePath = null;
                // エラー応答がある場合は、そのエラーメッセージを表示
                if (error.response && error.response.data) {
                    const errorMessage = error.response.data.error;
                    this.showErrorMessage(errorMessage);
                } else {
                    this.errorMessage = "サーバーとの通信に失敗しました。";
                }
            } finally {
                this.$root.stopLoading(); // ローディング終了
            }
        },
        async saveAsImage(targetId = "recipe-result") {
            try {
                const target = document.getElementById(targetId);
                if (!target) {
                    console.error(`ID "${targetId}" の要素が見つかりません。`);
                    Swal.fire({
                        icon: "error",
                        title: "エラー",
                        text: "保存対象が表示されていません。",
                        confirmButtonText: "OK",
                    });
                    return;
                }

                const metaViewport = document.querySelector(
                    'meta[name="viewport"]'
                );
                const originalViewportContent = metaViewport
                    ? metaViewport.content
                    : "";
                if (metaViewport) {
                    metaViewport.content = "width=1200";
                }

                const captureElement =
                    document.getElementById("capture_content");
                const displayElement =
                    document.getElementById("display_content");

                if (!captureElement) {
                    console.error("キャプチャ用の要素が見つかりません。");
                    return;
                }

                captureElement.style.display = "block";
                displayElement.style.display = "none";

                await new Promise((resolve) => setTimeout(resolve, 100));

                const canvas = await html2canvas(target, {
                    allowTaint: true,
                    useCORS: true,
                    scale: 2,
                });

                if (metaViewport) {
                    metaViewport.content = originalViewportContent;
                }

                const imageData = canvas.toDataURL("image/png");

                const isMobile = /iPhone|iPad|iPod|Android/i.test(
                    navigator.userAgent
                );

                if (isMobile) {
                    try {
                        Swal.fire({
                            icon: "info",
                            title: "画像の保存方法",
                            text: "画像を長押しして保存してください。",
                            confirmButtonText: "OK",
                        }).then(() => {
                            const newTab = window.open();

                            if (!newTab) {
                                Swal.fire({
                                    icon: "error",
                                    title: "エラー",
                                    text: "ポップアップブロックにより、新しいタブを開けませんでした。",
                                    confirmButtonText: "OK",
                                });
                                return;
                            }

                            newTab.document.write(
                                `<img src="${imageData}" style="width:100%">`
                            );
                            newTab.focus();
                        });

                        return;
                    } catch (err) {
                        console.error("新しいタブを開けませんでした:", err);
                        Swal.fire({
                            icon: "error",
                            title: "エラー",
                            text: "予期しないエラーが発生しました。",
                            confirmButtonText: "OK",
                        });
                    }
                }

                const link = document.createElement("a");
                link.download = `${targetId}.png`;
                link.href = imageData;

                document.body.appendChild(link);
                setTimeout(() => {
                    link.click();
                    document.body.removeChild(link);
                }, 100);
            } catch (error) {
                console.error("画像の保存処理中にエラーが発生しました:", error);
                Swal.fire({
                    icon: "error",
                    title: "エラー",
                    text: "画像の保存に失敗しました。",
                    confirmButtonText: "OK",
                });
            } finally {
                // **エラーの有無に関係なく実行**
                const captureElement =
                    document.getElementById("capture_content");
                const displayElement =
                    document.getElementById("display_content");

                if (captureElement && displayElement) {
                    captureElement.style.display = "none";
                    displayElement.style.display = "block";
                }

                // **セッションデータ削除**
                await this.deleteImageSession();
            }
        },

        showErrorMessage(message) {
            Swal.fire({
                title: "エラー",
                text: "ごめんなさい。レシピを作れませんでした。もう一度お試し下さい。",
                icon: "error",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // 例: ページをリロードする
                }
            });
        },

        // セッションから画像情報を削除するメソッド
        async deleteImageSession() {
            try {
                const response = await fetch("/session/delete-image", {
                    method: "GET", // GETリクエスト
                });

                if (response.ok) {
                    console.log("セッションから画像情報を削除しました");
                } else {
                    console.error("セッション削除に失敗しました");
                    alert("セッション削除に失敗しました。");
                }
            } catch (error) {
                console.error("セッション削除中にエラーが発生しました:", error);
                alert("セッション削除中にエラーが発生しました。");
            }
        },
    },
    mounted() {},
};
</script>
<style scoped>
/* デフォルトでは非表示 */
.responsive-br {
    display: none;
}

.main_image {
    display: block; /* 画像をブロック要素に変更 */
    margin-right: auto;
    width: auto;
    height: 100%;
    background-image: url("/images/main_image.png");
    background-size: cover;
    background-repeat: no-repeat;
}

.main_left {
    width: 80%;
    height: auto;
}

.main_right {
    margin-bottom: 100px;
}

label {
    display: inline-block;
    text-decoration: none;
    background: #87befd;
    color: #fff;
    width: 120px;
    height: 120px;
    line-height: 120px;
    border-radius: 50%;
    text-align: center;
    overflow: hidden;
    box-shadow: 0px 0px 0px 5px #87befd;
    border: dashed 1px #fff;
    transition: 0.4s;
}
label:hover {
    background: #668ad8;
    box-shadow: 0px 0px 0px 5px #668ad8;
}

#fileName {
    width: auto;
    margin-left: auto;
    margin-right: auto;
    font-size: 14px;
    color: #555;
}

.sp-description {
    position: relative;
}

.description {
    padding-bottom: 50px;
    padding-top: 20px;
    width: 100%;
    height: 100%;
    background-image: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1)),
        /* 黒いオーバーレイ */ url("/images/top_image.png");
    background-size: cover;
    background-repeat: no-repeat;
}

.description-h {
    position: relative;
    text-align: center;
    width: 90%;
    margin-top: 10px;
    margin-bottom: 20px;
    margin-left: auto;
    margin-right: auto;
    /*   background: #2190ff;
    box-shadow: 0px 0px 0px 5px #2190ff;
    border: dashed 2px white;*/
    padding: 0.2em 0.5em;
    font-size: 2.5rem;
    letter-spacing: 0.2em;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    font-weight: bold;
    color: #fafafa;
}

.first-letter {
    font-size: 110%;
    color: rgb(0, 235, 12);
}

.description-p {
    text-align: left;
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 50px;
    padding-left: 20px;
    padding-right: 20px;
}

/* 背景の透過効果 */
.custom-bg-color {
    background-color: rgba(255, 255, 255, 0.6);
    border-radius: 2.5%;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
    margin: 10px 10px;
    padding-bottom: 30px;
    padding-top: 20px;
}

.btn-circle-stitch {
    display: inline-block;
    text-decoration: none;
    background: #87befd;
    color: #fff;
    width: 125px;
    height: 125px;
    border-radius: 50%;
    text-align: center;
    overflow: hidden;
    box-shadow: 0px 0px 0px 5px #87befd;
    border: dashed 1px #fff;
    transition: 0.4s;
}

.btn-circle-stitch:hover {
    background: #668ad8;
    box-shadow: 0px 0px 0px 5px #668ad8;
}

.flex {
    display: flex;
}

.flex-responsive {
    display: flex;
}

.flex-left {
    /* margin-left: auto; */
    margin-left: 20px;
    margin-right: auto;
    display: flex; /* Flexbox を適用 */
    flex-direction: column; /* 縦方向に並べる */
}

.flex-right {
    margin-right: auto;
    margin-left: auto;
}

.custom_center {
    text-align: center;
}

.fileName {
    display: inline-block; /* サイズを指定するために必要 */
    background-color: rgba(255, 255, 255, 0.8); /* 半透明の白背景 */
    backdrop-filter: blur(10px); /* 背景をぼかす */
    -webkit-backdrop-filter: blur(10px); /* Safari用 */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); /* 影を追加 */
    border-radius: 12px; /* 角を丸める */
    margin-top: 20px;
    padding: 8px 12px; /* 内側の余白 */
    font-size: 14px; /* フォントサイズ調整 */
    color: #333; /* テキストの色 */
    border: 1px solid rgba(255, 255, 255, 0.6); /* 薄い白の枠線を追加 */
    max-width: 250px; /* 必須: 幅を指定 (必要に応じて調整) */
    white-space: nowrap; /* テキストを1行で表示 */
    overflow: hidden; /* はみ出したテキストを非表示 */
    text-overflow: ellipsis; /* 省略記号 (...) を表示 */
}

.errorMessage {
    margin-top: 20px;
    text-align: center;
}

.save-button {
    background-color: #4caf50; /* ベースカラー: 緑 */
    color: white; /* テキストカラー */
    font-size: 16px; /* フォントサイズ */
    font-weight: bold; /* 太字 */
    padding: 10px 20px; /* パディング */
    border: none; /* ボーダーなし */
    border-radius: 8px; /* 丸みを付ける */
    cursor: pointer; /* ポインターを表示 */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* シャドウ */
    transition: all 0.3s ease; /* ホバーアニメーション */
    margin-top: 20px; /* マージン */
    margin-left: auto;
    margin-right: auto;
}

.save-button:hover {
    background-color: #45a049; /* ホバー時の色 */
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); /* シャドウを拡大 */
    transform: translateY(-2px); /* 少し浮かせる */
}

.save-button:active {
    background-color: #3e8e41; /* クリック時の色 */
    transform: translateY(1px); /* 押し込むような効果 */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* シャドウを縮小 */
}

.button-container {
    display: flex; /* フレックスボックスを使用 */
    justify-content: center; /* 水平方向の中央寄せ */
    align-items: center; /* 垂直方向の中央寄せ（必要に応じて） */
    margin-top: 20px; /* 親要素に余白を設定 */
}

/* メディアクエリで画面幅に応じた調整 */
@media (max-width: 1000px) {
    .sp-description {
        position: relative;
    }

    .description-h {
        position: relative;
        font-size: 1.8rem; /* タイトル文字を小さくする */
    }
    .flex-responsive {
        display: grid;
    }
    .main_left {
        width: 50%;
        /* height: 71vh; */
        margin-left: auto;
        margin-right: auto;
    }

    .main_image {
        width: auto;
        height: 300px;
    }
}

/* メディアクエリで画面幅に応じた調整 */
@media (max-width: 768px) {
    .sp-description {
        position: relative;
    }

    .description-h {
        position: relative;
        font-size: 1.5rem; /* タイトル文字を小さくする */
        margin-top: 20px;
    }
    .responsive-br {
        display: inline;
    }
    .flex-responsive {
        display: grid;
    }

    .main_left {
        width: 60%;
    }
    .save-button {
        font-size: 14px;
        padding: 8px 16px;
    }
}

@media (max-width: 635px) {
    .sp-description {
        position: relative;
    }

    .description-h {
        position: relative;
    }

    .main_left {
        width: 75%;
    }
}

@media (max-width: 480px) {
    .sp-description {
        position: relative;
    }

    .description-h {
        position: absolute;
        left: 30px;
        font-size: 1.3rem; /* タイトル文字を小さくする */
        margin-top: 10px;
        margin-bottom: 20px;
    }
    .description-p {
        font-size: 16px;
        font-weight: normal;
        margin-bottom: 20px;
        text-align: left;
    }
    .responsive-br {
        display: inline;
    }

    .btn-circle-stitch {
        width: 100px;
        height: 100px;
    }

    label {
        width: 100px;
        height: 100px;
        line-height: 100px;
    }

    .main_left {
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    .flex-responsive {
        display: grid;
    }
}

/*検索結果のcss*/
.analysis {
    font-family: "Yomogi", sans-serif;
    text-align: center;
    margin-bottom: 50px;
    background: #dfefff;
    box-shadow: 0px 0px 0px 5px #dfefff;
    border: dashed 2px white;
    padding: 0.2em 0.5em;
    font-size: 2.5rem;
    color: #004080; /* 鮮やかな濃い青色 */
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2),
        /* 薄い黒い影 */ 0px 0px 10px #82b1ff; /* 青白い輝きのような影 */
    font-weight: bold; /* 文字を太くする */
    letter-spacing: 0.05em; /* 少し文字間を広げる */
}

.imagePath {
    margin-left: auto;
    margin-right: auto;
    width: 100%; /* 親要素の幅を自由に調整 */
    height: 200px; /* 親要素の高さを固定 */
    overflow: hidden; /* はみ出た部分を隠す */
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: white;
}

.imagePath img {
    width: auto; /* 親要素に合わせて幅を調整 */
    height: 100%; /* 親要素に合わせて高さを調整 */
    object-fit: contain; /* 画像全体が収まるようリサイズ */
    object-position: center; /* 画像を中央揃え */
}
</style>
