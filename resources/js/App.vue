<template>
    <div>
        <!-- image-annotator コンポーネントを使用 -->
        <image-annotator></image-annotator>
    </div>
</template>

<script>
// ImageAnnotator.vue をインポート
import ImageAnnotator from "./components/ImageAnnotator.vue";

export default {
    name: "App",
    components: {
        ImageAnnotator, // コンポーネントの登録
    },
};
</script>
