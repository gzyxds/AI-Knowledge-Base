<template>
    <view v-if="lists.length && isHidden" class="px-[30rpx] pt-[24rpx]">
        <u-swiper
            :list="lists"
            :height="280"
            name="image"
            :borderRadius="20"
            @click="handleClick"
        >
        </u-swiper>
    </view>
</template>

<script setup lang="ts">
import {watchEffect, ref} from "vue";
import {navigateTo} from "@/utils/navigate";
import {useAppStore} from "@/stores/app";
const {getImageUrl} = useAppStore();
const props = defineProps<{
    isHidden: boolean
    prop: Record<string, any>
}>()

const lists = ref<any>()
watchEffect(() => {
    try {
        const content = props?.prop.data;
        const len = content?.length;
        if (!len) return;
        for (let i = 0; i < len; i++) {
            const item = content[i];
            item.image = getImageUrl(item.image);
        }
        lists.value = content.filter((item: any) => item.isShow * 1);
    } catch (error) {
        //TODO handle the exception
        console.log("轮播图数据错误", error);
    }
})
const handleClick = (index: number) => {
    const link = lists.value[index]?.link;
    if (!link) return;
    navigateTo(link);
};
</script>

<style scoped>

</style>
