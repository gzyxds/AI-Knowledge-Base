<template>
    <view class="flex flex-wrap items-center">
        <view
            class="mr-[40rpx] bg-white flex item-center px-[24rpx] leading-[50rpx] text-xs rounded-full"
            @click="searchStore.launchSearch()"
        >
            <u-icon name="reload" />
            <text class="ml-[8rpx]"> 重写 </text>
        </view>
        <view
            class="mr-[40rpx] text-primary bg-white flex item-center px-[24rpx] leading-[50rpx] text-xs rounded-full"
            @click="copyResult"
        >
            <u-icon name="file-text" />
            <text class="ml-[8rpx]"> 复制 </text>
        </view>
    </view>
</template>
<script setup lang="ts">
import { useSearch } from '../../useSearch'
import { useCopy } from '@/hooks/useCopy'
const searchStore = useSearch()
const { copy } = useCopy()
const copyResult = () => {
    const text = searchStore.result.markdown.reduce((prev, item) => {
        if (['markdown', 'expand_query'].includes(item.type)) {
            prev += item.content + '\n'
        }
        return prev
    }, '')
    copy(text)
}
</script>

<style lang="scss" scoped></style>
