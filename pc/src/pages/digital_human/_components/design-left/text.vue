<template>
  <div class="avatar-select h-full flex flex-col">
    <div class="px-main pt-main">
      <el-button type="primary" class="w-full" size="large" @click="addWord">
        添加文字
      </el-button>
    </div>
    <div class="mt-[5px] flex-1 min-h-0" v-if="isActive">
      <el-tabs v-model="state.currentTab" stretch>
        <el-tab-pane name="setting">
          <template #label>
            <span class="el-tab__label-text">文字设置</span>
          </template>
          <div class="h-full">
            <el-scrollbar>
              <div class="p-main">
                <el-form>
                  <TextSetting
                    v-model:font="fontAttr.fontFamily"
                    v-model:font-size="fontAttr.fontSize"
                    v-model:font-color="fontAttr.fill"
                    v-model:stroke-color="fontAttr.stroke"
                  />
                </el-form>
              </div>
            </el-scrollbar>
          </div>
        </el-tab-pane>
        <el-tab-pane name="special">
          <template #label>
            <span class="el-tab__label-text">文字特效</span>
          </template>
          <div class="h-full">
            <el-scrollbar>
              <div class="p-main">
                <EffectList v-model="fontAttr.effect" />
              </div>
            </el-scrollbar>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>
    <div v-else class="p-main">
      <ElEmpty
        :image="empty_con"
        description="请添加文字，或在右侧选中文字～"
      />
    </div>
  </div>
</template>
<script lang="ts" setup>
import { useCanvasStore, TextTypes } from '@/stores/canvas'
import { cloneDeep } from 'lodash-es'
import TextSetting from './text-setting.vue'
import EffectList from './effect-list.vue'
import empty_con from '@/assets/image/empty_con.png'
const canvasStore = useCanvasStore()
const state = reactive({
  currentTab: 'setting'
})

const initFontAttr = {
  fontSize: 64,
  fontFamily: 'Alibaba PuHuiTi',
  fill: '#ffffff',
  stroke: '',
  effect: {
    name: '',
    server_key: '',
    type: '',
    url: ''
  }
}
const fontAttr = reactive(cloneDeep(initFontAttr))
const isActive = computed(
  () => canvasStore.activeObject?.customType === TextTypes.TEXT
)
const addWord = () => {
  state.currentTab = 'setting'
  const data = cloneDeep(initFontAttr)
  canvasStore.addText('这里是文字', TextTypes.TEXT, data)
}
watch(
  () => canvasStore.activeObject,
  (value) => {
    if (isActive.value) {
      for (const key in fontAttr) {
        //@ts-ignore
        fontAttr[key] = value?.data?.[key]
      }
    } else {
      Object.assign(fontAttr, cloneDeep(initFontAttr))
    }
  },
  {
    immediate: true
  }
)

const objectKeys = ['fontSize', 'fontFamily', 'fill', 'stroke']
watch(
  () => fontAttr,
  (value) => {
    if (!isActive.value) {
      return
    }
    for (const key in fontAttr) {
      if (objectKeys.includes(key)) {
        //@ts-ignore
        let item = value[key]
        if (key === 'fontSize') {
          item = canvasStore.calcFontSize(item)
        }

        canvasStore.activeObject?.set(key as any, item)
      }
    }
    const data = cloneDeep(fontAttr)
    canvasStore.activeObject?.set('data', data)
    canvasStore.canvas?.renderAll()
  },
  {
    deep: true
  }
)
</script>
<style lang="scss" scoped>
.avatar-select {
  :deep(.el-tabs) {
    height: 100%;
    display: flex;
    flex-direction: column;
    .el-tabs__header {
      margin-bottom: 0 !important;
      @apply px-main;
      .el-tabs__item.is-top {
        .el-tab__label-text {
          height: 100%;
          line-height: var(--el-tabs-header-height);
          border-bottom: 2px solid transparent;
        }
        &.is-active {
          .el-tab__label-text {
            @apply border-primary;
          }
        }
      }

      .el-tabs__active-bar {
        display: none;
      }
    }
    .el-tabs__content,
    .el-tab-pane {
      min-height: 0;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
  }
}
</style>
