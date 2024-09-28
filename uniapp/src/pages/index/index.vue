<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view>
        <view v-for="(item, index) in data" :key="item.id">
            <Title v-if="item.name === 'index-title'" :prop="item.content" :isHidden="!item.isHidden" :percent="percent" />
            <Banner v-if="item.name === 'index-banner'" :prop="item.content" :isHidden="!item.isHidden" />
            <Ad v-if="item.name === 'index-ad'" :prop="item.content" :isHidden="!item.isHidden" />
            <Menu v-if="item.name === 'index-menu'" :prop="item.content" :isHidden="!item.isHidden" />
            <Hot v-if="item.name === 'index-hot'" :prop="item.content" :isHidden="!item.isHidden" />
        </view>

        <!--  #ifdef H5  -->
        <view class="text-center py-4">
            <a
                class="flex items-center justify-center mx-1 text-xs text-[#495770]"
                :href="item.value"
                target="_blank"
                v-for="item in appStore.getCopyrightConfig"
                :key="item.key"
            >
                <img
                    v-if="item.icon"
                    :src="item.icon"
                    alt="备案图标"
                    style="width: 40rpx; height: 40rpx;"
                >
                <text class="mx-1">{{ item.key }}</text>
            </a>
        </view>
        <!--  #endif  -->

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { getDecorate } from '@/api/shop'
import { ref } from 'vue'
import Title from './components/title.vue'
import Banner from './components/banner.vue'
import Ad from './components/ad.vue'
import Menu from './components/menu.vue'
import Hot from './components/hot.vue'
import {useAppStore} from '@/stores/app'
import {onPageScroll} from "@dcloudio/uni-app";

const appStore = useAppStore()
const data = ref<any[]>([])
const scrollTop = ref<number>(0)
const percent = ref<number>(0)

const getData = async () => {
    try {
        const result = await getDecorate({ id: 7 })
        data.value = JSON.parse(result.data)
    } catch (error) {
        console.error(error)
    }
}


onPageScroll((event: any) => {
    scrollTop.value = event.scrollTop
    const top = uni.upx2px(100)
    percent.value = event.scrollTop / top > 0.8 ? 0.8 : event.scrollTop / top
})

getData()
</script>

<style lang="scss" scoped>
page {
    min-height: 100%;
    background-color: #f4f8fd;
}
</style>
