<template>
    <u-popup
        v-model="showModel"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[80vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                查看回复
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
import { useVModels } from '@vueuse/core'
const props = defineProps<{
    show: boolean
    content: string
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
}>()

const { show: showModel } = useVModels(props, emit)
</script>
