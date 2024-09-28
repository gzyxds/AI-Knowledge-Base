<template>
  <div>
    <popup
      ref="popupRef"
      title="用量设置"
      :async="true"
      width="550px"
      @confirm="handleConfirm"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-width="auto"
      >
        <el-form-item label="限制每个用户总对话数" prop="limit_today_chat">
          <div class="flex">
            <el-input-number
              v-model="formData.limit_today_chat"
              controls-position="right"
              :min="0"
              clearable
            />
            <span class="ml-[10px]">条</span>
          </div>
        </el-form-item>
        <el-form-item label="限制每个用户每天总对话数" prop="limit_total_chat">
          <div class="flex">
            <el-input-number
              v-model="formData.limit_total_chat"
              controls-position="right"
              :min="0"
              clearable
            />
            <span class="ml-[10px]">条</span>
          </div>
        </el-form-item>
        <el-form-item label="超出将默认回复" prop="limit_exceed">
          <div class="flex-1 min-w-0">
            <el-input v-model="formData.limit_exceed" />
          </div>
        </el-form-item>
      </el-form>
    </popup>
  </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { getReleaseDetail } from '@/api/robot'

const emit = defineEmits<{
  (event: 'confirm', value: any): void
}>()
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = reactive({
  id: '',
  limit_exceed: '',
  limit_today_chat: 0,
  limit_total_chat: 1
})

const formRules = shallowReactive<FormRules>({
  limit_exceed: [
    {
      required: true,
      message: '请输入超出将默认回复'
    }
  ],
  limit_today_chat: [
    {
      required: true,
      message: '请输入限制每个用户总对话数'
    }
  ],
  limit_total_chat: [
    {
      required: true,
      message: '请输入限制每个用户每天总对话数'
    }
  ]
})
const open = () => {
  formRef.value?.clearValidate()
  popupRef.value?.open()
}
const close = () => {
  popupRef.value?.close()
}
const handleConfirm = async () => {
  await formRef.value?.validate()
  emit('confirm', formData)
}
const setFormData = async (data: any) => {
  // const data = await getReleaseDetail({ apikey })
  Object.keys(formData).forEach((key) => {
    //@ts-ignore
    formData[key] = data[key]
  })
}

defineExpose({
  open,
  close,
  setFormData
})
</script>
