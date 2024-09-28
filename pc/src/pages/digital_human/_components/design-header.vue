<template>
  <div class="bg-primary h-[60px] flex items-center text-white px-main">
    <el-page-header @back="goBack">
      <template #content>
        <span class="text-white text-base">
          {{ canvasStore.name }}

          <el-button link text @click="changeName">
            <span class="text-white">
              <Icon name="el-icon-EditPen" />
            </span>
          </el-button>
        </span>
      </template>
    </el-page-header>
    <div class="ml-auto">
      <el-button
        plain
        style="--el-button-bg-color: transparent; --el-button-text-color: #fff"
        :loading="saveLoading"
        @click="handleSave"
      >
        存为草稿
      </el-button>
      <el-button @click="compositeVideo" :loading="compositeLoading">
        合成视频
      </el-button>
    </div>
    <el-dialog
      v-model="showLoading"
      title="视频生成"
      width="500px"
      :close-on-click-modal="false"
      :show-close="false"
    >
      <div class="flex flex-col items-center justify-center">
        <div
          class="w-[400px] h-[200px] relative text-white rounded-lg overflow-hidden"
        >
          <div
            class="loading absolute z-10 bg-[rgba(0,0,0,0.5)] inset-0 flex flex-col justify-center items-center"
          >
            <template v-if="compositeStatus == 2">
              <Icon
                class="el-icon is-loading"
                :size="30"
                name="el-icon-Loading"
              />
              <div class="mt-[10px] text-lg">视频生成中</div>
            </template>
            <template v-if="compositeStatus == 3">
              <Icon :size="30" name="el-icon-SuccessFilled" />
              <div class="mt-[10px] text-lg">视频合成成功</div>
            </template>
            <template v-if="compositeStatus == 4">
              <Icon :size="30" name="el-icon-CircleCloseFilled" />
              <div class="mt-[10px] text-lg">视频合成失败</div>
            </template>
          </div>
          <ElImage
            v-if="canvasStore.cover"
            class="w-full h-full"
            :src="canvasStore.cover"
            fit="contain"
          />
        </div>
        <div class="my-[20px] flex items-center">
          可在前往
          <NuxtLink to="/digital_human/aside/video_compositing" :replace="true">
            <span class="cursor-pointer text-primary"> 视频合成 </span>
          </NuxtLink>
          处查看 ，或留在当前页面
          <span
            v-if="compositeStatus == 4"
            class="cursor-pointer text-primary"
            @click="showLoading = false"
          >
            重新合成视频
          </span>
          <span
            v-else
            class="cursor-pointer text-primary"
            @click="continueCreate"
          >
            继续创作
          </span>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script setup lang="ts">
import { useCanvasStore } from '@/stores/canvas'
import { putVideoRename, checkVideoStatus } from '@/api/digital_human'
const canvasStore = useCanvasStore()
const router = useRouter()
const route = useRoute()
const showLoading = ref(false)
const compositeStatus = ref(-1)
const goBack = () => {
  router.push('/digital_human/aside/video_compositing')
}
const { isLock: saveLoading, lockFn: handleSave } = useLockFn(async () => {
  await canvasStore.savaOrComposite()
  router.replace({
    path: '',
    query: {
      ...route.query,
      id: canvasStore.id
    }
  })
})
const { isLock: compositeLoading, lockFn: compositeVideo } = useLockFn(
  async () => {
    await canvasStore.putImgCover()
    await canvasStore.savaOrComposite(2)
    await checkCompositeStatus()
    showLoading.value = true
  }
)
let timer: any = null
const checkCompositeStatus = async () => {
  const { status } = await checkVideoStatus({
    id: canvasStore.id
  })
  compositeStatus.value = status
  if (status == 2) {
    timer = setTimeout(() => {
      checkCompositeStatus()
    }, 30 * 1000)
  }
}

watch(showLoading, (value) => {
  console.log(value)
  if (!value) {
    timer && clearTimeout(timer)
  }
})

onUnmounted(() => {
  timer && clearTimeout(timer)
})

const changeName = async () => {
  const { value } = await feedback.prompt('修改视频名称', '', {
    inputValue: canvasStore.name
  })
  if (canvasStore.id) {
    await putVideoRename({
      name: value,
      id: canvasStore.id
    })
  }
  canvasStore.name = value
}

const continueCreate = () => {
  canvasStore.voiceContent.text = ''
  canvasStore.voiceContent.voice_url = ''
  showLoading.value = false
  canvasStore.id = undefined
  router.replace({
    path: '',
    query: {
      ...route.query,
      id: undefined
    }
  })
}
</script>
