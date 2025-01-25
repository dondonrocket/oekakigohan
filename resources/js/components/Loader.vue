<template>
    <div class="ball-wrapper">
        <div class="ball"></div>
    </div>
    <p class="ball-p">ただ今レシピを作成中です。少しお待ちください。</p>
</template>
<style>
.ball-wrapper {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.ball {
    position: relative;
    display: inline-block;
    border-radius: 50%;
    background: #f86;
    width: 100px;
    height: 100px;
    transform-style: preserve-3d;
    transform-origin: 0 100%;
    animation: ball 1.5s infinite linear;
}

.ball-p {
    margin-top: -130px; /* ボールから十分に離す */
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    color: #333;
}

.ball::after {
    content: "";
    position: absolute;
    top: 9px;
    left: 11px;
    width: 30px;
    height: 30px;
    background: #ff9988; /* ハイライト色 */
    border-radius: 50%;
    transform: skew(-20deg, 0) translate3d(0, 5px, 1px);
    animation: ball-highlight 1.5s infinite linear;
}

.ball::before {
    content: "";
    position: absolute;
    top: 100px;
    left: 11px;
    width: 100px;
    height: 30px;
    background: rgba(0, 0, 0, 0.2); /* 影の色 */
    border-radius: 50%;
    transform: translate3d(33px, 33px, -1px) scale3d(1.25, 1.25, 1);
    animation: ball-shadow 1.5s infinite linear;
    filter: blur(1px);
}

/* アニメーション */
@keyframes ball {
    0% {
        transform: translate3d(0, 0, 0) scale3d(1, 1, 1);
        animation-timing-function: ease-in;
    }
    45% {
        transform: translate3d(0, 75px, -10px) scale3d(1, 0.95, 1);
        animation-timing-function: ease-in;
    }
    50% {
        transform: translate3d(0, 75px, -10px) scale3d(1, 0.5, 1);
        animation-timing-function: linear;
    }
    55% {
        transform: translate3d(0, 75px, -10px) scale3d(1, 1.25, 1);
        animation-timing-function: ease-out;
    }
}

@keyframes ball-highlight {
    0% {
        transform: skew(-20deg, 0) translate3d(0, 5px, 1px);
        animation-timing-function: ease-in;
    }
    45% {
        transform: skew(-30deg, 0) translate3d(0, 0, 1px);
        animation-timing-function: ease-in;
    }
    50% {
        transform: skew(-30deg, 0) translate3d(0, 0, 1px);
        animation-timing-function: linear;
    }
    55% {
        transform: skew(-30deg, 0) translate3d(0, 0, 1px);
        animation-timing-function: ease-out;
    }
    100% {
        transform: skew(-20deg, 0) translate3d(0, 5px, 1px);
        animation-timing-function: ease-in;
    }
}

@keyframes ball-shadow {
    0% {
        transform: translate3d(33px, 33px, -1px) scale3d(1.25, 1.25, 1);
        animation-timing-function: ease-in;
    }
    45% {
        transform: translate3d(25px, -9px, -1px);
        animation-timing-function: ease-in;
    }
    50% {
        transform: translate3d(25px, -9px, -1px) scale3d(1, 1, 1);
        animation-timing-function: linear;
    }
    55% {
        transform: translate3d(25px, -9px, -1px);
        animation-timing-function: ease-out;
    }
    100% {
        transform: translate3d(33px, 33px, -1px) scale3d(1.25, 1.25, 1);
        animation-timing-function: ease-in;
    }
}
</style>
