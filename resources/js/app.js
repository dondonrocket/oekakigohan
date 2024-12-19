import "./bootstrap"; // Laravel用の設定やライブラリの読み込み
import { createApp } from "vue";
import App from "./App.vue";
import ImageAnnotator from "./components/ImageAnnotator.vue";

// Vueアプリケーションの作成
const app = createApp(App);

// コンポーネントの登録
app.component("image-annotator", ImageAnnotator); // スペースを削除

// マウント
app.mount("#app");
