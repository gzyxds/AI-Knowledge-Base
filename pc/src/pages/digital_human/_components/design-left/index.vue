<template>
  <div
    class="control-panel h-full"
    @mouseenter="showCollapsedBtn = true"
    @mouseleave="showCollapsedBtn = false"
  >
    <div class="h-full relative z-20 bg-white">
      <el-tabs
        tab-position="left"
        class="h-full"
        type="card"
        :model-value="tabsState.current"
        @tab-change="changeTabs($event as string)"
      >
        <el-tab-pane
          v-for="item in tabsState.tabs"
          :key="item.id"
          :name="item.id"
          lazy
        >
          <template #label>
            <div class="flex flex-col items-center justify-center">
              <Icon :name="item.icon" :size="22" />
              <span class="text-lg mt-[6px]">{{ item.label }}</span>
            </div>
          </template>
          <div class="w-[360px] h-full" v-show="!isCollapsed">
            <!-- @vue-ignore -->
            <component :is="components[item.component]" />
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>

    <div
      class="panel-left-arrow"
      v-if="isCollapsed || showCollapsedBtn"
      @click="isCollapsed = !isCollapsed"
    >
      <Icon
        class="mr-1"
        :name="`el-icon-${isCollapsed ? 'CaretRight' : 'CaretLeft'}`"
      />
    </div>
  </div>
</template>
<script setup lang="ts">
import Avatar from './avatar.vue'
import Dub from './dub.vue'
import Music from './music.vue'
import Background from './background.vue'
import Text from './text.vue'
import Captions from './captions.vue'
import Maps from './maps.vue'
import Prospect from './prospect.vue'
import { useCanvasStore } from '@/stores/canvas'

const components = {
  Avatar,
  Dub,
  Music,
  Background,
  Text,
  Captions,
  Maps,
  Prospect
}
const canvasStore = useCanvasStore()
const { tabsState, initTabs, changeTabs } = useDesignTabs()
const isCollapsed = computed({
  get() {
    return tabsState.value.isCollapsed
  },
  set(val) {
    tabsState.value.isCollapsed = val
  }
})
const showCollapsedBtn = ref(false)
initTabs()
watch(
  () => tabsState.value.current,
  async (value) => {
    canvasStore.setActiveObjectByType(value)
  }
)
</script>

<style lang="scss" scoped>
.control-panel {
  position: relative;
  .panel-left-arrow {
    position: absolute;
    width: 16px;
    height: 50px;
    top: 50%;
    right: -14px;
    transform: translateY(-50%);
    z-index: 10;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    @apply text-tx-secondary;
    &::before {
      content: '';
      position: absolute;
      border-radius: 0 10px 10px 0;
      box-sizing: border-box;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      transform: perspective(50px) rotateY(30deg);
      transform-style: preserve-3d;
      @apply border-br bg-white shadow-light;
    }
  }
  :deep() {
    .el-tabs--left.el-tabs--card {
      .el-tabs__item.is-left,
      .el-tabs__nav {
        border: none;
      }
    }
    .el-tabs--left > .el-tabs__header {
      border-right: 1px solid;
      margin-right: 0;
      @apply border-br-light;
    }
    .el-tabs--left .el-tabs__item.is-left {
      justify-content: center;
      padding: 10px;
      margin: 6px;
      height: auto;
      border-radius: 12px;
      font-weight: normal;
      &.is-active {
        @apply bg-primary-light-9;
      }
    }
    .el-tabs--left .el-tabs__content,
    .el-tabs--left .el-tab-pane {
      height: 100%;
    }
  }
}
</style>
