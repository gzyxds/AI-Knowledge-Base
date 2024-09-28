<template>
  <div
    class="h-[40px] px-[10px] max-w-[220px] flex items-center shadow-[0_2px_6px_#ebefff] rounded-full bg-white cursor-pointer"
    @click="selectMusic"
  >
    <div class="px-[10px]" v-if="!canvasStore.music.id">选择音乐</div>
    <template v-else>
      <ElImage
        :class="{
          playing: audioPlaying
        }"
        :src="canvasStore.music.cover"
        class="w-[30px] h-[30px] rounded-full"
      />
      <div class="flex-1 min-w-0 mx-[5px]">
        <OverflowTooltip :content="canvasStore.music.name"></OverflowTooltip>
      </div>
      <ElButton link @click="removeMusic">
        <Icon name="el-icon-Close" size="20" />
      </ElButton>
    </template>
  </div>
</template>
<script setup lang="ts">
import { useCanvasStore } from '@/stores/canvas'
const canvasStore = useCanvasStore()
const { changeTabs } = useDesignTabs()
const { play, audioPlaying, pause } = useAudioPlay()
const selectMusic = () => {
  if (canvasStore.music.url) {
    if (audioPlaying.value) {
      pause()
    } else {
      play(canvasStore.music.url)
    }
  } else {
    changeTabs('music')
  }
}

const removeMusic = () => {
  canvasStore.music = {
    id: 0,
    url: '',
    cover: '',
    name: ''
  }
}
</script>

<style lang="scss" scoped>
.playing {
  transform-origin: center center;
  animation: rotate 2s infinite linear forwards;
}
</style>
