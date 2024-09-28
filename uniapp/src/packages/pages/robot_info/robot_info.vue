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
        <u-navbar
            :title="robotName"
            title-color="#333"
            :title-bold="true"
        ></u-navbar>
        <u-tabs
            :is-scroll="false"
            ref="tabs"
            :active-color="$theme.primaryColor"
            :list="tabState.list"
            :current="tabState.current"
            @change="tabsChange"
        />
        <view class="flex-1 min-h-0">
            <RobotSetting v-if="currentTabType == 'setting'" />
            <ReleaseRobot v-if="currentTabType == 'release'" />
            <Dialogue v-if="currentTabType == 'dialogue'" />
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'uniapp-router-next'
import RobotSetting from './component/robot-setting/index.vue'
import ReleaseRobot from './component/release-robot/index.vue'
import Dialogue from './component/dialogue/index.vue'
import { useRobot } from './useRobot'
import { onLoad, onShow } from '@dcloudio/uni-app'
const route = useRoute()
const router = useRouter()
const { getRobotInfo, currentTab, setRobotId, reset, robotName } = useRobot()
const tabState = reactive({
    list: [
        {
            name: '智能体设置',
            type: 'setting'
        },
        {
            name: '发布智能体',
            type: 'release'
        },
        {
            name: '对话数据',
            type: 'dialogue'
        },
        {
            name: '开始对话',
            type: 'begin'
        }
    ],
    current: 0
})

const currentTabType = computed(() => tabState.list[tabState.current].type)
const tabsChange = (index: number) => {
    tabState.current = index
    if (currentTabType.value == 'begin') {
        router.navigateTo({
            path: '/packages/pages/robot_chat/robot_chat',
            query: {
                id: route.query.id
            }
        })
        return
    }
    currentTab.value = currentTabType.value
}

setRobotId(route.query.id as string)
onLoad(() => {
    getRobotInfo()
})

onUnmounted(() => {
    reset()
})

watch(
    () => route.query.type,
    (value) => {
        const index = tabState.list.findIndex(
            (item) => item.type == value || item.type == currentTab.value
        )

        // 确保u-tabs已渲染
        setTimeout(() => {
            if (index > -1) {
                tabState.current = index
            } else {
                tabState.current = 0
            }
        }, 10)
    },
    {
        immediate: true
    }
)
</script>
<style>
page {
    height: 100%;
}
</style>
