<template>
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
                知识库引用({{ data.length }}条)
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="p-[20rpx]">
                        <div
                            v-for="(item, index) in data"
                            :key="index"
                            class="py-[12rpx] px-[20rpx] border border-solid border-light rounded mb-[20rpx]"
                        >
                            <div
                                v-if="item.question"
                                class="text-muted text-sm whitespace-pre-wrap"
                            >
                                {{ item.question }}
                            </div>
                            <div v-if="item.answer" class="text-muted text-sm whitespace-pre-wrap">
                                {{ item.answer }}
                            </div>
                        </div>
                    </view>
                </scroll-view>
            </view>
        </view>
    </u-popup>
</template>
<script setup lang="ts">
import { useVModel } from '@vueuse/core'
const props = withDefaults(
    defineProps<{
        show: boolean
        data: any[]
    }>(),
    {
        data: () => []
    }
)
const emit = defineEmits<{
    (event: 'update:show', show: boolean): void
}>()
const show = useVModel(props, 'show', emit)
</script>
