import "./bootstrap"; // Laravel用の設定やライブラリの読み込み
import { createApp } from "vue";
import App from "./App.vue";
import AnalyzeImage from "./components/AnalyzeImage.vue";

// Vueアプリケーションの作成
const app = createApp(App);

// コンポーネントの登録
app.component("analyze-image", AnalyzeImage);

// マウント
app.mount("#app");
