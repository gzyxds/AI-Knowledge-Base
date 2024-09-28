<template>
    <div
        class="bg-center home-title mb-[30px]"
        :style="{
          height: `${height}px`,
          paddingTop: 'var(--header-height)',
          backgroundImage: `url(${getImageUrl(prop.bgImage)})`
        }"
    >
        <div class="max-w-[1200px] mx-auto h-full flex flex-col justify-center">
            <div class="flex justify-between items-center">
                <div
                    class="flex flex-col items-stretch justify-center h-full sm:py-[80px] py-[30px] mx-[20px]"
                >
                    <h1
                        v-if="prop.title"
                        class="font-medium sm:text-[45px] text-[30px] text-left"
                    >
                        <typing :textTips="prop.title"></typing>
                    </h1>
                    <h1 class="hidden">{{ prop.title }}</h1>
                    <p
                        v-if="prop.desc"
                        class="max-w-[610px] text-left text-lg sm:my-[40px] my-[20px]"
                    >
                        {{ prop.desc }}
                    </p>
                    <div>
                        <div v-if="prop.isShowBtn">
                            <AppLink
                                :to="{
                                  path: prop.link?.path,
                                  query: prop.link?.query
                                }"
                            >
                                <ElButton
                                    type="primary"
                                    class="enter-btn hover-to-right"
                                    size="large"
                                >
                                    <div
                                        class="flex justify-center items-center w-[50px] h-[50px] rounded-full bg-white"
                                    >
                                        <img
                                            src="~/assets/image/index_arrow-right02.png"
                                            class="w-[24px] h-[24px] round-btn"
                                            alt=""
                                        />
                                    </div>
                                    <span class="ml-4">{{ prop.btnText }}</span>
                                </ElButton>
                            </AppLink>
                        </div>
                    </div>
                </div>

                <div class="flex-none">
                    <img :src="getImageUrl(prop.rightImage)" class="w-[600px]" alt=""/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {useAppStore} from '~/stores/app'

const props = defineProps<{
    prop: Record<string, any>
}>()
const {getImageUrl} = useAppStore()

// 创建一个响应式变量来存储高度值
const height = ref(510)

// 根据屏幕宽度设置高度的函数
const setHeight = () => {
    // 这里是一个示例，假设宽度和高度的比例是16:9
    const aspectRatio = 6 / 2
     const result = window.innerWidth / aspectRatio
     if (result < 500) return
     height.value = result
}

 // 监听浏览器窗口大小改变事件
onMounted(() => {
     window?.addEventListener('resize', setHeight)
})

 // 取消监听事件
onUnmounted(() => {
     window?.removeEventListener('resize', setHeight)
})

// 使用watchEffect来立即触发一次高度计算，并且在响应式依赖变化时重新计算
if (process.client) {
    watchEffect(setHeight)
}
</script>

<style lang="scss" scoped>
.home-title {
    background-size: 100% auto;
}

.enter-btn {
    --el-button-size: 60px;
    --el-font-size-base: 18px;
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
    border: none;
    padding: 20px 25px 20px 8px;
    border-radius: 30px;
}

.round-btn {
    transition: margin-left 0.5s;
}

.enter-btn:hover .round-btn {
    margin-left: 10px;
}
</style>
