<template>
    <div class="custom_swiper">
        <!-- Swiper スライダー -->
        <swiper
            :slides-per-view="3"
            :space-between="calculatedSpaceBetween"
            loop
            @slideChange="handleSlideChange"
        >
            <swiper-slide v-for="(item, index) in categoryRanking" :key="index">
                <img
                    :src="item.image"
                    alt="Image"
                    class="swiper-image"
                    @click="goToUrl(item.url)"
                />
            </swiper-slide>
        </swiper>

        <!-- ドットインジケーター -->
        <div class="dot-indicators">
            <span
                v-for="(item, index) in categoryRanking"
                :key="index"
                :class="{ active: activeIndex === index }"
            ></span>
        </div>
    </div>
</template>

<script>
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/swiper-bundle.css";

export default {
    name: "SwiperComponent",
    components: {
        Swiper,
        SwiperSlide,
    },
    data() {
        return {
            screenWidth: window.innerWidth, // 現在の画面幅
            activeIndex: 0, // 現在アクティブなスライドのインデックス
            calculatedSpaceBetween: window.innerWidth * 0.05, // 初期値
        };
    },
    props: {
        categoryRanking: {
            type: Array,
            required: true,
        },
    },
    mounted() {
        this.updateSpaceBetween(); // 初期値を計算
        window.addEventListener("resize", this.updateSpaceBetween);
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.updateSpaceBetween);
    },
    methods: {
        updateSpaceBetween() {
            // 画面幅変更時に `spaceBetween` を再計算
            this.screenWidth = window.innerWidth;
            this.calculatedSpaceBetween = this.screenWidth * 0.05;
        },
        handleSlideChange(swiper) {
            // 現在のスライドインデックスを更新
            this.activeIndex = swiper.realIndex;
        },
        goToUrl(url) {
            window.location.href = url; // URL に遷移
        },
    },
};
</script>

<style scoped>
.custom_swiper {
    margin-left: 10px;
    margin-right: 10px;
}
.swiper-slide img.swiper-image {
    object-fit: cover;
    max-height: 50vh;
    width: 100%;
    height: auto;
    border-radius: 10px; /* 角を少し丸める */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06); /* 影を追加 */
    transition: transform 0.3s, box-shadow 0.3s;
}

.swiper-slide img.swiper-image:hover {
    transform: translateY(-5px); /* ホバー時に少し浮かせる */
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2), 0 4px 6px rgba(0, 0, 0, 0.1); /* 影を強調 */
}

.dot-indicators {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

.dot-indicators span {
    width: 10px;
    height: 10px;
    margin: 0 5px;
    border-radius: 50%;
    background-color: gray;
    transition: background-color 0.3s;
}

.dot-indicators span.active {
    background-color: blue; /* アクティブなスライドのインジケーター色 */
}
</style>
