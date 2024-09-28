<template>
  <div>
    <popup
      ref="popupRef"
      title="创建API"
      :async="true"
      width="550px"
      @confirm="handleConfirm"
      @close="formRef?.resetFields()"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-width="84px"
      >
        <el-form-item label="接口名称" prop="name">
          <el-input v-model="formData.name" placeholder="接口名称" clearable />
        </el-form-item>
      </el-form>
    </popup>
  </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import Popup from '@/components/popup/index.vue'

const emit = defineEmits<{
  (event: 'confirm', value: any): void
}>()
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = reactive({
  name: ''
})

const formRules = shallowReactive<FormRules>({
  name: [
    {
      required: true,
      message: '请输入接口名称'
    }
  ]
})
const open = () => {
  popupRef.value?.open()
}
const close = () => {
  popupRef.value?.close()
}
const handleConfirm = async () => {
  await formRef.value?.validate()
  emit('confirm', formData)
}

defineExpose({
  open,
  close
})
</script>
