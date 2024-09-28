<template>
  <ElForm
    ref="formRef"
    v-bind="props"
    :rules="formRules"
    :model="formData"
    labelPosition="top"
    @submit.prevent
  >
    <ElFormItem
      v-for="item in formLists"
      :key="item.id"
      :prop="item.props.field"
      :label="item.props.title"
    >
      <component
        v-bind="item.props"
        :is="getWidgetByName(item.name)"
        v-model="formData[item.props.field]"
      />
    </ElFormItem>
  </ElForm>
</template>

<script setup lang="ts">
import type { FormInstance, FormProps } from 'element-plus'
import * as widgets from './widgets'

interface Props extends /* @vue-ignore */ Partial<FormProps> {
  formLists: any[]
  modelValue: Record<string, any>
}
const props = withDefaults(defineProps<Props>(), {
  labelPosition: 'top',
  formLists: () => []
})
const emit = defineEmits<{
  (event: 'update:modelValue', value: any): void
}>()
const formRef = shallowRef<FormInstance>()
const formData = computed({
  get() {
    return props.modelValue
  },
  set(value) {
    emit('update:modelValue', value)
  }
})
const formRules = ref<any>({})
const getWidgetByName = (name: string) => {
  return (widgets as any)[name]
}
const validate = async () => {
  await formRef.value?.validate()
}

watch(
  () => props.formLists,
  async (value) => {
    formRules.value = value?.reduce((prev: any, item: any) => {
      formData.value[item.props.field] = undefined
      if (item.props.isRequired) {
        prev[item.props.field] = [
          {
            required: true,
            message: '必填项不能为空',
            trigger: 'blur'
          }
        ]
      }
      return prev
    }, {})
    setTimeout(() => {
      formRef.value?.resetFields()
    })
  }
)

defineExpose({
  validate
})
</script>
