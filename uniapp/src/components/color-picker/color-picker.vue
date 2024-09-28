<template>
    <view class="flex items-center">
        <view
            class="p-[15rpx] rounded-[3rpx] border border-solid border-light"
            @click="showColorPopup"
        >
            <view
                class="w-[40rpx] h-[40rpx] rounded-[3rpx] border border-solid border-light"
                :style="{
                    background: modelValue
                }"
            >
            </view>
        </view>
        <view class="flex-1 min-w-0 mx-[20rpx]">
            <u-input
                :modelValue="modelValue"
                border
                :disabled="true"
                :placeholder="''"
            />
        </view>
        <view
            class="text-primary"
            @click="emit('update:modelValue', defaultColor)"
        >
            重置
        </view>
    </view>
    <uv-pick-color ref="pickerColorRef" @confirm="confirm"></uv-pick-color>
</template>
<script setup lang="ts">
import { shallowRef } from 'vue'
const props = withDefaults(
    defineProps<{
        modelValue: string
        disabled?: boolean
        defaultColor?: string
    }>(),
    {
        modelValue: '',
        disabled: false,
        defaultColor: ''
    }
)

const emit = defineEmits<{
    (event: 'update:modelValue', id: string): void
}>()

const pickerColorRef = shallowRef()

const showColorPopup = () => {
    if (props.disabled) {
        return
    }
    pickerColorRef.value?.open()
}

const confirm = ({ hex }: any) => {
    emit('update:modelValue', hex)
}
</script>
