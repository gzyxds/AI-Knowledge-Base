<template>
  <div class="overflow-x-auto" :style="{ height: `${windowHeight}px` }">
    <el-container class="bg-page !min-w-[1200px] h-full">
      <el-header height="auto" style="padding: 0">
        <DesignHeader />
      </el-header>
      <el-container class="min-h-0">
        <el-aside width="auto" style="overflow: visible">
          <DesignLeft />
        </el-aside>
        <el-main style="padding: 0">
          <DesignCenter />
        </el-main>
        <el-aside width="auto">
          <DesignRight />
        </el-aside>
      </el-container>
    </el-container>
  </div>
</template>
<script setup lang="ts">
import { useWindowSize, useEventListener } from '@vueuse/core'
import DesignHeader from './_components/design-header.vue'
import DesignLeft from './_components/design-left/index.vue'
import DesignCenter from './_components/design-center/index.vue'
import DesignRight from './_components/design-right/index.vue'
import { useCanvasStore } from '@/stores/canvas'
import { useUserStore } from '@/stores/user'
const userStore = useUserStore()
const canvasStore = useCanvasStore()
const { height: windowHeight } = useWindowSize()
const route = useRoute()
const { initTabs } = useDesignTabs()

useEventListener(window, 'beforeunload', (e) => {
  if (canvasStore.isChangeData) {
    e.preventDefault()
    e.returnValue = '内容已修改，确认离开页面吗？'
  }
})
onBeforeRouteLeave(async (to, from) => {
  try {
    if (canvasStore.isChangeData && userStore.isLogin) {
      await feedback.confirm('内容已修改，确认离开页面吗？')
    }
  } catch (error) {
    return false
  }
})
const dataChange = () => {
  canvasStore.isChangeData = true
}

watch(() => canvasStore.canvasJson, dataChange)
watch(() => canvasStore.music, dataChange)
watch(() => canvasStore.dub, dataChange)
watch(() => canvasStore.voiceContent, dataChange, {
  deep: true
})
watch(
  () => route.query,
  () => {
    initTabs()
  }
)

definePageMeta({
  layout: 'blank',
  hasPanel: false,
  auth: true
})
</script>
