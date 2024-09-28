<template>
    <div class="h-full flex flex-col">
        <div class="flex px-[20px] py-[16px]">
            <div class="font-medium text-xl">我的智能体</div>
            <div class="flex items-center flex-none ml-auto">
                <div class="flex-none mr-[10px]">筛选</div>
                <el-select
                    class="!w-[100px] flex-none"
                    v-model="queryParams.type"
                >
                    <el-option label="全部" :value="' '" />
                    <el-option label="公开" :value="1" />
                    <el-option label="私有" :value="0" />
                </el-select>
                <el-input
                    class="mx-[10px]"
                    v-model="queryParams.keyword"
                    placeholder="请输入"
                />
                <div>
                    <ElButton @click.stop="resetPage" type="primary">搜索</ElButton>
                </div>
            </div>
        </div>
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div
                    class="px-[20px]"
                    v-infinite-scroll="load"
                    infinite-scroll-distance="50"
                >
                    <div v-if="userStore.isLogin">
                        <div class="flex flex-wrap items-stretch mx-[-10px]">
                            <div
                                class="w-1/4 2xl:w-1/5 sm:mb-[20px] mb-[10px] app-item"
                            >
                                <div
                                    class="mx-[10px] bg-body h-full rounded-[12px] p-[20px] overflow-hidden flex flex-col justify-center items-center cursor-pointer min-h-[200px]"
                                    @click="handleAppAdd"
                                    v-loading="isLock"
                                >
                                    <Icon name="el-icon-Plus" :size="24" />
                                    <div class="mt-[10px]">新增智能体</div>
                                </div>
                            </div>
                            <div
                                v-for="(item, index) in pageInfo.lists"
                                :key="index"
                                class="w-1/4 2xl:w-1/5 sm:mb-[20px] mb-[10px] app-item"
                            >
                                <NuxtLink
                                    :to="{
                                        path: '/application/robot/setting',
                                        query: {
                                            id: item.id
                                        }
                                    }"
                                    class="mx-[10px] bg-body h-full rounded-[12px] overflow-hidden relative flex flex-col"
                                >
                                    <div class="flex px-[15px] py-[12px]">
                                        <div class="flex-1 text-tx-secondary">
                                            <el-tag
                                                type="warning"
                                                v-if="item.is_public"
                                                >公开</el-tag
                                            >
                                            <el-tag type="primary" v-else
                                                >私有</el-tag
                                            >
                                        </div>
                                        <el-popover
                                            placement="bottom"
                                            trigger="hover"
                                            offset="12"
                                            :teleported="true"
                                            :show-arrow="false"
                                            transition="custom-popover"
                                            popper-class="cursor-pointer"
                                            :width="190"
                                        >
                                            <template #reference>
                                                <ElButton
                                                  link
                                                  class="el-dropdown-link"
                                                >
                                                    <Icon
                                                      name="el-icon-MoreFilled"
                                                    />
                                                </ElButton>
                                            </template>
                                            <template #default>
                                                <div
                                                    class="flex items-center dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-[12px] px-[12px] py-[10px]"
                                                    @click.stop="handleCommand('release', item)"
                                                >
                                                    <Icon name="el-icon-Position" />
                                                    <span class="ml-2">发布智能体</span>
                                                </div>
                                                <div
                                                    class="flex items-center dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-[12px] px-[12px] py-[10px]"
                                                    @click.stop="handleCommand('dialogue', item)"
                                                >
                                                    <Icon name="el-icon-ChatDotRound" />
                                                    <span class="ml-2">对话数据</span>
                                                </div>
                                                <div
                                                    v-if="item.is_public && appStore.getSquareConfig.robot_award?.is_open"
                                                    class="flex items-center dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-[12px] px-[12px] py-[10px]"
                                                    @click.stop="handleCommand('cancelPublic', item)"
                                                >
                                                    <Icon name="el-icon-Share" />
                                                    <span class="ml-2">取消发布至广场</span>
                                                </div>
                                                <div
                                                    v-else-if="appStore.getSquareConfig.draw_award?.is_open"
                                                    class="flex items-center dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-[12px] px-[12px] py-[10px]"
                                                    @click.stop="handleCommand('share', item)"
                                                >
                                                    <Icon name="el-icon-Share" />
                                                    <span class="ml-2">发布至广场</span>
                                                </div>
                                                <div
                                                    class="flex items-center dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-[12px] px-[12px] py-[10px]"
                                                    @click.stop="handleCommand('delete', item)"
                                                >
                                                    <Icon name="el-icon-Delete" />
                                                    <span class="ml-2">删除</span>
                                                </div>
                                            </template>
                                        </el-popover>
                                    </div>
                                    <div
                                        class="px-[15px] flex flex-col items-center text-center flex-1"
                                    >
                                        <img
                                            class="flex-none w-[74px] h-[74px] rounded-full"
                                            :src="item.image"
                                            alt=""
                                        />
                                        <div
                                            class="text-2xl mt-[6px] mb-[12px]"
                                        >
                                            <OverflowTooltip
                                                :content="item.name"
                                            />
                                        </div>
                                        <div
                                            class="text-tx-secondary leading-5 h-[60px] line-clamp-3"
                                        >
                                            {{
                                                item.intro ||
                                                '这个智能体还没介绍呢～'
                                            }}
                                        </div>
                                    </div>
                                    <div
                                        class="flex mt-4 items-center border-t border-solid border-br-light"
                                    >
                                        <div
                                            class="flex-1 text-tx-regular border-r border-solid border-br-light flex items-center justify-center h-[50px] cursor-pointer hover:text-primary"
                                        >
                                            <Icon
                                                name="el-icon-Position"
                                                :size="18"
                                            />
                                            <div class="ml-[8px]">
                                                设置智能体
                                            </div>
                                        </div>
                                        <NuxtLink
                                            :to="{
                                                path: '/application/chat',
                                                query: {
                                                    id: item.id
                                                }
                                            }"
                                            class="flex-1 text-tx-regular flex items-center justify-center h-[50px] cursor-pointer hover:text-primary"
                                        >
                                            <Icon
                                                name="el-icon-ChatLineSquare"
                                                :size="18"
                                            />
                                            <div class="ml-[8px]">开始对话</div>
                                        </NuxtLink>
                                    </div>
                                </NuxtLink>
                            </div>
                        </div>
                    </div>

                    <div v-if="!userStore.isLogin">
                        <tologin />
                    </div>
                </div>
            </ElScrollbar>
        </div>

        <robot-share
            v-if="showShare"
            ref="shareRef"
            @close="showShare = false"
            @success="shareSuccess"
        ></robot-share>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user'
import { useAppStore } from '~/stores/app'
import tologin from '@/layouts/components/account/tologin.vue'
import { useRobotStore } from '@/stores/robot'

import RobotShare from './_components/robot-share.vue'
import feedback from "~/utils/feedback";

const userStore = useUserStore()
const router = useRouter()
const appStore = useAppStore()
const robotStore = useRobotStore()

const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])

const shareRobot = async (records_id: number, is_share?: number) => {
    if (sharedIds.value.includes(records_id) || is_share) {
        await feedback.confirm('该视频已分享过，是否确认重复分享？')
    }
    showShare.value = true
    await nextTick()
    shareRef.value.open(records_id)
}

const shareSuccess = (value: number) => {
    sharedIds.value.push(value)
    robotStore.getRobot()
    resetPage()
}

const { isLock, lockFn: handleAppAdd } = useLockFn(async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (userStore.userInfo.robot_num <= 0) {
        if (!appStore.getIsShowRecharge) {
            feedback.msgError('智能体数量已用完。请联系客服增加')
        } else {
            await feedback.confirm('智能体数量已用完，请前往充值')
            router.push({
                path: '/user/recharge'
            })
        }
        return Promise.reject()
    }
    const id = await robotStore.addRobot()
    userStore.getUser()
    router.push({
        path: '/application/robot/setting',
        query: {
            id
        }
    })
})

const queryParams = reactive({
    type: ' ',
    keyword: ''
})
const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 15,
    lists: [] as any[]
})

const getLists = async () => {
    const data = await robotStore.getRobot({
        ...queryParams,
        page_no: pageInfo.pageNo,
        page_size: pageInfo.pageSize
    })
    pageInfo.count = data.count
    if (pageInfo.pageNo === 1) {
        pageInfo.lists = []
    }
    pageInfo.lists.push(...data.lists)
}

const load = () => {
    if (!userStore.isLogin) return
    if (pageInfo.count >= pageInfo.pageNo * pageInfo.pageSize) {
        pageInfo.pageNo++
        getLists()
    }
}

const resetPage = () => {
    pageInfo.pageNo = 1
    getLists()
}
await useAsyncData(() => getLists(), {
    lazy: true
})

const handleCommand = async (command: string | number | object, row: any) => {
    switch (command) {
        case 'delete':
            await robotStore.delRobot(row.id)
            userStore.getUser()
            resetPage()
            break
        case 'release':
        case 'dialogue':
            router.push({
                path: '/application/robot/setting',
                query: {
                    id: row.id,
                    currentTab: command
                }
            })
        case 'share':
            shareRobot(row.id, row.is_share)
            break;
        case 'cancelPublic':
            await robotStore.cancelShareRobot(row.id);
            resetPage();
    }
}

definePageMeta({
    title: '智能体应用'
})
</script>

<style lang="scss" scoped>
.app-category {
    :deep(.el-tabs) {
        .el-tabs__header {
            margin-bottom: 0;
        }
        .el-tabs__item {
            font-size: 16px;
        }
        .el-tabs__nav-wrap::after {
            height: 0;
        }
    }
}
.app-item {
    .action-btn {
        // display: none;
        transition: all 0.2s linear;
        @apply sm:opacity-0;
    }
    &--my:hover {
        .action-btn {
            //   display: block;
            opacity: 1;
            transition: all 0.2s linear;
        }
    }
}
:deep(.el-button + .el-button) {
    margin-left: 0px !important;
    // margin-right: 10px !important;
}
</style>
