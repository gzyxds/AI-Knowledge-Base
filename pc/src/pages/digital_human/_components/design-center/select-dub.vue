<template>
  <div
    class="h-[40px] px-[10px] max-w-[220px] flex items-center shadow-[0_2px_6px_#ebefff] rounded-full bg-white cursor-pointer"
    @click="selectMusic"
  >
    <div class="px-[10px]" v-if="!canvasStore.dub.Voice">选择配音</div>
    <template v-else>
      <div
        class="flex"
        :class="{
          playing: audioPlaying
        }"
      >
        <Icon name="local-icon-dub" size="18" />
      </div>

      <div class="flex-1 min-w-0 mx-[5px]">
        <OverflowTooltip
          :content="`${canvasStore.dub.Name}-${canvasStore.dub.Desc}`"
        ></OverflowTooltip>
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
import dub from '@/assets/image/dub.png'
const selectMusic = () => {
  if (canvasStore.dub.VoiceUrl) {
    if (audioPlaying.value) {
      pause()
    } else {
      play(canvasStore.dub.VoiceUrl)
    }
  } else {
    changeTabs('dub')
  }
}

const removeMusic = () => {
  canvasStore.dub = {}
}
</script>

<style lang="scss" scoped>
.playing {
  transform-origin: center center;
  animation: rotate 2s infinite linear forwards;
}
</style>
