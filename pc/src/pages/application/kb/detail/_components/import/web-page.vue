<template>
  <div class="web-page">
    <div class="py-4">
      <ElForm>
        <ElFormItem>
          <div class="flex-1">
            <el-input
              v-model="url"
              :placeholder="`请输入要解析的网页链接，添加多个请按回车键分隔`"
              type="textarea"
              resize="none"
              :rows="6"
            />
          </div>
        </ElFormItem>
        <ElFormItem>
          <ElButton type="primary" :loading="isLock" @click="parseUrl">
            解析
          </ElButton>
        </ElFormItem>
      </ElForm>
      <div>
        <div v-for="(item, index) in formData" :key="index" class="mb-4">
          <div class="mb-2 text-tx-primary font-medium text-lg">
            #{{ index + 1 }}
            {{ item.name }}
            <ElButton link type="primary">
              <Icon name="el-icon-Delete" @click="handleDelete(item)" />
            </ElButton>
          </div>
          <template v-for="(data, index) in item.data" :key="index">
            <el-input
              v-model="data.q"
              :placeholder="`文件内容，空内容会自动省略`"
              type="textarea"
              resize="none"
              :rows="15"
            />
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import type { IDataItem } from './hook'
import { webHtmlCapture } from '@/api/my_database'
import feedback from '@/utils/feedback'
const props = defineProps<{
  modelValue: IDataItem[]
}>()

const emit = defineEmits<{
  (event: 'update:modelValue', value: IDataItem): void
}>()
const formData = useVModel(props, 'modelValue', emit)
const url = ref('')

const handleDelete = async (item: IDataItem) => {
  await feedback.confirm(`确定删除：${item.name}？`)
  const index = formData.value.indexOf(item)
  if (index !== -1) {
    formData.value.splice(index, 1)
  }
}

const { lockFn: parseUrl, isLock } = useLockFn(async () => {
  if (!url.value) return feedback.msgError('请输入网页链接')
  const data = await webHtmlCapture({
    url: url.value.split('\n').filter(Boolean)
  })
  formData.value = [
    ...data.map((item: any) => ({
      data: [
        {
          a: '',
          q: item.content
        }
      ],
      path: '',
      name: item.url
    })),
    ...formData.value
  ]
  url.value = ''
})
</script>

<style scoped lang="scss"></style>
