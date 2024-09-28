<template>
    <el-container
        class="bg-body h-full layout-default"
        :style="[
            {
                height: `${
                    windowHeight == 'Infinity' ? '100vh' : windowHeight + 'px'
                }`
            }
        ]"
    >
        <el-header height="var(--header-height)" style="padding: 0">
            <LayoutHeader>
                <template v-if="$slots?.header">
                    <slot name="header" />
                </template>
            </LayoutHeader>
        </el-header>
        <el-container class="min-h-0">
            <el-aside width="auto" class="!overflow-visible">
                <LayoutAside>
                    <template v-if="$slots?.aside" #aside>
                        <slot name="aside" />
                    </template>
                </LayoutAside>
            </el-aside>
            <el-container
                class="overflow-hidden layout-bg rounded-[12px]"
                :class="{
                    '': $slots?.aside,
                    '!rounded-none ': $route.meta.hiddenRounded
                }"
            >
                <el-main class="scrollbar" style="padding: 0">
                    <slot />
                </el-main>
                <el-footer v-if="!$route.meta.hiddenFooter" height="auto">
                    <LayoutFooter />
                </el-footer>
            </el-container>
        </el-container>

        <!-- <LayoutTabbar v-if="appStore.isMobile" /> -->
        <LayoutAccount v-if="userStore.showLogin" />
        <NoticePopup />
        <Customer />
    </el-container>
</template>
<script lang="ts" setup>
import { useWindowSize, useDark, useEventListener } from '@vueuse/core'
import LayoutHeader from './components/header/index.vue'
import LayoutAside from './components/aside/index.vue'
import LayoutFooter from './components/footer/index.vue'
import LayoutTabbar from './components/tabbar/index.vue'
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
    isDark.value = settingStore.isDark
    settingStore.setTheme()
}
onMounted(() => {
    changeTheme()
})
onBeforeMount(() => {
    changeTheme()
})
// if (process.client) {
//     useEventListener(document, 'visibilitychange', (value) => {
//         if (document.visibilityState === 'visible') {
//             changeTheme()
//         }
//     })
// }
</script>
<style lang="scss" scoped>
.layout-default {
    min-width: 1200px;
}

.layout-bg {
    background-size: cover;
    background-position: center;
    @apply bg-page;
}
</style>
