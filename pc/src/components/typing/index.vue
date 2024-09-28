<template>
    <div class="typer inline-block">
        <div class="typer-content inline-block">
            <!-- 动态变化的内容-->
            <p class="typer-dynamic">
                <span class="cut">
                    <span
                        class="word"
                        v-for="(letter, index) in words"
                        :key="index"
                        >{{ letter }}</span
                    >
                    <!-- 模拟光标-->
                <span class="typer-cursor"></span>
                </span>
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'

// Define the props using defineProps method
const props = defineProps({
    color: {
        type: String,
        default: '#000000'
    },
    textTips: {
        type: [Array, Object],
        default: () => []
    }
})

const words = ref([])
const str = ref<any>('AI智能聊天系统、AI绘画、大模型知识库训练开发')
const letters = ref([])
const order = ref(0)

// Watcher for order changes
// watch(order, (currentOrder) => {
//     console.log('order')
//     str.value = props.textTips[currentOrder];
// });

// Watcher for textTips changes
watch(
    () => props.textTips,
    (newTips) => {
        console.log('textTips')
        str.value = newTips
    },
    {
        immediate: true
    }
)

// Mount logic
onMounted(() => {
    begin()
})

//开始输入的效果动画
const begin = () => {
    letters.value = str.value.split('')
    // this.write(letters.value[0])
    for (let i = 0; i < letters.value.length; i++) {
        setTimeout(write(i), i * 200)
    }
}
//开始删除的效果动画
const back = () => {
    const L = letters.value.length
    for (let i = 0; i < L; i++) {
        setTimeout(wipe(i), i * 100)
    }
}
//输入字母
const write = (i) => {
    return () => {
        const L = letters.value.length
        words.value.push(letters.value[i])
        /*如果输入完毕，在2s后开始删除*/
        if (i == L - 1) {
            setTimeout(back, 1500)
        }
    }
}
//擦掉(删除)字母
const wipe = (i) => {
    return () => {
        words.value.pop(letters.value[i])
        /*如果删除完毕，在300ms后开始输入*/
        if (words.value.length == 0) {
            order.value >= props.textTips.length - 1
                ? (order.value = 0)
                : order.value++
            setTimeout(begin, 500)
        }
    }
}
</script>

<style scoped lang="scss">
// #FFFFFF:#e84d49;
.typer {
    margin-top: 2%;
    box-sizing: border-box;
    @media screen and (max-width: 768px) {
        width: 355px;
    }
}
.typer .typer-content {
    // font-weight: bold;
    font-size: 36px;

    @media screen and (max-width: 768px) {
        font-size: 26px;
        height: 2em;
    }
    display: flex;
    flex-direction: row;
    letter-spacing: 2px;
    justify-content: center;
    //height: 1.3em;
}
.typer-dynamic {
    @media screen and (max-width: 768px) {
        width: 355px;
    }
}
.cut {
    position: relative;
    // color: #ffffff;
}
.typer-cursor {
    position: absolute;
    right: -10px;
    width: 3px;
    height: 28px;
    bottom: 0;
    transform: translateY(-40%);
    background: #333;
    -webkit-animation: blink 0.7s infinite;
    -moz-animation: blink 0.7s infinite;
    animation: blink 0.7s infinite;
}

@keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
@-webkit-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
@-moz-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
</style>
