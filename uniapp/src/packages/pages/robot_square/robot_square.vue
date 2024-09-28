<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="square">
        <view class="py-[10rpx] px-[30rpx] bg-white">
            <u-tabs
                :list="tabList"
                :is-scroll="false"
                :current="tbaIndex"
                :active-color="$theme.primaryColor"
                @change="handleChange"
            ></u-tabs>
        </view>
        <view class="py-[14rpx] px-[30rpx] bg-white">
            <u-search
                v-model="keyword"
                placeholder="请输入关键词搜索"
                height="72"
                bg-color="#F7F8F9"
            />
        </view>
        <view class="flex-1 min-h-0">
            <Robot v-if="tbaIndex === 0" class="w-full h-full" :keyword="keyword" />
            <Draw v-if="tbaIndex === 1" class="w-full h-full" :keyword="keyword" />
            <Music v-if="tbaIndex === 2" class="w-full h-full" :keyword="keyword" />
            <Video v-if="tbaIndex === 3" class="w-full h-full" :keyword="keyword" />
        </view>
        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

import Robot from './_components/robot.vue'
import Draw from './_components/draw.vue'
import Music from './_components/music.vue'
import Video from './_components/video.vue'

const keyword = ref('')
const tbaIndex = ref<number>(0)
const tabList = [
    { name: '智能体', value: 0 },
    { name: 'AI绘画', value: 1 },
    { name: 'AI音乐', value: 2 },
    { name: 'AI视频', value: 3 }
]

const handleChange = (index: number) => {
    if (tbaIndex.value === index) return
    keyword.value = ''
    tbaIndex.value = index
}
</script>

<style lang="scss">
page {
    height: 100%;
}

.square {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #f4f8fd;
    background-size: cover;
}
</style>
