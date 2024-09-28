<template>
  <div>
    <NuxtLayout name="single-row">
      <div class="h-full">
        <ElScrollbar>
          <div class="index">
            <div v-for="item in appStore.pageIndex" :key="item.id">
              <component
                v-if="item.isShow"
                :is="widgets[item.name]"
                :prop="item.prop"
              />
            </div>
          </div>
        </ElScrollbar>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import widgets from './_components'
// import { useDark, useToggle } from '@vueuse/core'
// import { useSettingStore } from '@/stores/setting'
//
// const settingStore = useSettingStore()
const appStore = useAppStore()

definePageMeta({
  layout: false,
  showLogo: true
})
</script>

<style lang="scss" scoped></style>
