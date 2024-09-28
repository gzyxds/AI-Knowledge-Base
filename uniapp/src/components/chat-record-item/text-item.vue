<template>
    <template v-if="isMarkdown">
        <ua-markdown
            :content="content"
            @click-link="emit('click-link', $event)"
            :style="{
                color
            }"
        ></ua-markdown>
    </template>
    <template v-else>
        <text
            user-select
            class="whitespace-pre-line leading-[40rpx] select-text"
            :style="{
                color
            }"
        >
            {{ content }}
        </text>
    </template>
    <div v-if="imagesList.length" class="flex flex-wrap mx-[-10rpx]">
        <u-image
            v-for="(image, index) in imagesList"
            :key="index"
            :src="image"
            :width="180"
            :height="180"
            class="m-[10rpx]"
            @click="previewImg(index)"
        />
    </div>
    <div v-if="videos.length" class="flex flex-wrap videos-lists">
        <view v-for="(item, index) in videos" :key="index">
            <!-- <view class="w-[180rpx] h-[180rpx] bg-page relative">
                <view
                    class="absolute z-[999] left-1/2 top-1/2 translate-x-[-50%] translate-y-[-50%] rounded-full w-[40rpx] h-[40rpx] flex justify-center items-center bg-[rgba(0,0,0,0.5)] cursor-pointer"
                >
                    <u-icon name="play-right-fill" :size="24" color="#fff" />
                </view>
            </view>

            <video
                ref="videoRef"
                :id="videoId"
                class="scale-0 absolute"
                muted
                autoplay
                :src="item.url"
            /> -->
            <video-preview
                width="180rpx"
                height="180rpx"
                :url="item.url"
            ></video-preview>
        </view>
    </div>
    <view v-if="files?.length" class="mt-[30rpx]">
        <view
            v-for="(item, index) in files"
            :key="index"
            class="flex mb-[20rpx] items-center"
        >
            <image
                class="w-[36rpx] h-[28rpx] mr-2"
                src="@/static/images/common/icon_folder.png"
            />
            <view class="line-clamp-1 mr-[20rpx]">
                {{ item.name }}
            </view>
            <a :href="item.url" target="_blank" class="text-primary"> 下载 </a>
        </view>
    </view>
    <view class="flex items-center flex-wrapç" v-if="!loading">
        <view
            v-if="showCopyBtn"
            class="text-content text-sm flex items-center mr-[20rpx] mt-[16rpx]"
            @click="copy(content)"
        >
            <image
                class="w-[26rpx] h-[26rpx] mr-[8rpx]"
                src="@/static/images/common/icon_copy.png"
                alt="复制"
            />
            复制
        </view>
        <template v-if="showVoiceBtn">
            <view
                v-if="!audioPlaying"
                class="text-content text-sm flex items-center mt-[16rpx] mr-[20rpx]"
                @click="play()"
            >
                <u-loading
                    v-if="audioLoading"
                    mode="flower"
                    class="mr-[8rpx]"
                    :size="26"
                ></u-loading>
                <u-icon v-else name="volume" class="mr-[8rpx]" />
                朗读
            </view>
            <view
                v-else
                class="text-content text-sm flex items-center mt-[16rpx] mr-[20rpx]"
                @click="pause"
            >
                <u-icon name="volume" class="mr-[8rpx]" />
                停止
            </view>
        </template>
        <view
            v-if="showPoster"
            class="text-xs text-content flex items-center mr-[20rpx] mt-[16rpx]"
            @click="emit('poster')"
        >
            <u-icon name="photo" class="mr-[8rpx]" :size="30"></u-icon>
            生成海报
        </view>
        <slot name="btn" />
    </view>
</template>
<script lang="ts">
export default {
    options: {
        virtualHost: true
    },
    externalClasses: ['class']
}
</script>
<script setup lang="ts">
import { getChatBroadcast } from '@/api/chat'
import { useAudioPlay } from '@/hooks/useAudioPlay'
import { useCopy } from '@/hooks/useCopy'
import { computed, shallowRef } from 'vue'
const props = withDefaults(
    defineProps<{
        content: string
        isMarkdown: boolean
        loading?: boolean
        showCopyBtn?: boolean
        showVoiceBtn?: boolean
        recordId?: number | string
        showPoster?: boolean
        recordList?: any[]
        index?: number
        type?: number
        images?: any[]
        files?: any[]
        videos?: any[]
        color?: string
    }>(),
    {
        showCopyBtn: false,
        loading: false,
        showVoiceBtn: false,
        showPoster: false,
        type: 1,
        images: () => [],
        files: () => [],
        videos: () => [],
        recordList: () => []
    }
)

const emit = defineEmits(['click-link', 'poster'])
const { copy } = useCopy()
const imagesList = computed(() => props.images.map(({ url }) => url))

const previewImg = (index: number) => {
    uni.previewImage({
        current: index,
        urls: imagesList.value
    })
}
const { play, audioPlaying, pause, audioLoading } = useAudioPlay({
    api: getChatBroadcast,
    dataTransform(data) {
        return data.file
    },
    params: {
        records_id: props.recordId,
        content: props.index,
        type: props.type
    }
})
</script>

<style lang="scss" scoped>
.videos-lists {
    :deep() {
        .uni-video-container {
            @apply bg-page;
        }
    }
}
</style>
