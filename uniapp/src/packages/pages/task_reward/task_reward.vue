<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status :status="status">
        <view class="task-center">
            <view
                class="bg-primary text-white px-[30rpx] pt-[30rpx] pb-[80rpx] mb-[-50rpx]"
            >
                <text class="text-base">{{ appStore.getTokenUnit }}数量</text>
                <text class="text-[48rpx] font-medium">
                    {{ userInfo.balance || 0 }}
                </text>
            </view>
            <view class="px-[30rpx] pb-[20rpx]">
                <view class="daily-tasks" v-if="taskData.content.length">
                    <view class="tasks-title">
                        <view class="font-medium text-xl">
                            {{ taskData.title }}
                        </view>
                        <view class="ml-[14rpx] text-sm">
                            {{ taskData.subTitle }}
                        </view>
                    </view>
                    <view class="tasks-content">
                        <view
                            class="tasks-item m-[24rpx] pb-[24rpx] flex"
                            v-for="(item, index) in taskData.content"
                            :key="index"
                        >
                            <u-icon
                                class="flex-none"
                                :name="item.image"
                                :size="88"
                            />
                            <view class="flex-1 min-w-0 ml-[20rpx]">
                                <view class="text-lg font-medium">
                                    {{ item.customName || item.name }}
                                    ({{ typeMap[item.type].num }})
                                </view>
                                <view
                                    class="mt-[10rpx] text-xs text-muted text-justify"
                                >
                                    <text>{{ typeMap[item.type].desc }}</text>
                                    <text class="text-error">{{item.data?.one_award}}</text>
                                    <text>{{ appStore.getTokenUnit }}</text>
                                </view>
                            </view>
                            <view class="w-[150rpx] flex justify-end items-center">
                                <u-button
                                    type="primary"
                                    shape="circle"
                                    size="medium"
                                    :customStyle="{
                                        margin: '0',
                                        padding: '0 24rpx',
                                        height: '56rpx'
                                    }"
                                    :disabled="item.data.num >= item.data.day_num"
                                    :open-type="(item.type == 2 || item.type == 3) ? 'share' : ''"
                                    @click="handleShare(item.type)"
                                >
                                    {{ typeMap[item.type].btn_text }}
                                </u-button>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
        <template #error>
            <u-empty text="加载出错～"></u-empty>
        </template>
    </page-status>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
    <tabbar/>
</template>

<script setup lang="ts">
import {getShareTaskList, signClick} from '@/api/task_reward'
import {PageStatusEnum} from '@/enums/appEnums'
import {useCopy} from '@/hooks/useCopy'
import {useShareMessage} from '@/hooks/useShareMessage'
import {useUserStore} from '@/stores/user'
import {handleClientEvent} from '@/utils/client'
import {onPullDownRefresh, onShareAppMessage, onShareTimeline, onShow} from '@dcloudio/uni-app'
import {storeToRefs} from 'pinia'
import {ref, computed} from 'vue'
import {useAppStore} from "@/stores/app";
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
import {getDecorate} from "@/api/shop";
import {useRouter} from "uniapp-router-next";

interface TaskRewardItem {
    customName: string
    data: {
        day_num: number
        is_open: 0 | 1
        num: number
        one_award: number
    }
    title: string
    image: string
    name: string
    show: 0 | 1
    type: number
}

const status = ref(PageStatusEnum.LOADING)
const {copy} = useCopy()
const router = useRouter()
const appStore = useAppStore()
const {resolveOptions} = useShareMessage()

const taskData = ref<any>({
    title: '',
    subTitle: '',
    content: []
})

const typeMap: Record<number, {
    num: string
    btn_text: string
    desc: string
    payload: string
}> = {
    1: {num: '0/1', btn_text: '立即签到', desc: '每天签到，可获得',  payload: '每天签到，可获得' },
    2: {num: '0/10', btn_text: '去分享', desc: '邀请1人，可获得',  payload: '邀请1人，可获得' },
    3: {num: '0/3', btn_text: '去分享', desc: '分享1次，可获得',  payload: '分享1次，可获得' },
    4: {num: '0/4', btn_text: '去分享', desc: '分享1次，可获得',  payload: '分享1次，可获得' },
    5: {num: '0/3', btn_text: '去分享', desc: '分享1次，可获得',  payload: '分享1次，可获得' },
    6: {num: '0/3', btn_text: '去分享', desc: '分享1次，可获得',  payload: '分享1次，可获得' },
    7: {num: '0/3', btn_text: '去分享', desc: '分享1次，可获得',  payload: '分享1次，可获得' }
}

const currentType = ref<number>(-1)
const handleShare = async (type: number) => {
    switch (type) {
        case 1:
            await signClick()
            await getData()
            await userStore.getUser()
            break
        case 4:
            await router.navigateTo('/packages/pages/draw_list/draw_list')
            break
        case 5:
            await router.navigateTo('/packages/pages/video_list/video_list')
            break
        case 6:
            await router.navigateTo('/packages/pages/music_list/music_list')
            break
        case 7:
            await router.navigateTo('/pages/kb/kb')
            break
        default:
            handleClientShare(type)
    }
 }

const handleClientShare = (type: number) => {
    handleClientEvent({
        OA_WEIXIN: () => {
            uni.showModal({
                title: '温馨提示',
                content: '点击右上角“...”，分享给好友',
                showCancel: false
            })
        },
        H5: async () => {
            const options = await resolveOptions()
            copy(options.path || options.link)
            uni.showModal({
                title: '温馨提示',
                content: '已经复制到剪贴板，请转发给好友',
                showCancel: false
            })
        },
        ANDROID: async () => {
            const options = await resolveOptions()
            uni.share({
                provider: 'weixin',
                scene: 'WXSceneSession',
                type: 0,
                href: options.path,
                title: options.title,
                imageUrl: options.imageUrl,
                success: (res) => {
                    console.log('分享成功')
                },
                fail: (err) => {
                    uni.$u.toast(err.errMsg)
                }
            })
        },
        IOS: async () => {
            const options = await resolveOptions()
            uni.share({
                provider: 'weixin',
                scene: 'WXSceneSession',
                type: 0,
                href: options.path,
                title: options.title,
                imageUrl: options.imageUrl,
                success: (res) => {
                    console.log('分享成功')
                },
                fail: (err) => {
                    uni.$u.toast(err.errMsg)
                }
            })
        }
    })
}

const getDecorateData = async () => {
    // 将任务数据合并给装修数据中
    const res = await getDecorate({ id: 10 })
    const data = JSON.parse(res.data)[0]
    taskData.value.title = data?.content.title
    taskData.value.subTitle = data?.content?.subTitle
}

const getTaskData = async () => {
    const data = await getShareTaskList()
    const res: TaskRewardItem[] = JSON.parse(data.data)
    taskData.value.content = res.filter((item: TaskRewardItem) => {

        if (item.data.num >= item.data.day_num) {
            typeMap[item.type].btn_text = item.type === 1 ? '已签到' : '已分享'
        }

        typeMap[item.type].num = `${item.data?.num}/${item.data?.day_num}`
        typeMap[item.type].desc = `${typeMap[item.type].payload}`

        return item.data.is_open === 1
    })
}

const userStore = useUserStore()
const {userInfo} = storeToRefs(userStore)
const getData = async () => {
    try {
        await getTaskData()
        await getDecorateData()
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        console.error(error)
        status.value = PageStatusEnum.ERROR
    }
}

onShow(() => {
    getData()
    userStore.getUser()
})

onPullDownRefresh(async () => {
    try {
        await getData()
        await userStore.getUser()
    } catch (error) {
    }
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss">
.task-center {
    .daily-tasks {
        background: linear-gradient(
                180deg,
                var(--color-primary-light-7) 0%,
                $-color-white 100%
        ),
        #fff;
        background-size: 100% 175rpx;
        background-repeat: no-repeat;
        border-radius: 14rpx;
    }

    .tasks-title {
        padding: 0 20rpx;
        height: 88rpx;
        display: flex;
        align-items: center;
    }

    .tasks-content {
        padding-bottom: 20rpx;

        .tasks-item {
            &:not(:last-of-type) {
                border-bottom: 1px solid $u-border-color;
                margin-bottom: 10rpx;
            }
        }
    }
}
</style>
