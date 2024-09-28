<template>
    <div class="menu">
        <el-menu :default-active="activeMenu">
            <menu-item
                v-for="item in navList"
                :key="item.id"
                :item="item"
                :showName="true"
                :is-show-icon="isShowIcon"
                :path="item.link.path"
                :is-active="activeMenu === item.link.path"
            />
        </el-menu>
    </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import MenuItem from './menu-item.vue'

interface Props {
    isHome?: boolean
}

const props = defineProps<Props>()

const appStore = useAppStore()
const navList = computed(() => {
    return (
        appStore.pageAside.nav?.filter((item: any) => Number(item.is_show) === 1) ||
        []
    )
})
const isShowIcon = computed(() => {
    return appStore.pageAside.showNavIcon
})
const route = useRoute()
const activeMenu = computed<string>(() => {
    const routePath =
        route.path === '/' ? route.path : route.path.replace(/\/$/, '')
    return route.meta.parentPath || route.meta.activePath || routePath
})
</script>

<style lang="scss" scoped>
.menu {
    :deep() {
        .el-menu {
            border-right: none;
            --el-menu-bg-color: transparent;
            --el-menu-text-color: var(--el-text-color-primary);
            .el-menu-item {
                flex-direction: column;
                line-height: 20px;
                justify-content: center;
                align-items: center;
                padding: 0 12px;
                [class^='el-icon'] {
                    margin-right: 0;
                }
                margin: 18px 0;
                &:hover {
                    background: transparent;
                    @apply text-primary;
                }
                .menu-icon {
                    @apply text-tx-secondary;
                }
                &.is-active {
                    background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    .menu-icon {
                        @apply text-primary;
                    }
                }
            }
        }
    }
}
</style>
