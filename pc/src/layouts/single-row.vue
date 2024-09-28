<template>
    <el-container
        class="layout-bg h-full layout-default"
        :style="[
            {
                height: `${
                    windowHeight == 'Infinity' ? '100vh' : windowHeight + 'px'
                }`
            }
        ]"
    >
        <el-container class="min-h-0">
            <el-main style="padding: 0">
                <slot />
            </el-main>
            <el-footer v-if="!$route.meta.hiddenFooter" height="auto">
                <LayoutFooter />
            </el-footer>
        </el-container>

        <LayoutAccount v-if="userStore.showLogin" />
        <NoticePopup />
        <Customer />
    </el-container>
</template>
<script lang="ts" setup>
import { useWindowSize, useDark, useEventListener } from '@vueuse/core'
import LayoutFooter from './components/footer/index.vue'
import LayoutAccount from './components/account/index.vue'
import NoticePopup from './components/notice/index.vue'
import Customer from './components/customer/index.vue'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import { useSettingStore } from '@/stores/setting'
const appStore = useAppStore()
const userStore = useUserStore()
const settingStore = useSettingStore()
const isDark = useDark()
const mobileCss = computed(() => {
    return appStore.isMobile
        ? {
              '--header-height': '50px',
              '--main-padding': '12px'
          }
        : {
              '--main-padding': '15px'
          }
})
const { height: windowHeight } = useWindowSize()
const changeTheme = () => {
    isDark.value = false
    settingStore.setTheme(false)
}
onMounted(() => {
    changeTheme()
})
onBeforeMount(() => {
    changeTheme()
})
</script>
<style lang="scss" scoped>
.layout-default {
    min-width: 1200px;
}

.layout-bg {
    background: url(../assets/image/layout_bg.png) no-repeat;
    background-size: cover;
    background-position: center;
}
</style>
