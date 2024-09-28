<template>
    <view class="h-full flex flex-col">
        <u-navbar
            v-if="!isMiniProgram"
            :title="KBName"
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
        <view class="flex-1 min-h-0 pt-[20rpx]">
            <dataStudy v-if="tabState.current == 0"></dataStudy>
            <dataTest v-if="tabState.current == 1"></dataTest>
            <setup v-if="tabState.current == 2"></setup>
            <team v-if="tabState.current == 3"></team>
        </view>
    </view>
</template>
<script lang="ts">
export default {
    options: {
        virtualHost: true
    }
}
</script>
<script setup lang="ts">
import { reactive } from 'vue'
import dataStudy from './dataStudy.vue'
import dataTest from './dataTest.vue'
import setup from './setup.vue'
import team from './team.vue'
import { onLoad } from '@dcloudio/uni-app'
import { useKB } from '../useKb'
import { isMiniProgram } from '@/utils/env'
const { getKBInfo, KBName, setKBId } = useKB()

const tabState = reactive({
    list: [
        {
            name: '数据学习',
            type: 'study'
        },
        {
            name: '数据测试',
            type: 'test'
        },
        {
            name: '知识库设置',
            type: 'setup'
        },
        {
            name: '团队成员',
            type: 'team'
        }
    ],
    current: 0
})

const tabsChange = (index: number) => {
    tabState.current = index
    // currentTab.value = currentTabType.value
}

onLoad(async (option: any) => {
    setKBId(option.id)
    await getKBInfo()
    uni.setNavigationBarTitle({
        title: String(KBName.value || '知识库详情')
    })
})
</script>
