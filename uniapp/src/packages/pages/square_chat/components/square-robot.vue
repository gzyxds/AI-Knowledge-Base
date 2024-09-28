<template>
    <view class="bg-white flex items-center px-[20rpx] py-[24rpx]">
        <u-icon name="list" :size="40" @click="show = true" />
        <view class="flex-1 line-clamp-1 ml-[20rpx]">{{
            currentRobot.name
        }}</view>
        <slot name="right"></slot>
    </view>
    <u-popup v-model="show" mode="left" width="480rpx" safe-area-inset-bottom>
        <view class="h-full">
            <scroll-view scroll-y class="h-full">
                <view class="p-[20rpx]">
                    <view
                        class="robot-item"
                        :class="{
                            'robot-item--active': item.id === Number(current)
                        }"
                        v-for="item in data"
                        :key="item.id"
                        @click="handleSelect(item.id)"
                    >
                        <u-image
                            :src="item.image"
                            shape="circle"
                            width="80"
                            height="80"
                        ></u-image>

                        <view class="flex-1 min-w-0 ml-[20rpx]">
                            <view class="line-clamp-1 font-medium text-xl">{{
                                item.name
                            }}</view>
                            <view
                                class="text-muted line-clamp-1"
                                :class="{
                                    '!text-white': item.id === Number(current)
                                }"
                                >{{ item.intro }}</view
                            >
                        </view>
                    </view>
                </view>
            </scroll-view>
        </view>
    </u-popup>
</template>
<script setup lang="ts">
import { PropType } from 'vue'
import { computed, ref } from 'vue'
const props = defineProps({
    data: {
        required: true,
        type: Array as PropType<any[]>
    },
    modelValue: {
        required: true,
        type: [String, Number]
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: string | number): void
}>()
const show = ref(false)

const currentRobot = computed<any>(() => {
    return (
        props.data?.find((item: any) => item.id === Number(current.value)) || {}
    )
})
const current = computed({
    get: () => {
        return props.modelValue
    },
    set: (value) => {
        emit('update:modelValue', value)
    }
})

const handleSelect = (id: number) => {
    current.value = id
    show.value = false
}
</script>
<style lang="scss">
.robot-item {
    display: flex;
    align-items: center;
    padding: 20rpx;
    background-color: #f8f8fb;
    border-radius: 16rpx;
    margin-bottom: 20rpx;
    &--active {
        background-color: var(--color-primary);
        @apply text-white;
    }
}
</style>
