<template>
    <view class="mt-4">
        <item-title title="图片尺寸" tips="指定生成图像的宽高比" required></item-title>
        <u-form-item prop="size">
            <view class="flex-1 bg-white rounded-lg py-[20rpx] pr-[20rpx] overflow-hidden">
                <scroll-view class="flex-1 min-w-0 whitespace-nowrap" scroll-x>
                    <view
                        v-for="(item, index) in pictureSize.lists"
                        :key="index"
                        class="inline-block w-[140rpx] ml-[20rpx]"
                    >
                        <view
                            class="picture-size text-center hover:text-primary"
                            :class="{
                                'picture-size-active': value == item?.scaleValue,
                                // 'picture-size-disable': !item?.scaleValue
                            }"
                            @click="value = item.scaleValue"
                        >
                            <view
                                class="flex justify-center items-center h-[40rpx]"
                            >
                                <view
                                    class="rect"
                                    :style="{
                                    width: `${item.width}rpx`,
                                    height: `${item.height}rpx`
                                }"
                                />
                            </view>
                            <view
                                class="text-[#101010] font-medium mt-[8rpx] size-scale"
                            >
                                {{ item.scaleLabel }}
                            </view>
                            <view
                                class="text-[#808D9C] mt-[8rpx] text-xs size-scale"
                            >
                                {{ item.name }}
                            </view>
                        </view>
                    </view>
                </scroll-view>
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import { reactive } from 'vue'
import ItemTitle from '../item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: '512x512'
    }
)
const { modelValue: value } = useVModels(props, emit)

const pictureSize = reactive({
    lists: [
        {
            name: '头像',
            scaleLabel: '1:1',
            scaleValue: '1:1',
            width: 40,
            height: 40
        },
        {
            name: '手机壁纸',
            scaleLabel: '1:2',
            scaleValue: '1:2',
            width: 20,
            height: 40
        },
        {
            name: '小红书',
            scaleLabel: '2:3',
            scaleValue: '2:3',
            width: 25,
            height: 40
        },
        {
            name: '文章',
            scaleLabel: '4:3',
            scaleValue: '4:3',
            width: 40,
            height: 30
        },
        {
            name: '媒体',
            scaleLabel: '3:4',
            scaleValue: '3:4',
            width: 30,
            height: 40
        },
        {
            name: '宣传海报',
            scaleLabel: '9:16',
            scaleValue: '9:16',
            width: 24,
            height: 40
        },
        {
            name: '电脑壁纸',
            scaleLabel: '16:9',
            scaleValue: '16:9',
            width: 40,
            height: 24
        },
        {
            name: '横版名片',
            scaleLabel: '3:2',
            scaleValue: '3:2',
            width: 40,
            height: 30
        },
    ]
})

value.value = '1:1'
</script>

<style lang="scss" scoped>
// 图片尺寸
.picture-size {
    transition: all 0.2s;
    border: 1px solid transparent;
    user-select: none;
    border-radius: 14rpx;
    padding: 28rpx 0;
    line-height: 1.5;
    background-color: #f7f7f7;
    .rect {
        background-color: #b3bcc8;
    }
}
.picture-size-active {
    border: 1px solid var(--color-primary);
    background-color: var(--color-primary-light-9);
    .rect {
        background-color: var(--color-primary);
    }
    .size-scale,
    .size-name {
        color: var(--color-primary);
    }
}
</style>
