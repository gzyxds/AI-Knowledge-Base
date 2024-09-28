<template>
  <section class="layout-blank">
    <slot />
    <LayoutAccount />
  </section>
</template>
<script lang="ts" setup>
import { useDark, useEventListener } from '@vueuse/core'
import LayoutAccount from './components/account/index.vue'
import { useSettingStore } from '@/stores/setting'
// const settingStore = useSettingStore()
// const isDark = useDark()
// const changeTheme = () => {
//     isDark.value = false
//     settingStore.setTheme(false)
// }
// onMounted(() => {
//     console.log('blank.vue onMounted')
//     changeTheme()
// })
// onBeforeMount(() => {
//     console.log('blank.vue onBeforeMount')
//     changeTheme()
// })
</script>
