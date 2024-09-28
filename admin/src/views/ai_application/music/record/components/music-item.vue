<template>
    <div
        class="flex items-center cursor-pointer hover:text-primary"
        :class="{ 'text-primary': audioPlaying }"
        @click="togglePlay"
    >
        <div class="line-clamp-1">{{ name }}</div>
        <span class="flex ml-[10px]" v-if="url" :class="{ '!opacity-100': audioPlaying }">
            <Icon :name="`el-icon-${audioPlaying ? 'VideoPause' : 'VideoPlay'}`" :size="18" />
        </span>
    </div>
</template>

<script lang="ts" setup>
import { useAudioPlay } from '@/hooks/useAudioPlay'
const props = defineProps<{
    name: string
    url: string
}>()

const { pause, play, audioPlaying } = useAudioPlay()
const togglePlay = () => {
    if (!props.url) return
    if (audioPlaying.value) {
        pause()
    } else {
        play(props.url)
    }
}
</script>
