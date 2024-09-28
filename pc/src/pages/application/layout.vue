<template>
    <div>
        <NuxtLayout name="default">
            <!-- <template #aside>
        <div
          class="robot-aside flex flex-col h-full w-[90px] text-tx-primary tab-list"
        >
          <ElScrollbar>
            <LayoutSide :nav-list="navList" />
          </ElScrollbar>
        </div>
      </template> -->
            <div class="h-full flex flex-col">
                <NavList class="px-[20px] pt-[16px]" :nav-list="navList" />
                <div class="flex-1 min-h-0">
                    <NuxtPage />
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
const navList = [
    {
        name: '智能体',
        path: '/application/layout/robot'
    },
    {
        name: '智能体形象',
        path: '/application/layout/digital'
    },
    {
        name: '知识库',
        path: '/application/layout/kb'
    }
]

definePageMeta({
    layout: false,
    hasPanel: true
})
</script>
<style lang="scss" scoped>
.robot-aside {
}
</style>
