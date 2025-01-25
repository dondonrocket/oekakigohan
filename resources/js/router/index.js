import { createRouter, createWebHistory } from "vue-router";
import ImageAnalyzer from "../components/ImageAnalyzer.vue";
import ContactForm from "../components/ContactForm.vue";

const routes = [
    { path: "/", component: ImageAnalyzer }, // ホームページで ImageAnalyzer を表示
    { path: "/contact", component: ContactForm }, // お問い合わせページで ContactForm を表示
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
