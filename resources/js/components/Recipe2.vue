<template>
    <div class="recipe-container">
        <!-- 材料セクション -->
        <section class="materials-section custom-bg-color">
            <h2>材料</h2>
            <ul class="materials-list">
                <li v-for="material in materials" :key="material">
                    {{ material }}
                </li>
            </ul>
        </section>

        <!-- 作り方セクション -->
        <section class="steps-section custom-bg-color">
            <h2>作り方</h2>
            <div v-for="step in steps" :key="step.step_number" class="step">
                <div class="step-number">{{ step.step_number }}</div>
                <div class="step-content">
                    <div class="step-description">{{ step.description }}</div>
                    <!-- sub_stepsの表示 -->
                    <ul
                        v-if="step.sub_steps && step.sub_steps.length"
                        class="sub-steps"
                    >
                        <li
                            v-for="(subStep, index) in step.sub_steps"
                            :key="index"
                        >
                            {{ subStep }}
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: "RecipeDisplay",
    props: {
        materials: {
            type: Array,
            required: true,
        },
        steps: {
            type: Array,
            required: true,
        },
    },
};
</script>

<style scoped>
h2 {
    font-family: "Yomogi", sans-serif;
    padding: 0.4em 0.5em; /*文字の上下 左右の余白*/
    color: #494949; /*文字色*/
    background: #dfefff; /*背景色*/
    border-left: solid 5px #7db4e6; /*左線*/
    border-bottom: solid 3px #d7d7d7; /*下線*/
    margin-left: 30px;
    margin-right: 30px;
}
.recipe-container {
    font-family: Arial, sans-serif;
    line-height: 1.5;
}

.materials-section,
.steps-section {
    margin-bottom: 20px;
}

.step {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
    position: relative;
}

.step-number {
    background-color: #ffcc33;
    color: white;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 50%;
    margin-right: 10px;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
}

.step-description {
    font-weight: bold;
    margin-bottom: 5px;
}

.sub-steps {
    list-style: disc;
    margin: 10px 0 0 20px; /* インデントを追加 */
    padding-left: 20px; /* インデント調整 */
}

.sub-steps li {
    margin-bottom: 5px;
}

/* 区切り線のスタイル */
.step-divider {
    height: 1px;
    background-color: #ccc;
    margin: 15px 0;
    width: 100%;
}

.custom-bg-color {
    background-color: rgba(255, 255, 255, 0.6);
    border-radius: 50px;
    /* backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(10px); */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
    margin: 10px 10px;
    padding-bottom: 30px;
    padding-top: 20px;
}
</style>
