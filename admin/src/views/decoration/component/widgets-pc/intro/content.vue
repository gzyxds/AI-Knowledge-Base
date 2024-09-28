<template>
    <div class="py-[40px]">
        <div class="w-[1160px] mx-auto">
            <div class="mb-[20px]" v-for="(item, index) in getShowData" :key="index">
                <div
                    class="flex flex-col items-center"
                >
                    <div class="flex-1 my-[30px] px-[20px]">
                        <div class="flex justify-center text-center">
                            <div>
                                <div class="text-[24px] font-bold">{{ item.title }}</div>
                                <div class="mt-[20px] text-[16px]">{{ item.subtitle }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 px-[20px]">
                        <div class="flex justify-center">
                            <ElImage
                                class="w-[1200px]"
                                fit="cover"
                                :src="getImageUrl(item.image)"
                            />
                        </div>
                    </div>
                    <div class="mt-[90px]">
                        <NuxtLink :to="item.link?.path">
                            <ElButton
                                type="primary"
                                class="enter-btn hover-to-right"
                                size="large"
                            >
                                <div class="flex justify-center items-center w-[50px] h-[50px] rounded-full bg-white">
                                    <img src="../../../image/index_arrow-right02.png" class="w-[24px] h-[24px]" alt="">
                                </div>
                                <span class="ml-4">马上体验</span>
                            </ElButton>
                        </NuxtLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { Prop } from './config'
import DecorationImg from '../../decoration-img.vue'
import useAppStore from "@/stores/modules/app";
const props = defineProps<{
    prop: Prop
}>()
const getShowData = computed(() => props.prop.data.filter((item) => item.isShow))
const { getImageUrl } = useAppStore()
</script>

<style lang="scss" scoped>
.enter-btn {
    --el-button-size: 60px;
    --el-font-size-base: 18px;
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
    border: none;
    padding: 20px 35px 20px 8px;
    border-radius: 30px;
}
</style>