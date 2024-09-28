<template>
  <div>
    <NuxtLayout name="default">
      <div class="flex h-full">
        <div class="h-full px-[16px] py-[16px]">
          <sidePop v-model="sideShow"></sidePop>
        </div>
        <div class="flex-1 min-w-0 h-full">
          <div class="mx-auto py-[16px] rounded-lg pr-[16px] h-full">
            <NuxtPage></NuxtPage>
          </div>
        </div>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import sidePop from './_components/sidePop.vue'

const appStore = useAppStore()

const sideShow = ref(false)

definePageMeta({
  layout: false,
  auth: true,
  hiddenFooter: true
})
</script>

<style scoped lang="scss">
.isSelect {
  position: relative;
  color: var(--el-color-primary);

  &::before {
    content: '';
    position: absolute;
    width: 4px;
    height: 100%;
    background: var(--el-color-primary);
    left: 0;
  }
}
</style>
