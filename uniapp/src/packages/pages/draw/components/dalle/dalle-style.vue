<template>
    <view class="mt-[15rpx]">
        <item-title title="风格选择" tips="" required></item-title>

        <u-form-item prop="quality">
            <view class="flex justify-between flex-1 bg-white rounded-lg py-[20rpx] pr-[20rpx] overflow-hidden">
                <view
                    v-for="item in styleList"
                    :key="item.value"
                    class="style-picker"
                    :class="{
                    'style-picker__active': item.value === value,
                }"
                    @click="value = item.value"
                >
                    {{ item.label }}
                </view>
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import ItemTitle from "@/packages/pages/draw/components/item-title.vue";

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: 'standard'
    }
)
const { modelValue: value } = useVModels(props, emit)

const styleList: { value: string, label: string }[] = [
    {
        value: 'vivid',
        label: '生动'
    },
    {
        value: 'natural',
        label: '自然'
    }
]

value.value = 'vivid'
</script>

<style scoped>
.style-picker {
    display: inline-block;
    cursor: pointer;
    width: 45%;
    margin-left: 20rpx;
    padding: 12rpx 0;
    text-align: center;
    border-radius: 8rpx;
    border: 1px solid transparent;
    @apply bg-page;
}

.style-picker__active {
    color: var(--color-primary);
    border: 1px solid var(--color-primary);
    background-color: var(--color-primary-light-9);
}
</style>
