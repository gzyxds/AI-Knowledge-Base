<template>
  <ElDialog v-model="showNotice" width="600" append-to-body>
    <template #header>
      <div class="text-lg text-center font-medium">公告</div>
    </template>
    <el-scrollbar max-height="400px">
      <div class="richText" v-html="richTextContent"></div>
    </el-scrollbar>
    <div class="flex-1 flex justify-center items-center bg-body pt-[20px]">
      <el-button type="primary" size="large" @click="showNotice = false">
        我知道了
      </el-button>
    </div>
  </ElDialog>
</template>

<script setup lang="ts">
import { watch, computed, ref } from 'vue'
import { ElDialog } from 'element-plus'
import { useAppStore } from '~/stores/app'
import { NOTICE_KEY } from '@/enums/cacheEnums'

const appStore = useAppStore()
const showNotice = ref<boolean>(false)
const richTextContent = computed(
  () => appStore.getBulletinConfig.bulletin_content
)
const isBulletin = computed(() => appStore.getBulletinConfig.is_bulletin)

const shouldShowNotice = (value: number) => {
  const lastVisitTime = useCookie(NOTICE_KEY)
  const currentTime = new Date().toDateString()
  const isNewDay = !lastVisitTime.value || lastVisitTime.value !== currentTime
  if (isNewDay && value) {
    lastVisitTime.value = currentTime
  }
  return isNewDay
}

watch(
  () => isBulletin.value,
  (val) => {
    if (val && shouldShowNotice(val)) {
      showNotice.value = true
    }
  },
  {
    deep: true,
    immediate: true
  }
)
</script>

<style lang="scss" scoped>
:deep(.richText) {
  img {
    display: inline-block;
  }
}
</style>
