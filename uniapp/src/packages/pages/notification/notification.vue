<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="notification flex flex-col min-h-0">
        <u-navbar
            :is-fixed="false"
            :back-text="`消息通知(${ dataLists.length })`"
        >
            <template #right>
                <text
                    class="text-xs mr-4 text-primary"
                    @click.stop="handleAllRead"
                >
                    全部已读
                </text>
            </template>
        </u-navbar>

        <view class="notification__main flex-1 h-full">
            <z-paging
                ref="paging"
                v-model="dataLists"
                @query="getList"
                :fixed="false"
                height="100%"
                empty-view-text="暂无消息通知～"
                :empty-view-img="EmptyNoticeImage"
                :empty-view-style="{ 'margin-top': '100px' }"
                :empty-view-center="false"
                :auto-clean-list-when-reload="false"
                :show-loading-more-no-more-view="false"
                :auto-scroll-to-top-when-reload="false"
                :empty-view-img-style="{ width: '360rpx', height: '360rpx' }"
            >
                <view
                    class="p-[20rpx] flex"
                    v-for="item in dataLists"
                    :key="item.id"
                    @click.stop="handleRead(item)"
                >
                    <view class="flex-none w-[74rpx] h-[74rpx] relative">
                        <!--          notice_type： 3-机器人，4-绘画，5-音乐，6-视频        -->
                        <template v-if="item.notice_type === 3">
                            <u-image
                                :width="84"
                                :height="84"
                                :src="item.avatar"
                                border-radius="50%"
                            >
                            </u-image>
                        </template>
                        <template v-else>
                            <view
                                class="flex justify-center items-center w-[84rpx] h-[84rpx] text-sm rounded-full"
                                :style="{
                                    background: worksMap[item.notice_type].bg,
                                    color: worksMap[item.notice_type].color
                                }"
                            >
                                {{ worksMap[item.notice_type].name }}
                            </view>
                        </template>
                        <u-badge
                            v-if="!item.read"
                            :is-dot="true"
                            type="error"
                            :offset="[-8, -8]"
                        />
                    </view>

                    <view class="flex-1 border-bottom pb-[20rpx] ml-[24rpx]">
                        <view class="flex justify-between items-center">
                            <view
                                v-if="item.nickname"
                                class="line-clamp-1 w-[300rpx] text-main text-base"
                            >
                                {{ item.nickname }}
                            </view>
                            <text class="text-muted text-sm">{{ item.create_time }}</text>
                        </view>

                        <view class="mt-[20rpx] text-main text-base">
                            <!--          notice_type： 3-机器人，4-绘画，5-音乐，6-视频        -->
                            <template v-if="item.notice_type === 3">
                                <text class="text-muted">在</text>
                                {{ item.robot }}
                                <text class="text-muted">反馈：</text>
                                {{ item.content }}
                            </template>
                            <template v-else>
                                <text class="text-muted">分享</text>
                                {{ item.records_name }}
                                <text class="text-muted">至广场</text>
                                <text
                                    :class="[
                                        item.verify_status === 1
                                            ? 'text-success'
                                            : 'text-error'
                                    ]"
                                >
                                    {{ item.verify_status === 1 ? '审核成功' : '审核失败' }}
                                </text>
                                <text v-if="item.verify_status == 2">，原因： {{ item.verify_result }}</text>
                                <text v-if="item.verify_status == 1 && item.balance > 0">
                                  ， 获得 {{ item.balance }} {{ appStore.getTokenUnit }}
                                </text>
                            </template>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>
    </view>
</template>
<script lang="ts" setup>
import {ref, shallowRef} from 'vue'
import {useRouter} from 'uniapp-router-next'
import {noticeLists, noticeRead, noticeAllRead} from '@/api/app'
import EmptyNoticeImage from '@/packages/static/empty/notice.png'
import {useAppStore} from '@/stores/app'

interface NoticePropsType {
    avatar: string
    content: string
    create_time: string
    id: number
    nickname: string
    read: 0 | 1
    robot: string
    robot_id: number
    balance: number // 审核成功获得的余额
    notice_type: 3 | 4 | 5 | 6 // 3-机器人，4-绘画，5-音乐，6-视频
    records_name: string // 分享的作品名称
    verify_result: string // 审核失败原因
    verify_status: 1 | 2 // 1-审核成功，2-审核失败
    model: string // 模型
}

const router = useRouter()
const appStore = useAppStore()
const paging = shallowRef()
const dataLists = ref<NoticePropsType[]>([])

const worksMap: Record<number, { name: string; bg: string; color: string, link: string }> = {
    4: { name: '绘画', bg: '#EAF3FF', color: '#4A92FF', link: '/packages/pages/draw_list/draw_list' },
    5: { name: '音乐', bg: '#FFF1E4', color: '#FF8F1F', link: '/packages/pages/music_list/music_list' },
    6: { name: '视频', bg: '#FFF0F0', color: '#FA5151', link: '/packages/pages/video_list/video_list' },
    7: { name: '智能体', bg: '#E7FFF7', color: '#00B578', link: '/pages/kb/kb' }
}

// 已读
const handleRead = async (row: NoticePropsType) => {
    if (row.notice_type === 3) {
        router.navigateTo({
            path: '/packages/pages/robot_info/robot_info',
            query: {
                id: row.robot_id,
                type: 'dialogue',
                dialogue: 'record'
            }
        })
    } else {
        if (row.notice_type === 4) {
            router.navigateTo({
                path: worksMap[row.notice_type].link,
                query: {
                    model: row.model
                }
            })
        } else {
            router.navigateTo(worksMap[row.notice_type].link)
        }
    }
    if (row.read) return
    await noticeRead({
        id: row.id
    })
    paging.value?.reload()
}

// 全部已读
const handleAllRead = async () => {
    await noticeAllRead()
    paging.value?.reload()
}

// 获取列表
const getList = async (pageNo: number, pageSize: number) => {
    try {
        const {lists} = await noticeLists({
            page_no: pageNo,
            page_size: pageSize
        })
        paging.value.complete(lists)
    } catch (error) {
        paging.value.complete(false)
        console.log('请求消息通知列表失败', error)
    }
}
</script>

<style lang="scss">
page {
    height: 100%;
}
.notification {
    height: 100%;
    background: #ffffff;

    &__main {
        .border-bottom {
            border-bottom: 1px solid #E0E3EA;
        }
    }
}
</style>
