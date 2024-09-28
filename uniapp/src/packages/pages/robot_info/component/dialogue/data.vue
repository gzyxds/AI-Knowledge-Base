<template>
    <view class="h-full w-full overflow-hidden">
        <scroll-view class="h-full" scroll-y>
            <view class="flex flex-wrap px-[20rpx] mx-[-15rpx]">
                <view
                    class="w-[50%] px-[15rpx] mb-[30rpx]"
                    v-for="item in dataList"
                    :key="item.key"
                >
                    <view
                        class="bg-[#fafafc] flex flex-col items-center rounded-[20rpx] px-[20rpx] py-[30rpx] border border-solid border-light h-full"
                    >
                        <view class="text-lg">{{ item.title }}</view>
                        <view class="text-[60rpx] mt-[24rpx]">
                            {{ get(data, item.key) }}
                        </view>
                    </view>
                </view>
            </view>
        </scroll-view>
    </view>
</template>
<script lang="ts" setup>
import { getRobotChatData } from '@/api/robot'
import { useRoute } from 'uniapp-router-next'
import { get } from 'lodash-es'
import { ref } from 'vue'
import { onShow } from '@dcloudio/uni-app'

const dataList = ref([
    {
        title: '今日对话次数',
        key: 'robot.todayChatCount'
    },
    {
        title: '昨日对话次数',
        key: 'robot.yesterdayChatCount'
    },
    {
        title: '本周对话次数',
        key: 'robot.weekChatCount'
    },
    {
        title: '全部对话次数',
        key: 'robot.wholeChatCount'
    },
    {
        title: '今日访问用户/人',
        key: 'visitor.todayVisitorCount'
    },
    {
        title: '昨日访问用户/人',
        key: 'visitor.yesterdayVisitorCount'
    },
    {
        title: '本周访问用户/人',
        key: 'visitor.weekVisitorCount'
    },
    {
        title: '全部用户/人',
        key: 'visitor.wholeVisitorCount'
    }
])
const route = useRoute()
const data = ref<any[]>([])
const getData = async () => {
    data.value = await getRobotChatData({
        robot_id: route.query.id
    })
}
getData()
</script>
