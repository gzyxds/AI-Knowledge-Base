<template>
    <div class="ai-create bg-[#f2f7fd]">
        <div>
            <img src="../../../image/ai-create01.png" alt="">
        </div>
        <div>
            <div
                class="ai-create__header banner_bg_img flex flex-col justify-center items-center"
                :style="{
                        'background-image': `url(${getImageUrl(prop.banner_bg)})`
                    }"
            >
                <h1
                    class="font-medium text-[50px]"
                    :class="getTitleColor(prop?.title_color)"
                >
                    {{ prop.title }}
                </h1>
                <div class="max-w-[880px] w-full mt-4">
                    <el-input
                        size="large"
                        class="h-[54px] rounded-[50px] overflow-hidden"
                        style="--el-border-color: #f2f2f2"
                        :prefix-icon="Search"
                        placeholder="请输入关键词搜索"
                    >
                    </el-input>
                </div>
            </div>

            <div class="mt-[30px] px-[100px] pb-[200px]">
                <img src="../../../image/ai-create02.png" alt="">
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type {Prop} from './config'
import useAppStore from "@/stores/modules/app";
import {Search} from "@element-plus/icons-vue";

const props = defineProps<{
    prop: Prop
}>()

const {getImageUrl} = useAppStore()

const getTitleColor = computed(() => {
    return (type: number) => {
        switch (type) {
            case 1:
                return 'text-black'
            case 2:
                return 'text-white'
            case 3:
                return 'text-primary'
        }
    }
})
</script>

<style lang="scss" scoped>
.ai-create__header {
    height: 300px;
    background-size: 100% 100%;
    background-repeat: no-repeat;
}

.banner_bg_img {
    background-size: 100% 100%;
    background-repeat: no-repeat;
}
</style>
