<template>
  <div class="bg-white rounded-[10px] p-main h-[420px]">
    <ElForm>
      <el-form-item label="播报内容">
        <el-radio-group
          v-model="canvasStore.voiceContent.type"
          @change="typeChange"
        >
          <el-radio :label="1">文本输入</el-radio>
          <el-radio :label="2">音频输入</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item v-if="canvasStore.voiceContent.type == 1">
        <el-input
          v-model="canvasStore.voiceContent.text"
          type="textarea"
          :rows="15"
          resize="none"
          placeholder="请输入播报内容..."
        />
      </el-form-item>
      <el-form-item v-if="canvasStore.voiceContent.type == 2">
        <div class="flex-1">
          <el-upload
            v-loading="loading"
            ref="uploadRef"
            drag
            :on-change="fileInput"
            :auto-upload="false"
            :show-file-list="false"
            :accept="fileAccept"
          >
            <div class="el-upload__text flex items-center justify-center">
              <Icon name="el-icon-Upload" />
              拖拽文件至此，或点击<em> 选择文件 </em>
            </div>
            <div class="el-upload__text">
              音频支持：{{ fileAccept }}格式，时长不超过30分钟， 大小不超过50MB
            </div>
          </el-upload>
          <div class="mt-[10px]" v-if="canvasStore.voiceContent.voice_url">
            <div class="flex flex-col items-center justify-center">
              <div>{{ canvasStore.voiceContent.voice_name }}</div>
              <div class="flex mt-[10px] items-center">
                <div class="scale-90">
                  <audio :src="canvasStore.voiceContent.voice_url" controls />
                </div>
                <ElButton link @click="removeAudio">
                  <Icon name="el-icon-Delete" />
                </ElButton>
              </div>
            </div>
          </div>
        </div>
      </el-form-item>
    </ElForm>
  </div>
</template>
<script setup lang="ts">
import { useCanvasStore, TextTypes } from '@/stores/canvas'
import { uploadFile } from '@/api/app'
import { UploadFile, UploadInstance } from 'element-plus'
const canvasStore = useCanvasStore()
const uploadRef = shallowRef<UploadInstance>()

const fileAccept = '.wav,.mp3'
const loading = ref(false)

const typeChange = (value: any) => {
  // if (value === 2) {
  //   feedback.msgError('音频输入暂不支持字幕和配音，如已有选择，已自动清除')
  //   const objects = canvasStore.canvas?.getObjects()
  //   objects?.forEach((item) => {
  //     if (item.customType === TextTypes.CAPTIONS) {
  //       canvasStore.canvas?.remove(item)
  //     }
  //   })
  //   canvasStore.dub = {}
  // }
}

const fileInput = async ({ raw: file }: UploadFile) => {
  try {
    if (file) {
      loading.value = true
      const res = await uploadFile('audio', { file, data: { use_type: 2 } })
      canvasStore.voiceContent.voice_url = res.uri
      canvasStore.voiceContent.voice_name = res.name
    }
  } catch (error: any) {
    feedback.msgError(error)
  } finally {
    loading.value = false
    uploadRef.value?.clearFiles()
  }
}

const removeAudio = () => {
  canvasStore.voiceContent.voice_url = ''
  canvasStore.voiceContent.voice_name = ''
}
</script>
