<template>
    <div class="layout-aside h-full flex">
        <div class="h-full flex justify-between flex-col">
            <ElScrollbar class="w-[80px] el-scrollbar">
                <Nav class="mb-auto" />
            </ElScrollbar>
            <Menu />
        </div>
        <div v-show="!appStore.isCollapsed" class="h-full">
            <!-- <el-drawer
              v-if="isMobile"
              v-model="showPanelDrawer"
              direction="ltr"
              size="auto"
              :with-header="false"
              custom-class="panel-drawer"
            >
              <Panel>
                <slot name="aside" />
              </Panel>
            </el-drawer> -->
            <Panel>
                <!-- <TitleLogo
                  v-if="$route.meta.showLogo || !appStore.isMobile"
                  :class="{ 'mr-[50px]': !appStore.isMobile }"
                  :logo="appStore.getWebsiteConfig.pc_logo"
                  :title="appStore.getWebsiteConfig.pc_name"
                /> -->
                <slot name="aside" />
            </Panel>
        </div>

        <div
            v-if="$slots.panel && !isMobile"
            class="panel-left-arrow"
            @click="appStore.toggleCollapsed()"
        >
            <Icon
                class="mr-1"
                :name="`el-icon-${
                    appStore.isCollapsed ? 'CaretRight' : 'CaretLeft'
                }`"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import Panel from './panel.vue'
import { useAppStore } from '@/stores/app'
import Menu from './menu.vue'
import Nav from './nav.vue'

const appStore = useAppStore()
const isMobile = computed(() => appStore.isMobile)
const showPanelDrawer = computed({
    get() {
        return !appStore.isCollapsed && isMobile.value
    },
    set(value) {
        appStore.toggleCollapsed(!value)
    }
})
</script>
<style lang="scss" scoped>
.layout-aside {
    position: relative;

    // .el-scrollbar :deep(.el-scrollbar__view) {
    //     height: 100%;
    // }

    :deep(.panel-drawer) {
        .el-drawer__body {
            padding: 0;
        }
    }

    .panel-left-arrow {
        position: absolute;
        width: 16px;
        height: 50px;
        top: 50%;
        right: -15px;
        transform: translateY(-50%);
        z-index: 10;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        // &::before {
        //   content: '';
        //   position: absolute;
        //   border-radius: 0 10px 10px 0;
        //   box-sizing: border-box;
        //   top: 0;
        //   right: 0;
        //   bottom: 0;
        //   left: 0;
        //   transform: perspective(50px) rotateY(30deg);
        //   transform-style: preserve-3d;
        //   background-color: var(--aside-bg-color);
        //   @apply border-br;
        // }
    }
}
</style>
