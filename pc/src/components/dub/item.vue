<template>
  <div
    class="dub-item"
    :class="{
      'dub-item--is-active': isActive
    }"
  >
    <div class="flex-1 min-w-0 line-clamp-1">
      {{ value }}
    </div>
    <template v-if="url">
      <Icon
        v-if="audioPlaying"
        name="el-icon-VideoPause"
        :size="18"
        @click.stop="pause"
      />
      <Icon
        v-else
        name="el-icon-VideoPlay"
        :size="18"
        @click.stop="play(url)"
      />
    </template>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  isActive: boolean
  value: string
  url?: string
}>()
const { play, pause, audioPlaying } = useAudioPlay()
</script>

<style lang="scss" scoped>
.dub-item {
  position: relative;
  font-size: var(--el-font-size-base);
  display: inline-flex;
  width: 100%;
  min-width: 150px;
  line-height: 30px;
  box-sizing: border-box;
  flex-grow: 1;
  align-items: center;
  justify-content: center;
  padding: 1px 11px;
  background-color: var(--el-input-bg-color, var(--el-fill-color-blank));
  background-image: none;
  border-radius: var(--el-input-border-radius, var(--el-border-radius-base));
  cursor: pointer;
  transition: var(--el-transition-box-shadow);
  transform: translate3d(0, 0, 0);
  box-shadow: 0 0 0 1px var(--el-input-border-color, var(--el-border-color))
    inset;
  &--is-active {
    box-shadow: 0 0 0 1px var(--el-color-primary);
    @apply text-primary;
  }
}
</style>
