<template>
    <div
        class="bg-center bg-cover"
        :style="{
          backgroundImage: `url(${getImageUrl(prop.bgImage)})`
        }"
    >
        <div class="w-[1160px] mx-auto title">
            <div class="flex justify-between items-center">
                <div class="flex flex-col items-stretch max-w-[610px] pt-[80px] pb-[40px]">
                    <h1 class="font-medium text-[45px]">
                        {{ prop.title }}
                    </h1>
                    <p class="max-w-[850px] text-left text-lg mt-[40px]">
                        {{ prop.desc }}
                    </p>
                    <div v-if="prop.isShowBtn" class="mt-[40px] flex">
                        <div>
                            <ElButton type="primary" class="enter-btn" size="large">
                                <div class="flex justify-center items-center w-[50px] h-[50px] rounded-full bg-white">
                                    <img src="../../../image/index_arrow-right02.png" class="w-[24px] h-[24px]" alt="">
                                </div>
                                <span class="ml-4">{{ prop.btnText }}</span>
                            </ElButton>
                        </div>
                    </div>
                </div>
                <div>
                    <img :src="getImageUrl(prop.rightImage)" class="w-[600px]" alt="">
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { Prop } from './config'
import useAppStore from "@/stores/modules/app";
const props = defineProps<{
    prop: Prop
}>()
const { getImageUrl } = useAppStore()
</script>

<style lang="scss" scoped>
.enter-btn {
    --el-button-size: 60px;
    --el-font-size-base: 18px;
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
    border: none;
    padding: 20px 25px 20px 8px;
    border-radius: 30px;
}
</style>
