<template>
  <div>
    <el-form-item label="设置字体">
      <el-select
        class="w-full"
        :model-value="fontModel"
        @change="fontFamilyChange"
      >
        <el-option
          v-for="item in appStore.fontList"
          :key="item.code"
          :label="item.name"
          :value="item.code"
        />
      </el-select>
    </el-form-item>
    <el-form-item label="字体大小">
      <el-input-number
        v-model="fontSizeModel"
        :min="20"
        :max="160"
        controls-position="right"
      />
    </el-form-item>
    <el-form-item label="字体颜色">
      <div class="flex flex-1">
        <el-color-picker v-model="fontColorModel" />
        <el-input class="flex-1 ml-[10px]" v-model="fontColorModel" readonly />
      </div>
    </el-form-item>
    <el-form-item label="描边颜色">
      <div class="flex flex-1">
        <el-color-picker v-model="strokeColorModel" />
        <el-input
          class="flex-1 ml-[10px]"
          v-model="strokeColorModel"
          readonly
        />
      </div>
    </el-form-item>
  </div>
</template>
<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import FontFaceObserver from 'fontfaceobserver'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
const props = withDefaults(
  defineProps<{
    font: string
    fontSize: number
    fontColor: string
    strokeColor: string
  }>(),
  {}
)
const emit = defineEmits<{
  (event: 'update:font', value: string): void
  (event: 'update:fontSize', value: number): void
  (event: 'update:fontColor', value: string): void
  (event: 'update:strokeColor', value: string): void
}>()

const {
  font: fontModel,
  fontSize: fontSizeModel,
  fontColor: fontColorModel,
  strokeColor: strokeColorModel
} = useVModels(props, emit)

const fontFamilyChange = async (font: string) => {
  feedback.loading('正在加载字体中，请稍等...')
  try {
    await new FontFaceObserver(font).load(null, 100 * 1000)
    fontModel.value = font
  } catch (error) {
    console.log(error)
    feedback.msgError('字体加载失败，请重试')
  } finally {
    feedback.closeLoading()
  }
}
</script>
<style lang="scss" scoped></style>
