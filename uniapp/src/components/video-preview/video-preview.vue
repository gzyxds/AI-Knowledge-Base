<template>
    <view class="video-preview" :style="{ width, height }">
        <!-- #ifdef H5 -->
        <view
            ref="videoRef"
            class="video-js"
            style="width: 100%; height: 100%"
        ></view>
        <!-- #endif -->
        <!-- #ifndef H5 -->
        <video
            :id="videoId"
            :style="{ height: 0, width: 0 }"
            @pause="playing = false"
            @fullscreenchange="fullscreenchange"
            class="video"
            :src="url"
        ></video>
        <!-- #endif -->
        <view :style="{ width, height }" class="box" @click="play">
            <image
                :style="{ width, height }"
                class="poster-img"
                :src="posterUrl"
                mode="aspectFit"
            ></image>
            <image
                class="play-icon"
                src="@/static/images/icon/icon_play.png"
                mode="widthFix"
            ></image>
        </view>
    </view>
</template>

<script setup lang="ts">
import {
    onMounted,
    nextTick,
    computed,
    shallowRef,
    ref,
    getCurrentInstance
} from 'vue'
//#ifdef H5
import videojs from 'video.js'
import 'video.js/dist/video-js.min.css'
//#endif
const props = withDefaults(
    defineProps<{
        url: string
        poster?: string
        width?: string
        height?: string
    }>(),
    {
        poster: '',
        width: '750rpx',
        height: '375rpx'
    }
)
const context = getCurrentInstance()
const videoRef = shallowRef()
const videoPlayer = shallowRef()
const playing = ref(false)
const posterUrl = computed(() => {
    if (props.poster) return props.poster
    return (
        props.url +
        '?x-oss-process=video/snapshot,t_' +
        0 +
        ',f_jpg,w_800,m_fast'
    )
})
const videoId = Date.now() + Math.ceil(Math.random() * 10000000) + ''
const initVideo = () => {
    //#ifndef H5
    videoPlayer.value = uni.createVideoContext(videoId, context?.proxy)
    //#endif

    //#ifdef H5
    const video = document.createElement('video')
    video.id = videoId
    //如果需要全屏幕展现播放器需要增加 object-fit: cover;
    video.style.width = '100%'
    video.style.height = '100%'
    video.controls = true
    //@ts-ignore
    video['x5-video-player-fullscreen'] = 'true'
    videoRef.value?.$el.appendChild(video)
    videoPlayer.value = videojs(
        video,
        {
            preload: 'none', //auto - 当页面加载后载入整个视频 meta - 当页面加载后只载入元数据 none - 当页面加载后不载入视频
            language: 'zh-CN',
            width: '100%',
            height: '100%',
            autoDisable: false,
            fluid: false, // 自适应宽高
            muted: false, //  是否静音
            controls: true, //是否拥有控制条 【默认true】,如果设为false ,那么只能通过api进行控制了。也就是说界面上不会出现任何控制按钮
            autoplay: false, //如果true,浏览器准备好时开始回放。 autoplay: "muted", // //自动播放属性,muted:静音播放
            loop: true, // 导致视频一结束就重新开始。 视频播放结束后，是否循环播放
            techOrder: ['html5', 'flash'], //播放顺序
            screenshot: true,
            sources: [
                {
                    src: props.url,
                    type: 'video/mp4'
                }
            ]
        },
        function (this: any) {
            this.on('fullscreenchange', function () {
                if (!videoPlayer.value?.isFullscreen()) {
                    videoPlayer.value?.pause()
                }
            })
        }
    )
    //#endif
}

const play = () => {
    videoPlayer.value?.play()

    //#ifdef H5
    videoPlayer.value?.requestFullscreen()
    //#endif

    //#ifndef H5
    videoPlayer.value?.requestFullScreen({ direction: 0 })
    //#endif
}

const fullscreenchange = (e: any) => {
    playing.value = e.detail.fullScreen
    if (!playing.value) {
        videoPlayer.value?.pause()
    }
}
onMounted(async () => {
    await nextTick()
    initVideo()
})
</script>

<style lang="scss" scoped>
.video-js {
    :deep() {
        .vjs-control-bar {
            height: 40px;
        }
        .vjs-time-control {
            line-height: 40px;
        }
        .vjs-button {
            background-color: transparent;
            outline: none;
            border: none;
            color: #fff;
            line-height: 40px;
        }
    }
}
.video-preview {
    position: relative;
    width: 750rpx;
    height: 300px;
    overflow: hidden;
}

.poster-img,
.box {
    top: 0;
    left: 0;
    display: flex;
    width: 750rpx;
    height: 300px;
    position: absolute;
    z-index: 99999;
    @apply bg-page;
}
.play-icon {
    width: 50rpx;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    z-index: 99999;
}
</style>
