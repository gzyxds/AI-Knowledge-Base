<template>
    <view
        class="bg-white flex justify-between items-center px-[20rpx] py-[24rpx]"
    >
        <slot name="left" />
        <view class="flex items-center" @click="show = true">
            <u-icon name="plus-circle" :size="36" />
            <view class="flex-1 line-clamp-1 ml-[8rpx]">新建</view>
        </view>
        <slot name="right" />
    </view>
</template>
<script setup lang="ts">
import { computed } from 'vue'
const props = defineProps({
    name: {
        type: String
    },
    modelValue: {
        type: Boolean
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
}>()
const show = computed({
    get: () => {
        return props.modelValue
    },
    set: (value) => {
        emit('update:modelValue', value)
    }
})
</script>
