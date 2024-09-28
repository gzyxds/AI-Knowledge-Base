<template>
  <div v-if="isClient">
    <div>
      <ElUpload
        ref="uploadRef"
        :show-file-list="false"
        :limit="1"
        :on-change="handleChange"
        :auto-upload="false"
        accept=".jpg,.png,.gif,.jpeg"
      >
        <slot />
      </ElUpload>
      <ElDialog
        v-model="state.cropperVisible"
        :append-to-body="true"
        :close-on-click-modal="false"
        :width="600"
        @close="state.cropperVisible = false"
      >
        <div class="h-[400px]">
          <VueCropper
            ref="vueCropperRef"
            :img="state.imagePath"
            :autoCrop="true"
            :auto-crop-height="200"
            :auto-crop-width="200"
            output-type="png"
          />
        </div>
        <template #footer>
          <span class="dialog-footer">
            <ElButton @click="handleConfirmCropper"> 确认裁剪 </ElButton>
          </span>
        </template>
      </ElDialog>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { ElUpload, ElDialog, ElButton } from 'element-plus'
import 'vue-cropper/dist/index.css'
import 'element-plus/es/components/upload/style/css.mjs'
import { VueCropper } from 'vue-cropper'
import { uploadImage } from '@/api/app'
const emit = defineEmits(['change'])
const vueCropperRef = shallowRef()
const uploadRef = shallowRef<InstanceType<typeof ElUpload>>()

const state = reactive({
  cropperVisible: false,
  imagePath: ''
})
const isClient = ref(false)

const handleChange = (rawFile: any) => {
  const URL = window.URL || window.webkitURL
  state.imagePath = URL.createObjectURL(rawFile.raw)
  state.cropperVisible = true
  uploadRef.value?.clearFiles()
}
const handleConfirmCropper = () => {
  vueCropperRef.value?.getCropBlob(async (file: any) => {
    const fileName = `file.${file.type.split('/')[1]}`
    const imgFile = new window.File([file], fileName, {
      type: file.type
    })
    const data = await uploadImage({ file: imgFile })
    state.cropperVisible = false
    emit('change', data.uri)
  })
}

onMounted(() => {
  isClient.value = true
})
</script>
<style lang="scss" scoped></style>
