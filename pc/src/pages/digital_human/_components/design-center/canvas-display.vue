<template>
  <div
    ref="workspaceRef"
    v-loading="loading"
    class="canvas-display overflow-hidden"
    :style="{
      height: `${workspaceHeight}px`
    }"
  >
    <div class="canvas-bg" v-show="canvasStore.canvas">
      <canvas :id="canvasId" />
    </div>
  </div>
</template>
<script setup lang="ts">
import { useElementSize, watchThrottled } from '@vueuse/core'
import { useCanvasStore } from '@/stores/canvas'
const canvasStore = useCanvasStore()
const workspaceRef = shallowRef<HTMLDivElement | null>(null)
const { width } = useElementSize(workspaceRef)
const canvasId = 'design-canvas'
const workspaceHeight = ref(canvasStore.defaultSize.height)
const loading = ref(false)

watchThrottled(
  () => canvasStore.defaultSize,
  () => {
    calculateTheHeight()
  }
)

const calculateTheHeight = () => {
  if (width.value >= canvasStore.defaultSize.width) {
    workspaceHeight.value = canvasStore.defaultSize.height
  } else {
    workspaceHeight.value =
      (width.value * canvasStore.defaultSize.height) /
      canvasStore.defaultSize.width
  }
}
watchThrottled(width, (value) => {
  calculateTheHeight()
})
watchThrottled(workspaceHeight, (value) => {
  if (value) {
    const scale = value / canvasStore.defaultSize.height
    canvasStore.setZoom(scale)
  }
})
onMounted(async () => {
  loading.value = true
  await nextTick()
  try {
    await canvasStore.initCanvas(canvasId, workspaceRef.value!)
  } finally {
    loading.value = false
  }
})
onUnmounted(() => {
  canvasStore.canvas?.dispose()
  canvasStore.$dispose()
})
</script>

<style lang="scss" scoped>
.canvas-display {
  width: 100%;
  background-color: #ebebeb;
  border-radius: 10px;
  box-shadow: 0 2px 6px #ebefff;
  display: flex;
  justify-content: center;
  align-items: center;
  .canvas-bg {
    background: url('../../../../assets/image/16_9.png') no-repeat;
    background-size: cover;
    background-position: center;
  }
}
</style>
