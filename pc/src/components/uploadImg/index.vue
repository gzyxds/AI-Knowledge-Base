<template>
  <div v-if="isClient">
    <div v-loading="loading" element-loading-text="上传中...">
      <ElUpload
        ref="uploadRef"
        class="avatar-uploader"
        :show-file-list="false"
        :limit="1"
        :on-change="handleChange"
        :auto-upload="false"
        accept=".jpg,.png,.gif,.jpeg"
      >
        <slot>
          <!-- <div v-if="!value" class="el-upload flex-col">
            <Icon name="el-icon-Plus" :size="16" />
            <div>添加图片</div>
          </div>
          <div v-if="!!value" class="imgContiner relative">
            <img class="w-[100px] h-[100px] rounded-lg" :src="value" />
            <div
              v-if="canClose"
              class="icon absolute top-[-10px] right-[-10px]"
              @click.stop="value = ''"
            >
              <Icon size="20" name="el-icon-CircleCloseFilled" />
            </div>
          </div> -->
          <div v-if="!value" class="el-upload flex-col bg-fill-lighter">
            <Icon name="el-icon-Plus" :size="20" />
            <div class="text-tx-secondary mt-[2px]">添加图片</div>
          </div>
          <div v-if="!!value" class="imgContiner relative">
            <div
              class="border border-solid border-br-light rounded-[6px] relative cursor-pointer"
              :style="{
                width: size,
                height: size
              }"
            >
              <img class="rounded-lg w-full h-full" :src="value" />
            </div>

            <div
              v-if="canClose"
              class="icon absolute top-[-10px] right-[-10px] text-tx-secondary"
              @click.stop="value = ''"
            >
              <Icon size="20" name="el-icon-CircleCloseFilled" />
            </div>
          </div>
        </slot>
      </ElUpload>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { uploadImage } from '@/api/app'

const emits = defineEmits(['change', 'update:modelValue'])

const { getImageUrl } = useAppStore()

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  excludeDomain: {
    type: Boolean,
    default: false
  },
  canClose: {
    type: Boolean,
    default: true
  },
  size: {
    type: String,
    default: '100px'
  }
})

const loading = ref(false)
const isClient = ref(false)

const value = computed({
  get() {
    return props.excludeDomain
      ? getImageUrl(props.modelValue)
      : props.modelValue
  },
  set(value) {
    emits('update:modelValue', value)
  }
})
//上传组件ref
const uploadRef = shallowRef()

// const imgSrc = ref('')

const handleChange = async ({ raw }) => {
  try {
    loading.value = true
    const data = await uploadImage({ file: raw })
    loading.value = false
    value.value = props.excludeDomain ? data.url : data.uri
    emits('change', data.uri)
    uploadRef.value?.clearFiles()
  } catch (error) {
    loading.value = false
    uploadRef.value?.clearFiles()
  }
}

onMounted(() => {
  isClient.value = true
})
</script>

<style lang="scss" scoped>
.avatar-uploader .el-upload {
  width: 100px;
  height: 100px;
  border: 1px dashed #dddddd;
  border-radius: 6px;
  cursor: pointer;
  // position: relative;
  // overflow: hidden;
  // transition: var(--el-transition-duration-fast);
}

.avatar-uploader .el-upload:hover {
  border-color: var(--el-color-primary);
}

.el-icon.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  text-align: center;
}
.imgContiner {
  .icon {
    opacity: 0;
    transition: all 0.2s linear;
  }
  &:hover {
    .icon {
      opacity: 1;
    }
  }
}
</style>
