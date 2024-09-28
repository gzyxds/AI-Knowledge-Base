<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col">
        <u-navbar :back-text="navigationBarTitleStore.getTitle || '我的作品'">
            <template #right>
                <view
                    class="flex items-center mr-[20rpx] text-primary border-[1px] border-primary border-solid text-sm py-[7rpx] px-[16rpx] rounded-[8rpx]"
                    @click="isBatch = !isBatch"
                >
                    {{ isBatch ? '取消' : '批量管理' }}
                </view>
            </template>
        </u-navbar>
        <view class="m-[20rpx] bg-white rounded-[16rpx] overflow-hidden">
            <u-tabs
                :list="tabList"
                v-model="current"
                :is-scroll="true"
                :active-color="$theme.primaryColor"
            ></u-tabs>
        </view>
        <view class="flex-1 min-h-0">
            <VideoList
                class="w-full h-full"
                v-if="currentComponent == 'video'"
                :isBatch="isBatch"
            />
            <MusicList
                class="w-full h-full"
                v-if="currentComponent == 'music'"
                :isBatch="isBatch"
            />
            <DrawList
                class="w-full h-full"
                v-if="currentComponent == 'draw'"
                :isBatch="isBatch"
            />
        </view>
    </view>
</template>

<script setup lang="ts">
import { useNavigationBarTitleStore } from '@/stores/navigationBarTitle'
import { ref, computed } from 'vue'
import MusicList from './components/music.vue'
import VideoList from './components/video.vue'
import DrawList from './components/draw.vue'
const navigationBarTitleStore = useNavigationBarTitleStore()
const isBatch = ref(false)
const current = ref(0)
const tabList = ref([
    {
        name: 'AI音乐',
        component: 'music'
    },
    {
        name: 'AI视频',
        component: 'video'
    },
    {
        name: 'AI绘画',
        component: 'draw'
    }
])

const currentComponent = computed(() => tabList.value[current.value].component)
</script>
<style lang="scss">
page {
    height: 100%;
    overflow: hidden;
}
</style>
