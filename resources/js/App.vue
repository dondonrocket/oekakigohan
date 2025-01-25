<template>
    <div id="app">
        <div v-if="isLoading" class="global-loading-overlay">
            <loader></loader>
        </div>
        <router-view></router-view>
    </div>
</template>

<script>
import Loader from "./components/Loader.vue";

export default {
    name: "App",
    components: {
        Loader,
    },
    data() {
        return {
            isLoading: false,
        };
    },
    methods: {
        startLoading() {
            this.isLoading = true;
        },
        stopLoading() {
            this.isLoading = false;
        },
    },
};
</script>

<style scoped>
.global-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
</style>
