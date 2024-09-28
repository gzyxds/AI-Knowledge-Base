<template>
  <DefineTemplate v-slot="{ icon, text }">
    <div
      class="w-[54px] py-[8px] my-[10px] bg-body shadow-light rounded-lg cursor-pointer"
    >
      <div class="flex flex-col items-center justify-center">
        <ElImage v-if="icon" class="w-[30px] h-[30px] mb-[4px]" :src="icon" />
        <div class="text-xs">{{ text }}</div>
      </div>
    </div>
  </DefineTemplate>
  <div>
    <div class="absolute right-[10px] top-[60%] z-[9999]">
      <Manual v-if="customerManual.status == 1">
        <ReuseTemplate :icon="customerManual.icons" text="客服" />
      </Manual>
      <Online v-if="customerOnline.status == 1">
        <ReuseTemplate :icon="customerOnline.icons" text="在线客服" />
      </Online>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import Manual from './manual.vue'
import Online from './online.vue'
const appStore = useAppStore()
const [DefineTemplate, ReuseTemplate] = useTemplate<{
  icon: string
  text: string
}>()
const customerOnline = computed(() => appStore.getOnlineKf)
const customerManual = computed(() => appStore.getManualKf)
</script>

<style scoped lang="scss"></style>
