<template>
  <div
    class="setting flex cursor-pointer h-full items-center pl-2"
    @click="openSetting"
  >
    <Icon :size="20" name="local-icon-dianpu_fengge" />
    <layout-setting />
  </div>
</template>

<script setup lang="ts">
import LayoutSetting from './drawer.vue'
import { useSettingStore } from '@/stores/setting'
const settingStore = useSettingStore()

const openSetting = () => {
  settingStore.setSetting({
    key: 'showDrawer',
    value: true
  })
}
</script>
