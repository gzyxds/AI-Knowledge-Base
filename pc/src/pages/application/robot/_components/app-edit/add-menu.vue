<template>
  <el-dialog
    v-model="showModel"
    title="添加菜单"
    width="640px"
    :destroy-on-close="true"
  >
    <div>
      <ElForm
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-width="100px"
        :disabled="type === 'view'"
      >
        <el-form-item label="关键词" prop="keyword">
          <el-input
            v-model="formData.keyword"
            placeholder="请输入关键词"
            clearable
            :maxlength="20"
            :show-word-limit="true"
          />
        </el-form-item>
        <el-form-item label="回复内容" prop="content">
          <el-input
            v-model="formData.content"
            placeholder="请输入回复内容"
            type="textarea"
            :autosize="{ minRows: 8, maxRows: 8 }"
            clearable
            :maxlength="3000"
            :show-word-limit="true"
            resize="none"
          />
        </el-form-item>

        <el-form-item label="上传图片" prop="image">
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
      </ElForm>
    </div>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="showModel = false">取消</el-button>
        <el-button type="primary" @click="handleConfirm"> 确定 </el-button>
      </span>
    </template>
  </el-dialog>
</template>
<script setup lang="ts">
import { cloneDeep } from 'lodash-es'
import { useVModels } from '@vueuse/core'
import { FormInstance, FormRules } from 'element-plus'
const props = defineProps<{
  show: boolean
  // 'add' | 'edit' | 'view'
  type: string
  data: Record<string, any>
}>()
const emit = defineEmits<{
  (event: 'update:show', value: boolean): void
  (event: 'update:data', value: boolean): void
  (event: 'confirm', value: any): void
}>()

const formRef = shallowRef<FormInstance>()
const { show: showModel, data: formData } = useVModels(props, emit)
const images = computed({
  get() {
    return formData.value.images.map((url: string) => ({ url }))
  },
  set(value) {
    formData.value.images = value.map((item: any) => item.url)
  }
})
const formRules: FormRules = {
  keyword: [
    {
      required: true,
      message: '请输入关键词'
    }
  ]
}
const handleConfirm = async () => {
  await formRef.value?.validate()
  emit('confirm', cloneDeep(formData.value))
  showModel.value = false
}
</script>
