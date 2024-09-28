<template>
    <div
        class="video-player"
        :style="{
            width,
            height
        }"
        v-if="isClient"
    >
          <video-player
              class="w-full h-full vjs-default-skin vjs-big-play-centered"
              ref="playerRef"
              v-bind="options"
              :src="src"
          />
    </div>
</template>

<script setup lang="ts">
import { reactive, shallowRef } from 'vue'
import { VideoPlayerProps, VideoPlayer } from '@videojs-player/vue'
import 'video.js/dist/video-js.css'
import videojs from 'video.js'
import zhCn from 'video.js/dist/lang/zh-CN.json'

videojs.addLanguage('zh-CN', zhCn)
const props = defineProps({
    src: {
        type: String,
        required: true
    },
    height: String,
    width: String
})
const attr = useAttrs()
const playerRef = shallowRef()
const options = reactive<VideoPlayerProps>({
    muted: false, //静音
    autoplay: false,
    loop: false, //循环播放
    volume: 0.5, //默认音量大小
    controls: true,
    language: 'zh-CN',
    ...attr
})
const isClient = ref(false)

const play = () => {
    playerRef.value.play()
}

const pause = () => {
    playerRef.value.pause()
}

onMounted(() => {
  isClient.value = true
})

defineExpose({
    play,
    pause
})
</script>

<style lang="scss" scoped>
.video-player {
    :deep() {
        .vjs-time-control {
            display: block !important;
            padding: 0;
        }
        .vjs-time-divider {
            min-width: 0;
            text-align: center;
        }
        .vjs-remaining-time {
            display: none !important;
        }
        .video-js .vjs-big-play-button {
            font-size: 2.2em !important;
            line-height: 2.1em !important;
            height: 2.2em !important;
            width: 2.2em !important;
            -webkit-border-radius: 2.2em !important;
            -moz-border-radius: 2.2em !important;
            border-radius: 2.2em !important;
            background-color: #73859f;
            background-color: rgba(115, 133, 159, 0.5) !important;
            border-width: 0.12em !important;
            margin-top: -1.1em !important;
            margin-left: -1.1em !important;
        }
        .vjs-big-play-button .vjs-icon-placeholder {
            font-size: 1.5em !important;
        }
        .vjs-paused .vjs-big-play-button,
        .vjs-paused.vjs-has-started .vjs-big-play-button {
            display: block !important;
        }
        .vjs-picture-in-picture-control {
            display: none !important;
        }
        .vjs-control {
            width: 2.8em;
        }
    }
}
</style>
