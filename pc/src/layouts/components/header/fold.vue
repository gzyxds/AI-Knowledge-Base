<template>
  <div
    class="fold h-full cursor-pointer flex items-center pr-2 text-white"
    @click="toggleCollapsed"
  >
    <Icon :name="`local-icon-${isCollapsed ? 'close' : 'open'}`" :size="24" />
  </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
const appStore = useAppStore()
const isCollapsed = computed(() => appStore.isCollapsed)
// 折叠展开菜单
const toggleCollapsed = () => {
  appStore.toggleCollapsed()
}
</script>
