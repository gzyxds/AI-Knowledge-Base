<template>
  <Popup
    ref="popupRef"
    :title="title"
    width="800px"
    :destroy-on-close="true"
    async
    @confirm="handleConfirm"
  >
    <el-form
      ref="formRef"
      :model="formData"
      :rules="formRules"
      label-width="100px"
      :disabled="disabled"
    >
      <el-form-item v-if="formData.type === 1" label="内容" prop="question">
        <el-input
          v-model="formData.question"
          placeholder="请输入内容"
          type="textarea"
          resize="none"
          :rows="20"
          clearable
        />
      </el-form-item>
      <template v-if="formData.type === 2">
        <el-form-item label="提问问题" prop="question">
          <el-input
            v-model="formData.question"
            placeholder="请输入问题"
            type="textarea"
            resize="none"
            :rows="6"
            maxlength="600"
            show-word-limit
            clearable
          />
        </el-form-item>
        <el-form-item label="问题答案" prop="answer">
          <el-input
            v-model="formData.answer"
            placeholder="请输入答案"
            type="textarea"
            resize="none"
            :rows="20"
            clearable
          />
        </el-form-item>
        <template v-if="formData.type === 2 && formData.method === 1">
          <el-form-item label="上传图片">
            <div class="flex-1">
              <div>
                <Upload
                  v-model:files="formData.images"
                  type="image"
                  list-type="picture-card"
                  :limit="9"
                  multiple
                  show-file-list
                >
                  <Icon name="el-icon-Plus" :size="20" />
                </Upload>
              </div>
              <div class="form-tips">最多支持上传 9 张图</div>
            </div>
          </el-form-item>
          <el-form-item label="上传附件">
            <div class="flex-1">
              <div class="max-w-[600px]">
                <Upload
                  v-model:files="formData.files"
                  type="file"
                  show-file-list
                >
                  <el-button>上传附件</el-button>
                  <template #tip>
                    <div class="el-upload__tip">
                      支持上传PDF、docx、excel、等文件格式
                    </div>
                  </template>
                </Upload>
              </div>
            </div>
          </el-form-item>
        </template>
      </template>
    </el-form>
  </Popup>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type { FormInstance, FormRules } from 'element-plus'
import Popup from '@/components/popup/index.vue'
interface FormData {
  method: number
  type: number
  question: string
  answer: string
  images: any[]
  files: any[]
}
const props = withDefaults(
  defineProps<{
    modelValue: FormData
    title: string
    disabled?: boolean
  }>(),
  {
    disabled: false
  }
)
const emit = defineEmits<{
  (event: 'update:modelValue', value: FormData): void
  (event: 'confirm'): void
}>()

const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = useVModel(props, 'modelValue', emit)
const formRules: FormRules = {
  question: [
    {
      validator(rule, value, callback, source, options) {
        if (!value) {
          if (formData.value.type === 1) {
            callback('请输入内容')
          } else if (formData.value.type === 2) {
            callback('请输入问题')
          }
        } else {
          callback()
        }
      }
    }
  ],
  answer: [
    {
      validator(rule, value, callback, source, options) {
        if (!value) {
          callback('请输入答案')
        } else {
          callback()
        }
      }
    }
  ]
}
const open = () => {
  popupRef.value?.open()
}
const close = () => {
  popupRef.value?.close()
}
const handleConfirm = async () => {
  await formRef.value?.validate()
  emit('confirm')
}

defineExpose({
  open,
  close
})
</script>
