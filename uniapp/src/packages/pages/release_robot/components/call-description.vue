<template>
    <view @click="show = true">
        <slot />
    </view>
    <u-popup
        v-model="show"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[80vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                {{ title }}
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="p-[20rpx]">
                        <ua-markdown :content="content" />
                    </view>
                </scroll-view>
            </view>
        </view>
    </u-popup>
</template>
<script lang="ts" setup>
import { ref } from 'vue'

const prop = defineProps({
    content: {
        type: String,
        default: ''
    },
    title: {
        type: String,
        default: ''
    }
})

const show = ref(false)
</script>
