<template>
    <view
        v-if="url"
        class="flex mb-[32rpx] bg-white rounded-[10rpx] p-[20rpx] max-w-[450rpx] items-center"
    >
        <u-image
            class="flex-none"
            :src="url"
            width="80"
            height="80"
            @click="onPreview([url])"
        />
        <view
            class="line-clamp-2 text-main flex-1 min-w-0 ml-[12rpx]"
            :style="{
                'word-break': 'break-word'
            }"
        >
            {{ name }}
        </view>
    </view>
</template>

<script lang="ts" setup>
const props = defineProps<{
    url: string
    name: string
}>()
const onPreview = (picture: string[]) => {
    uni.previewImage({
        urls: picture
    })
}
</script>
