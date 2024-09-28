<template>
    <div class="layout-header h-full flex items-center">
        <!-- <Fold v-if="appStore.isMobile && $route.meta.hasPanel" /> -->
        <TitleLogo
            :class="'mr-[50px]'"
            :logo="appStore.getWebsiteConfig.pc_logo"
            :title="appStore.getWebsiteConfig.pc_name" />
        <!-- <div
      v-if="$route.meta.title && !$route.meta.showLogo && appStore.isMobile"
      class="text-lg font-medium"
    >
      {{ $route.meta.title }}
    </div> -->
        <div class="flex-1 min-w-0">
            <!-- <Menu /> -->
            <div class="">
                <slot />
            </div>
        </div>
        <User class="ml-auto" />
    </div>
</template>
<script lang="ts" setup>
import Fold from './fold.vue'
import TitleLogo from './title-logo.vue'
import Menu from './menu.vue'
import User from './user.vue'
import { useAppStore } from '@/stores/app'
const appStore = useAppStore()

// const styles = computed(() => {
//   return settingStore.sideTheme === 'dark'
//     ? {
//         '--aside-bg-color': settingStore.sideDarkColor,
//         color: 'var(--color-white)'
//       }
//     : {
//         color: 'var(--el-text-color-primary)'
//       }
// })
</script>

<style lang="scss" scoped>
.layout-header {
    // background-color: var(--aside-bg-color);
    padding: 0 var(--main-padding);
    position: relative;
    //   &::after {
    //     position: absolute;
    //     bottom: 0;
    //     left: 0;
    //     content: '';
    //     display: block;
    //     width: 100%;
    //     height: 1px;
    //     background-color: var(--color-white);
    //     transform: scaleY(0.3);
    //   }
}
</style>
