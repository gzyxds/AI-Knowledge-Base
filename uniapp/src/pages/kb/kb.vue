<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="flex-1 min-h-0">
        <z-paging-swiper :fixed="false" :swiper-style="{ height: '100%' }">
            <!-- 需要固定在顶部不滚动的view放在slot="top"的view中 -->
            <!-- 注意！此处的z-tabs为独立的组件，可替换为第三方的tabs，若需要使用z-tabs，请在插件市场搜索z-tabs并引入，否则会报插件找不到的错误 -->
            <template #top>
                <u-tabs
                    :is-scroll="false"
                    ref="tabs"
                    :active-color="$theme.primaryColor"
                    :list="tabState.list"
                    :current="tabState.current"
                    @change="tabsChange"
                />
            </template>
            <!-- swiper必须设置height:100%，因为swiper有默认的高度，只有设置高度100%才可以铺满页面  -->
            <swiper
                class="h-full"
                :current="tabState.current"
                @change="swiperChange"
            >
                <swiper-item
                    class="swiper-item h-full"
                    v-for="(item, index) in tabState.list"
                    :key="index"
                >
                    <Robot
                        ref="tabsItemRef"
                        v-if="item.type == 'robot'"
                        :tab-index="index"
                        :current-index="tabState.current"
                    />
                    <Kb
                        ref="tabsItemRef"
                        v-if="item.type == 'kb'"
                        :tab-index="index"
                        :current-index="tabState.current"
                    />
                    <Digital
                        ref="tabsItemRef"
                        v-if="item.type == 'digital'"
                        :tab-index="index"
                        :current-index="tabState.current"
                    />
                </swiper-item>
            </swiper>
        </z-paging-swiper>
    </view>
    <tabbar />
</template>

<script setup lang="ts">
import { reactive, shallowRef } from 'vue'
import Robot from './components/robot/index.vue'
import Kb from './components/kb/index.vue'
import Digital from './components/digital/index.vue'
import { onLoad, onShow } from '@dcloudio/uni-app'
const tabState = reactive({
    list: [
        {
            name: '智能体应用',
            type: 'robot'
        },
        {
            name: '知识库',
            type: 'kb'
        },
        {
            name: '虚拟形象',
            type: 'digital'
        }
    ],
    current: 0
})
const tabsItemRef = shallowRef()
const tabsChange = (index: number) => {
    tabState.current = index
}

const swiperChange = (e: any) => {
    tabState.current = e.detail.current
}

onShow(() => {
    setTimeout(() => {
        tabsItemRef.value?.forEach((item: any) => {
            item.reload?.()
        })
    })
})
</script>

<style>
page {
    height: 100%;
    display: flex;
    flex-direction: column;
    background-color: #f4f8fd;
}
</style>
