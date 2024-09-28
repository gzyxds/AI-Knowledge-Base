<template>
  <div class="flex items-center justify-center">
    <span class="mr-[10px]">画布尺寸</span>

    <el-dropdown>
      <div
        class="bg-white min-w-[90px] flex item-center py-[6px] px-[10px] text-tx-primary rounded-md shadow-[0_2px_6px_#ebefff]"
      >
        <span class="flex-1 mr-[4px]">
          {{ canvasStore.defaultSize.label }}</span
        >

        <Icon name="el-icon-ArrowDown" />
      </div>
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item
            v-for="(item, key) in sizeData"
            :key="key"
            @click="changeSize(item)"
          >
            {{ item.label }}
          </el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>
</template>
<script setup lang="ts">
import { useCanvasStore, canvasSizeData } from '@/stores/canvas'
const sizeData = ref(canvasSizeData)
const canvasStore = useCanvasStore()
const changeSize = async (item: any) => {
  if (item.id === canvasStore.defaultSize.resolution) {
    return
  }
  if (!canvasStore.getCanvasJson()?.objects.length) {
    canvasStore.changeSize(item)
    return
  }
  await feedback.confirm(
    '是否确认更改画布尺寸？当前画面所有设置将被重置且无法恢复'
  )

  canvasStore.changeSize(item)
}
</script>

<style lang="scss" scoped></style>
