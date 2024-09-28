<template>
    <el-affix
        target=""
        style="height: 0; width: 100%"
        :offset="0"
        @scroll="onScroll">
        <div
            class="header w-full h-[60px] flex items-center justify-center"
            :style="{ background: 'rgba(256,256, 256,' + percent + ')' }">
            <div class="max-w-[1200px] w-full mx-auto flex items-center">
                <TitleLogo
                    :class="'mr-[50px]'"
                    :logo="appStore.getWebsiteConfig.pc_logo"
                    :title="appStore.getWebsiteConfig.pc_name" />
                <div class="flex-1 min-w-0">
                    <Menu :is-home="true" />
                </div>

                <User class="ml-auto" :isHidden="true" />
            </div>
        </div>
    </el-affix>
</template>
<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import TitleLogo from '~/layouts/components/header/title-logo.vue'
import Menu from '~/layouts/components/header/menu.vue'
import User from '~/layouts/components/header/user.vue'

const props = defineProps<{
    prop: Record<string, any>
}>()
const appStore = useAppStore()

const percent = ref<number>(0)
const onScroll = ({ scrollTop }: { scrollTop: number }) => {
    const top = 80
    percent.value = scrollTop / top > 0.8 ? 0.8 : scrollTop / top
}
</script>

<style lang="scss" scoped>
.header {
    padding: 0 var(--main-padding);
}
</style>
