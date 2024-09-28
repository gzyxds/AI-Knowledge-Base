<template>
  <div class="tabbar">
    <div class="tabbar__content">
      <template v-for="(item, index) in navList" :key="index">
        <NuxtLink
          :to="{
            path: item.link.path,
            replace: true
          }"
          class="flex-1"
        >
          <div
            class="tabbar__content__item w-full"
            :class="{
              active: currentPath === item.link.path
            }"
          >
            <div class="tabbar__content__item__icon">
              <!-- <Icon :name="`local-icon-${item.icon}`" :size="18" /> -->
              <el-image
                v-if="currentPath === item.link.path"
                class="w-[18px] h-[18px]"
                :src="appStore.getImageUrl(item.selected)"
              ></el-image>
              <el-image
                v-else
                class="w-[18px] h-[18px]"
                :src="appStore.getImageUrl(item.unselected)"
              ></el-image>
            </div>
            <div class="tabbar__content__item__text">
              {{ item.name }}
            </div>
          </div>
        </NuxtLink>
      </template>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useAppStore } from '@/stores/app'
const route = useRoute()
const appStore = useAppStore()
const navList = computed(() => {
  return appStore.getHeaderConfig?.nav?.filter((item: any) => item.isShow) || []
})
const currentPath = computed(() => {
  const routePath =
    route.path === '/' ? route.path : route.path.replace(/\/$/, '')
  return route.meta.parentPath || route.meta.activePath || routePath
})
</script>
<style lang="scss" scoped>
$tabbar-height: 50px;
.tabbar {
  height: $tabbar-height;
  border-top: 1px solid;
  padding-bottom: env(safe-area-inset-bottom) !important;
  box-sizing: content-box;
  @apply border-br bg-white;
  &__content {
    display: flex;
    flex-direction: row;
    align-items: center;
    height: 100%;
    width: 100%;
    position: relative;
    z-index: 998;
    box-sizing: content-box;
    &__item {
      flex: 1;
      justify-content: center;
      height: 100%;
      padding: 6px 0;
      display: flex;
      flex-direction: row;
      flex-direction: column;
      align-items: center;
      position: relative;
      color: #999;
      &.active {
        color: var(--el-color-primary);

        &::after {
          content: '';
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          border-radius: 50%;
          width: 18px;
          height: 18px;
          background: var(--el-color-primary);
          filter: blur(10px);
          box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.25);
        }
      }
      &__icon {
        position: relative;
        display: inline-block;
        margin-top: 5px;
      }
      &__text {
        font-size: 10px;
        line-height: normal;
        margin-top: 3px;
      }
    }
  }
}
</style>
