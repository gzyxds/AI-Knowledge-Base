<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full">
        <!-- #ifdef H5 -->
        <Content />
        <!-- #endif -->
    </view>
    <!-- #ifdef MP-WEIXIN -->
    <web-view :src="url" />
    <!-- #endif -->
</template>

<script setup lang="ts">
import { computed } from 'vue'
// #ifdef H5
import Content from './components/content.vue'
// #endif
import config from '@/config'
import { useUserStore } from '@/stores/user'
import { useRoute } from 'uniapp-router-next'
const userStore = useUserStore()
const route = useRoute()
const id = route.query.id
const url = computed(() => {
    const domain = config.baseUrl
    return `${domain}mobile/packages/pages/kb_info/kb_info?id=${id}&webview=1&token=${userStore.token}`
})
</script>

<style scoped lang="scss">
page {
    height: 100%;
}
</style>
