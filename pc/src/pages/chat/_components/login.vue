<template>
  <div>
    <Popup
      ref="popupRef"
      title="输入密码"
      :async="true"
      @confirm="handleConfirm"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-width="84px"
      >
        <el-form-item label="密码" prop="password">
          <el-input
            v-model="formData.password"
            placeholder="请输入密码"
            type="password"
            clearable
            @keydown.enter="(e) => e.preventDefault()"
          />
        </el-form-item>
      </el-form>
    </Popup>
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
  password: ''
})

const formRules = shallowReactive<FormRules>({
  password: [
    {
      required: true,
      message: '请输入密码'
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
