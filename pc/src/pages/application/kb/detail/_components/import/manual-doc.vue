<template>
  <div class="manual-import">
    <div class="py-4 flex flex-col">
      <ElForm>
        <ElFormItem>
          <div class="flex-1 min-w-0">
            <el-input
              v-model="formData.question"
              :placeholder="`请输入文本内容，10000个字以内。`"
              type="textarea"
              resize="none"
              :rows="15"
              maxlength="10000"
            />
          </div>
        </ElFormItem>
      </ElForm>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import type { IDataItem } from './hook'

const props = defineProps<{
  modelValue: IDataItem
}>()

const emit = defineEmits<{
  (event: 'update:modelValue', value: IDataItem): void
}>()
const formData = useVModel(props, 'modelValue', emit)
</script>

<style scoped lang="scss"></style>
