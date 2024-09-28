<template>
  <div
    class="dub-item flex items-center p-main cursor-pointer"
    :class="{
      'is-hover': isHover,
      'is-active': activeId == itemId,
      'is-disable': disabled
    }"
    @mouseenter="isHover = true"
    @mouseleave="isHover = false"
    @click="clickItem"
  >
    <div class="relative flex-none flex">
      <ElImage
        :src="pic"
        class="w-[40px] h-[40px] rounded-full overflow-hidden"
      />
      <div
        v-if="isHover || audioPlaying"
        class="absolute inset-0 flex items-center justify-center bg-[rgba(0,0,0,0.5)] rounded-full"
      >
        <div
          class="audio-btn cursor-pointer"
          :class="{
            'audio-btn--animation': audioPlaying
          }"
        >
          <span v-if="audioPlaying" class="text-white flex" @click.stop="pause">
            <Icon :size="24" name="el-icon-VideoPause" />
          </span>

          <span v-else class="text-white flex" @click.stop="play(url)">
            <Icon :size="24" name="el-icon-VideoPlay" />
          </span>
        </div>
      </div>
    </div>
    <div class="flex-1 line-clamp-2 min-w-0 ml-[10px]">
      {{ name }}
    </div>
    <div
      class="flex-none"
      :style="{
        visibility: isHover ? 'visible' : 'hidden'
      }"
    >
      <ElButton type="primary" :disabled="disabled">使用</ElButton>
    </div>
  </div>
</template>
<script lang="ts" setup>
const props = withDefaults(
  defineProps<{
    activeId?: string | number
    itemId: string | number
    name: string
    pic: string
    url: string
    disabled?: boolean
  }>(),
  {
    disabled: false
  }
)
const emit = defineEmits(['click'])

const isHover = ref(false)
const { play, audioPlaying, pause } = useAudioPlay()

const clickItem = () => {
  if (!props.disabled) {
    emit('click')
  }
}
</script>
<style lang="scss" scoped>
.dub-item {
  box-shadow: 0 2px 6px #ebefff;
  border-radius: 6px;
  border: 1px solid transparent;

  &.is-active {
    @apply border-primary;
  }
  &.is-disable {
    cursor: not-allowed;
  }
  .audio-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    &--animation {
      transform-origin: center center;
      animation: rotate 2s infinite linear forwards;
    }
  }
}
</style>
