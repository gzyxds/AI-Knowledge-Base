<template>
  <div class="menu">
    <el-menu :default-active="activeMenu" mode="horizontal">
      <menu-item
        v-for="item in navList"
        :key="item.id"
        :item="item"
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
  isHome: boolean
}

const props = defineProps<Props>()

const appStore = useAppStore()
const navList = computed(() => {
  return appStore.getHeaderConfig.nav?.filter((item: any) => item.isShow) || []
})
const isShowIcon = computed(() => {
  return appStore.getHeaderConfig.isShowIcon && !props.isHome
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
  .el-menu {
    border: none;
    --el-menu-base-level-padding: 20px;
    --el-menu-item-height: 34px;
    --el-menu-horizontal-height: auto;
    --el-menu-item-font-size: var(--el-font-size-large);
    background-color: transparent;
    :deep(.el-menu-item) {
      > span {
        // border-bottom: 2px solid transparent;
      }
      &:not(.is-disabled).is-active,
      &:hover {
        background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }
    }
  }
}
</style>
