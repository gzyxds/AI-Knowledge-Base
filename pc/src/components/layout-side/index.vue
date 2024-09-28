<template>
  <div class="tab-list">
    <el-menu :default-active="currentPath" router style="border: none">
      <el-menu-item v-for="item in navList" :key="item.path" :index="item.path">
        <span class="menu-icon">
          <Icon :size="20" :name="item.icon" />
        </span>

        <span class="mt-[10px] text-sm">{{ item.name }}</span>
      </el-menu-item>
    </el-menu>
  </div>
</template>
<script setup lang="ts">
interface NavItem {
  name: string
  icon: string
  path: string
}
const props = defineProps<{
  navList: NavItem[]
}>()

const route = useRoute()
const currentPath = computed(() => {
  const routePath =
    route.path === '/' ? route.path : route.path.replace(/\/$/, '')
  return route.meta.activePath || routePath
})
</script>
<style lang="scss" scoped>
.tab-list {
  padding: 10px 0;
  :deep() {
    .el-menu {
      border-right: none;
      --el-menu-bg-color: transparent;
      .el-menu-item {
        flex-direction: column;
        line-height: 20px;
        justify-content: center;
        align-items: center;
        padding: 0 12px;
        [class^='el-icon'] {
          margin-right: 0;
        }
        margin: 25px 0;
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
