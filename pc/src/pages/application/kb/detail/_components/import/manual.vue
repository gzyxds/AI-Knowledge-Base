<template>
  <div class="manual-import">
    <el-scrollbar>
      <div class="py-4">
        <el-form label-width="0px">
          <el-form-item>
            <el-input v-model="formData.question" :placeholder="`请输入问题`" />
          </el-form-item>
          <el-form-item>
            <el-input
              v-model="formData.answer"
              :placeholder="`请输入问题答案，10000个字以内。`"
              type="textarea"
              resize="none"
              :rows="15"
              maxlength="10000"
            />
          </el-form-item>
          <el-form-item>
            <div class="flex-1">
              <div>
                <Upload
                  v-model:files="images"
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
          <el-form-item>
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
        </el-form>
      </div>
    </el-scrollbar>
  </div>
</template>
<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import type { IManualQAData } from './hook'

const props = defineProps<{
  modelValue: IManualQAData
}>()

const emit = defineEmits<{
  (event: 'update:modelValue', value: IManualQAData): void
}>()
const formData = useVModel(props, 'modelValue', emit)
const images = ref([])
watch(images, (value) => {
  formData.value.images = value.map(({ url }) => url)
})
</script>

<style scoped lang="scss"></style>
