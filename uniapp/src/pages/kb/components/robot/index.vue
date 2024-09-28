<template>
    <view class="content">
        <!--  :enable-back-to-top="currentIndex===tabIndex" 在微信小程序上可以多加这一句，因为默认是允许点击返回顶部的，但是这个页面有多个scroll-view，会全部返回顶部，所以需要控制是当前index才允许点击返回顶部 -->
        <!-- 如果当前页已经加载过数据或者当前切换到的tab是当前页，才展示当前页数据（懒加载） -->
        <z-paging
            v-if="firstLoaded || isCurrentPage"
            ref="pagingRef"
            v-model="dataList"
            :auto-clean-list-when-reload="false"
            @query="queryList"
            :fixed="false"
        >
            <!-- 如果希望其他view跟着页面滚动，可以放在z-paging标签内 -->
            <template #top>
                <view class="py-[14rpx] px-[30rpx] bg-white">
                    <u-search
                        v-model="keyword"
                        placeholder="请输入关键词搜索"
                        height="72"
                        bg-color="#F7F8F9"
                        @search="pagingRef.reload()"
                        @custom="pagingRef.reload()"
                        @clear="pagingRef.reload()"
                    />
                </view>
            </template>
            <view class="px-[20rpx] pt-[30rpx]">
                <view class="flex flex-wrap mx-[-15rpx]">
                    <view
                        class="item w-[50%] px-[15rpx] mb-[30rpx]"
                        v-for="item in dataList"
                        :key="item.id"
                    >
                        <view
                            class="h-full robot-item"
                            @click="toInfo(item.id)"
                        >
                            <view class="flex justify-end" @click.stop>
                                <view class="flex-1">
                                    <u-tag
                                        type="warning"
                                        v-if="item.is_public"
                                        text="公开"
                                        size="mini"
                                    ></u-tag>
                                    <u-tag
                                        type="primary"
                                        v-else
                                        text="私有"
                                        size="mini"
                                    ></u-tag>
                                </view>
                                <view @click="showAction(item)">
                                    <u-icon name="more-dot-fill" />
                                </view>
                            </view>
                            <view class="flex flex-col items-center">
                                <u-image
                                    :src="item.image"
                                    width="100"
                                    height="100"
                                    shape="circle"
                                ></u-image>
                                <view
                                    class="text-2xl mt-[22rpx] mb-[16px] line-clamp-3"
                                    >{{ item.name }}</view
                                >
                                <view
                                    class="text-muted leading-[40rpx] h-[120rpx] line-clamp-3"
                                >
                                    {{
                                        item.intro || '这个智能体还没介绍呢'
                                    }}</view
                                >
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </z-paging>
        <AddBtn @click="handelShowAdd" />
        <AddPopup v-model="showAdd" @update="pagingRef.reload()" />
        <u-action-sheet
            :list="menuOptions"
            v-model="actionState.show"
            @click="menuAction"
        ></u-action-sheet>
        <share-popup
            v-if="showShare"
            ref="shareRef"
            @close="showShare = false"
            @success="shareSuccess"
        ></share-popup>
    </view>
</template>

<script setup lang="ts">
import { watch, shallowRef, ref, nextTick, reactive } from 'vue'
import { getRobotLists, delRobot, cancelShare } from '@/api/robot'
import AddBtn from '../add-btn.vue'
import AddPopup from './add-popup.vue'
import SharePopup from './share-popup.vue'
import { useRouter } from 'uniapp-router-next'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '@/stores/app'

const props = defineProps<{
    tabIndex: number
    currentIndex: number
}>()

const userStore = useUserStore()
const appStore = useAppStore()
const dataList = ref<any[]>([])
const pagingRef = shallowRef()
const firstLoaded = ref(false)
const isCurrentPage = ref(false)
const showAdd = ref(false)
const keyword = ref('')
const actionState = reactive({
    show: false,
    currentId: -1,
    isShare: -1 as number | undefined,
    isPublic:  -1 as number | undefined
})
const menuOptions = shallowRef([
    {
        value: 'begin',
        text: '开始对话'
    },
    {
        value: 'release',
        text: '发布智能体'
    },
    {
        value: 'dialogue',
        text: '对话数据'
    },
    {
        value: 'share',
        text: '分享至广场',
        show: !appStore.getSquareConfig.robot_award?.is_open
    },
    {
        value: 'delete',
        text: '删除'
    }
])

const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])

const showAction = (item: any) => {
    actionState.show = true
    actionState.currentId = item.id
    actionState.isPublic = item.is_public
    actionState.isShare = item.is_share
    // 如果已经发布了的，则显示的菜单是取消发布
    if (item.is_public) {
        menuOptions.value[3].text = '取消发布广场'
    } else {
        menuOptions.value[3].text = '分享至广场'
    }
}
const removeRobot = async (id: number) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await delRobot({ id })
    const index = dataList.value.findIndex((item) => item.id == id)
    dataList.value.splice(index, 1)
}

const shareAgent = async (id: number, is_share?: number) => {
    if (sharedIds.value.includes(id) || is_share) {
        const res = await uni.showModal({
            title: '温馨提示',
            content: '该智能体已分享过，是否确认重复分享？'
        })
        if (res.cancel) {
            return
        }
    }
    showShare.value = true
    await nextTick()
    setTimeout(() => shareRef.value.open(id), 50)
}

const shareSuccess = (value: number) => {
    sharedIds.value.push(value)
    reload()
}

const menuAction = async (index: number) => {
    const action = menuOptions.value[index].value
    const id = actionState.currentId
    switch (action) {
        case 'delete': {
            removeRobot(id)
            // reload()
            break
        }
        case 'dialogue':
        case 'release': {
            toInfo(id, action)
            break
        }
        case 'share': {
            if (actionState.isPublic) {
                await cancelShare({ id })
            } else {
                shareAgent(id, actionState.isShare)
            }
            reload()
            break
        }
        case 'begin': {
            router.navigateTo({
                path: '/packages/pages/robot_chat/robot_chat',
                query: {
                    id
                }
            })
        }
    }
}
const router = useRouter()
const toInfo = (id: number, type?: string) => {
    router.navigateTo({
        path: '/packages/pages/robot_info/robot_info',
        query: {
            id,
            type: type || null
        }
    })
}
const handelShowAdd = async () => {
    if (!userStore.isLogin) return router.navigateTo('/pages/login/login')
    if (userStore.userInfo.robot_num <= 0) {
        if (!appStore.getIsShowRecharge) {
            uni.$u.toast('智能体数量已用完。请联系客服增加')
        } else {
            const { cancel } = await uni.showModal({
                title: '温馨提示',
                content: '智能体数量已用完，请前往充值'
            })
            if (cancel) return
            router.navigateTo({
                path: '/packages/pages/recharge/recharge'
            })
        }
        return Promise.reject()
    }
    showAdd.value = true
}
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [] } = await getRobotLists({
            page_size: pageSize,
            page_no: pageNo,
            keyword: keyword.value
        })

        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const reload = () => {
    nextTick(() => {
        // 刷新列表数据(如果不希望列表pageNo被重置可以用refresh代替reload方法)
        pagingRef.value && pagingRef.value.refresh()
    })
}

watch(
    () => props.currentIndex,
    (newVal) => {
        if (newVal === props.tabIndex) {
            // 懒加载，当滑动到当前的item时，才去加载
            if (!firstLoaded.value) {
                // 这里需要延迟渲染z-paging的原因是为了避免在一些平台上立即渲染可能引发的底层报错问题
                nextTick(() => {
                    setTimeout(() => {
                        isCurrentPage.value = true
                    }, 100)
                })
            }
        }
    },
    {
        immediate: true
    }
)

defineExpose({
    reload
})
</script>

<style>
/* 注意:父节点需要固定高度，z-paging的height:100%才会生效 */
.content {
    height: 100%;
}

.robot-item {
    border-radius: 16rpx;
    background: #fff;
    box-shadow: 0 0 16rpx #3c5efd0f;
    padding: 20rpx 20rpx 30rpx;
}

.add-float {
}
</style>
