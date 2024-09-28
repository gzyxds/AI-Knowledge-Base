<template>
    <u-sticky
        v-if="isHidden"
        h5-nav-height="0"
        bg-color="transparent"
    >
        <view
            class="header flex items-center"
            :style="{
                'background-color': `rgba(255, 255, 255, ${percent})`
             }"
        >
            <image
                :src="appStore.getWebsiteConfig.pc_logo"
                class="w-[50rpx] h-[50rpx] rounded-md"
            />
            <text class="ml-2">{{ appStore.getWebsiteConfig.pc_name }}</text>
        </view>
    </u-sticky>
</template>

<script setup lang="ts">
import { onPageScroll } from "@dcloudio/uni-app";
import { ref } from 'vue'
import { useAppStore } from "@/stores/app";
const appStore = useAppStore()

const props = defineProps<{
    percent: number
    isHidden: boolean
    prop: Record<string, any>
}>()
</script>

<style scoped>
.header {
    padding: 24rpx 30rpx;
    font-size: 28rpx;
    font-weight: bold;
}
</style>
