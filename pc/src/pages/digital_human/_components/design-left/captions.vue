<template>
  <div class="avatar-select h-full flex flex-col">
    <div class="flex-1 min-h-0">
      <div class="h-full">
        <el-scrollbar>
          <div class="p-main">
            <el-form>
              <ElFormItem label="字幕显示">
                <el-switch
                  v-model="fontAttr.status"
                  inline-prompt
                  :active-value="1"
                  :inactive-value="0"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="insertCaptions"
                />
              </ElFormItem>
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
    </div>
  </div>
</template>
<script lang="ts" setup>
import { cloneDeep } from 'lodash-es'
import { useCanvasStore, TextTypes } from '@/stores/canvas'
import TextSetting from './text-setting.vue'
import { Console } from 'console'
const canvasStore = useCanvasStore()
const fontAttr = reactive({
  status: 0,
  fontSize: 64,
  fontFamily: 'Alibaba PuHuiTi',
  fill: '#ffffff',
  stroke: ''
})
const isActive = computed(() => {
  return canvasStore.activeObject?.customType === TextTypes.CAPTIONS
})

const insertCaptions = (val: any) => {
  if (val) {
    canvasStore.addText('此处是字幕', TextTypes.CAPTIONS, fontAttr)
  } else {
    const objects = canvasStore.canvas?.getObjects()
    const currentObject = objects?.find(
      (item) => item.customType === TextTypes.CAPTIONS
    )
    canvasStore.canvas?.remove(currentObject!)
  }
}
watch(
  () => canvasStore.activeObject,
  (value) => {
    if (isActive.value) {
      for (const key in fontAttr) {
        //@ts-ignore
        fontAttr[key] = value?.data?.[key]
      }
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
const objectRemoved = () => {
  const objects = canvasStore.canvas?.getObjects()
  const currentObject = objects?.find(
    (item) => item.customType === TextTypes.CAPTIONS
  )
  if (currentObject) {
    fontAttr.status = 1
  } else {
    fontAttr.status = 0
  }
}

watchEffect(() => {
  if (canvasStore.canvas) {
    canvasStore.canvas?.on('object:removed', objectRemoved)
  }
})

onUnmounted(() => {
  canvasStore.canvas?.off('object:removed', objectRemoved)
})
</script>
<style lang="scss" scoped></style>
