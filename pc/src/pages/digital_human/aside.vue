<template>
  <NuxtLayout name="default">
    <template #panel>
      <div
        class="bg-white flex flex-col h-full w-[150px] text-tx-primary tab-list"
      >
        <ElScrollbar>
          <LayoutSide :nav-list="navList" />
        </ElScrollbar>
      </div>
    </template>
    <NuxtPage />
  </NuxtLayout>
</template>
<script setup lang="ts">
const navList = [
  {
    name: '视频合成',
    icon: 'el-icon-VideoCamera',
    path: '/digital_human/aside/video_compositing'
  }
]

definePageMeta({
  layout: false,
  hasPanel: true
})
</script>
<style lang="scss" scoped></style>
