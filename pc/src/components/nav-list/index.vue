<template>
  <div class="flex">
    <div class="flex bg-body p-[8px] rounded-[10px]">
      <NuxtLink v-for="item in navList" :key="item.path" :to="item.path">
        <div
          class="text-xl px-[17.5px] py-[5px] rounded-[7px] min-w-[85px] text-center font-bold"
          :class="{
            'text-white bg-primary': item.path == currentPath
          }"
        >
          {{ item.name }}
        </div>
      </NuxtLink>
    </div>
  </div>
</template>
<script setup lang="ts">
interface NavItem {
  name: string
  icon?: string
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
  display: flex;
  padding: 16px;
  // :deep() {
  //   .el-menu {
  //     border-right: none;
  //     .el-menu-item {
  //       flex-direction: column;
  //       line-height: 20px;
  //       justify-content: center;
  //       align-items: center;
  //       padding: 0 12px;
  //       [class^='el-icon'] {
  //         margin-right: 0;
  //       }
  //       &:hover {
  //         background: transparent;
  //         @apply text-primary;
  //       }
  //       .menu-icon {
  //         @apply text-tx-secondary;
  //       }
  //       &.is-active {
  //         background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
  //         -webkit-background-clip: text;
  //         -webkit-text-fill-color: transparent;
  //         background-clip: text;
  //         .menu-icon {
  //           @apply text-primary;
  //         }
  //       }
  //     }
  //   }
  // }
}
</style>
