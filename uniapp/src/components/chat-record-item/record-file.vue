<template>
    <view
        v-if="url"
        class="flex mb-[32rpx] bg-white rounded-[10rpx] p-[20rpx] max-w-[450rpx] items-center"
        @click="onPreview"
    >
        <!-- <u-image class="flex-none" :src="icon_doc" width="80" height="80" /> -->
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
// import icon_doc from '@/static/images/icon/icon_doc.png'
const props = defineProps<{
    url: string
    name: string
}>()
const onPreview = async () => {
    //#ifdef H5
    window.open(props.url, '_blank')
    //#endif
    //#ifndef H5
    try {
        uni.showLoading({
            title: '请稍等...'
        })
        const { tempFilePath } = await uni.downloadFile({
            url: props.url
        })
        await uni.openDocument({
            filePath: tempFilePath,
            showMenu: true
        })
        uni.hideLoading()
    } catch (error) {
        uni.hideLoading()
        uni.$u.toast(`文件${props.name}打开失败，请重试`)
    }

    //#endif
}
</script>
