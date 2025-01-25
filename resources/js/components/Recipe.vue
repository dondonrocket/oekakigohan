<template>
    <div class="recipe-container">
        <!-- 材料セクション -->
        <section class="materials-section">
            <h2 class="custom_h2">材料</h2>
            <ul class="materials-list">
                <li v-for="material in materials" :key="material">
                    {{ material }}
                </li>
            </ul>
        </section>

        <!-- 作り方セクション -->
        <section class="steps-section">
            <h2>作り方</h2>
            <div v-for="step in steps" :key="step.step_number" class="step">
                <div class="step-number">{{ step.step_number }}</div>
                <div class="step-description">{{ step.description }}</div>
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

<style>
.custom_h2 {
    padding: 0.4em 0.5em; /*文字の上下 左右の余白*/
    color: #494949; /*文字色*/
    background: #dfefff; /*背景色*/
    border-left: solid 5px #7db4e6; /*左線*/
    border-bottom: solid 3px #d7d7d7; /*下線*/
}
.recipe-container {
    font-family: Arial, sans-serif;
    line-height: 1.5;
}

.materials-section,
.steps-section {
    margin-bottom: 20px;
}

.materials-list {
    list-style: none;
    padding: 0;
}

.materials-list li {
    margin-bottom: 10px;
}

.step {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.step-number {
    background-color: #ffcc33;
    color: white;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 50%;
    margin-right: 10px;
}

.step-description {
    flex: 1;
}
</style>
