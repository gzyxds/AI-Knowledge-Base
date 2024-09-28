<template>
  <Popup
    ref="popupRef"
    center
    title="修正问答"
    async
    width="900px"
    @confirm="handleConfirm"
  >
    <ElScrollbar>
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-width="120px"
      >
        <el-form-item label="选择知识库" prop="kb_id">
          <el-select v-model="formData.kb_id" class="w-[240px]">
            <el-option
              v-for="(item, index) in optionsData.knowledge"
              :key="index"
              :label="`${item.name}`"
              :value="item.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="提问问题" prop="ask">
          <el-input
            v-model="formData.ask"
            placeholder="请输入问题"
            type="textarea"
            resize="none"
            :rows="4"
            maxlength="600"
            show-word-limit
            clearable
          />
        </el-form-item>
        <el-form-item label="问题答案" prop="reply">
          <el-input
            v-model="formData.reply"
            placeholder="请输入答案"
            type="textarea"
            resize="none"
            :rows="15"
            clearable
          />
        </el-form-item>
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
        <el-form-item label="上传视频">
          <div class="flex-1">
            <div>
              <UploadVideo v-model="video" size="80px"> </UploadVideo>
            </div>
            <div class="form-tips">格式为MP4，大小不能超过20M</div>
          </div>
        </el-form-item>

        <el-form-item label="上传附件">
          <div class="flex-1">
            <div class="max-w-[600px]">
              <Upload v-model:files="formData.files" type="file" show-file-list>
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
    </ElScrollbar>
  </Popup>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import { knowKnowledgeList } from '@/api/my_database'
import Popup from '@/components/popup/index.vue'

const emit = defineEmits<{
  (event: 'confirm', value: any): void
}>()

const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const video = ref('')
const formData = ref({
  name: '',
  kb_id: '',
  ask: '',
  reply: '',
  images: [],
  video: [],
  files: []
})

watch(video, (value) => {
  formData.value.video = [{ url: value, name: '' }] as any
})

const formRules: FormRules = {
  kb_id: [
    {
      required: true,
      message: '选择知识库'
    }
  ],
  ask: [
    {
      required: true,
      message: '请输入问题'
    }
  ],
  reply: [
    {
      required: true,
      message: '请输入答案'
    }
  ]
}
const open = (data: any) => {
  formData.value = {
    ...formData.value,
    ask: data.ask,
    reply: data.reply,
    name: data.name
  }
  popupRef.value?.open()
}
const close = () => {
  popupRef.value?.close()
}
const handleConfirm = async () => {
  await formRef.value?.validate()
  emit('confirm', formData.value)
}

const { optionsData } = useDictOptions<{
  knowledge: any[]
}>({
  knowledge: {
    api: knowKnowledgeList,
    params: {
      page_type: 0
    },
    transformData(data) {
      return data.lists || []
    }
  }
})
defineExpose({
  open,
  close
})
</script>
