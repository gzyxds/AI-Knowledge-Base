<template>
  <div class="h-full bg-white w-[240px]">
    <ElScrollbar>
      <div>
        <div class="font-medium p-main">图层</div>

        <div v-if="layerLists.length">
          <draggable
            v-model="layerLists"
            item-key="id"
            handle=".drag-icon"
            draggable=".draggable"
          >
            <template #item="{ element }">
              <div
                class="flex items-center h-[32px] pl-[15px] pr-[10px] layer-item cursor-pointer"
                :class="{
                  active: element.id === canvasStore.activeObject?.id,
                  draggable: element.customType !== 'background'
                }"
                @click="canvasStore.setActiveObject(element.id)"
              >
                <span>
                  <Icon :name="getIcon(element.customType)" />
                </span>
                <span class="flex-1 min-w-0 line-clamp-1 ml-[10px]">
                  {{ element.name }}
                  <span class="text-tx-regular text-sm" v-if="element.text">
                    （{{ element.text }}）
                  </span>
                </span>
                <div class="layer-icon">
                  <ElButton link @click="canvasStore.delObject(element.id)">
                    <Icon name="local-icon-del" />
                  </ElButton>
                </div>

                <div
                  class="drag-icon layer-icon"
                  v-if="element.customType !== 'background'"
                >
                  <ElButton link class="!cursor-move">
                    <Icon name="local-icon-dot" />
                  </ElButton>
                </div>
              </div>
            </template>
          </draggable>
        </div>
        <div v-else>
          <el-empty
            :image="empty_layer"
            description="暂无图层～"
            :image-size="80"
          >
          </el-empty>
        </div>
      </div>
    </ElScrollbar>
  </div>
</template>

<script setup lang="ts">
import { useCanvasStore } from '@/stores/canvas'
import Draggable from 'vuedraggable'
import empty_layer from '@/assets/image/empty_layer.png'

const canvasStore = useCanvasStore()
const { tabsState } = useDesignTabs()
const getIcon = (type: string) => {
  const current = tabsState.value.tabs.find((item) => item.id == type)
  return current?.icon || 'local-icon-chuangyiwuliao'
}
const layerLists = computed({
  get() {
    return [...canvasStore.canvasJson.objects].reverse()
  },
  set(value) {
    canvasStore.setCanvasJson({
      ...canvasStore.canvasJson,
      objects: value.reverse()
    })
  }
})
</script>
<style lang="scss" scoped>
.layer-item {
  &.active {
    background-color: var(--el-color-primary-light-9);
  }
  &.sortable-drag {
    background-color: var(--el-color-primary-light-9);
  }
  &.sortable-ghost {
    background: var(--el-color-primary);
    height: 2px;
    width: 100%;
    * {
      visibility: hidden;
    }
  }
  &:hover {
    .layer-icon {
      opacity: 1;
    }
  }
  .layer-icon {
    opacity: 0;
  }
}
</style>

<style lang="scss" scoped></style>
