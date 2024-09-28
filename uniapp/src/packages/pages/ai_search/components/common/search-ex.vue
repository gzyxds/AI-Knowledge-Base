<template>
    <view class="flex flex-wrap mx-[-10rpx] mb-[-20rpx]">
        <view
            class="flex max-w-full items-center mx-[10rpx] mb-[20rpx] px-[22rpx] py-[12rpx] border border-light border-solid rounded-full"
            v-for="(item, index) in lists"
            :key="index"
            @click="emit('click-item', getText(item))"
        >
            <view class="flex-1 line-clamp-1 text-xs text-content">
                {{ getText(item) }}
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        lists: any[]
        prop: string
    }>(),
    {
        lists: () => [],
        prop: ''
    }
)
const emit = defineEmits<{
    (event: 'click-item', value: string): void
}>()

const getText = (item: any) => {
    return item[props.prop] ? item[props.prop] : item
}
</script>
