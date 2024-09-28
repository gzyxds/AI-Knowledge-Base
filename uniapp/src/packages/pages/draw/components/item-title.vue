<template>
    <view class="flex">
        <text v-if="required" class="text-error text-lg">*</text>
        <view
            :style="{
                fontSize: fontSize,
                fontWeight: fontWeight,
            }"
        >
            {{ title }}
        </view>
        <view
            v-if="tips"
            class="flex mx-2"
            @click.stop="showTips(title, tips)"
        >
            <u-icon name="error-circle" color="#9CA3AF" :size="32" />
        </view>
    </view>
</template>

<script lang="ts" setup>
import {ref} from 'vue'
defineProps({
    title: {
        type: String,
        default: ''
    },
    tips: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    fontSize: {
        type: String,
        default: '28rpx'
    },
    fontWeight: {
        type: String,
        default: '500'
    },
})

const showTips = (title: string, content: string) => {
    uni.showModal({
        title,
        content,
        showCancel: false
    })
}
</script>