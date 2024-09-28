<template>
    <u-form-item prop="scale" label="生成尺寸" required>
        <view class="flex-1 min-w-0 overflow-hidden">
            <view class="flex flex-wrap mx-[-12rpx]">
                <view
                    v-for="(item, index) in pictureSize.lists"
                    :key="index"
                    class="w-[33.3%] px-[12rpx]"
                >
                    <view
                        class="picture-size cursor-pointer text-center hover:text-primary"
                        :class="{
                            'picture-size-active': value == item?.scaleValue,
                            'picture-size-disable': !item?.scaleValue
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
                            class="text-[#101010] dark:text-white mt-[8rpx] size-scale"
                        >
                            {{ item.scaleValue }}
                        </view>
                    </view>
                </view>
            </view>
        </view>
    </u-form-item>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import { computed, reactive, ref, shallowRef } from 'vue'
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: '1:1'
    }
)
const { modelValue: value } = useVModels(props, emit)

const pictureSize = reactive({
    lists: [
        {
            scaleValue: '1:1',
            width: 40,
            height: 40
        },
        {
            scaleValue: '3:4',
            width: 30,
            height: 40
        },
        {
            scaleValue: '4:3',
            width: 40,
            height: 30
        },
        {
            scaleValue: '9:16',
            width: 24,
            height: 40
        },
        {
            scaleValue: '16:9',
            width: 40,
            height: 24
        }
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

    margin-bottom: 20rpx;
    border-radius: 14rpx;
    padding: 28rpx;
    line-height: 1.5;
    @apply bg-white;
    .rect {
        background-color: #b3bcc8;
    }
}
.picture-size:hover {
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
.picture-size-disable {
    filter: opacity(0.5);
    user-select: none;
    pointer-events: none;
    cursor: not-allowed;
}
</style>
