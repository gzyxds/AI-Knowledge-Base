<template>
    <div class="notification cursor-pointer ml-[0px] mr-[30px]">
        <el-popover
            placement="bottom"
            :width="380"
            trigger="hover"
            :show-arrow="false"
            transition="custom-popover"
            :teleported="false"
        >
            <template #default>
                <!--        标题头部        -->
                <div class="flex justify-between border-bottom pb-[20px] mb-[10px]">
                    <div class="text-xl text-tx-primary font-medium">
                        <div>消息通知</div>
                    </div>
                    <ElButton
                        type="primary"
                        :link="true"
                        :disabled="!data.all_unread"
                        @click="handleAllRead"
                    >
                        全部已读
                    </ElButton>
                </div>

                <!--        通知卡片        -->
                <template v-if="data?.lists?.length">
                    <NotificationCard
                        v-for="item in data.lists"
                        :key="item.id"
                        :data="item"
                        class="px-[8px]"
                        @read="refreshNotice"
                    />
                </template>

                <!--        空状态时        -->
                <template v-else>
                    <el-empty
                        :image="EmptyNotice"
                        :image-size="150"
                        description="暂无消息通知"
                    />
                </template>

                <!--        底部        -->
                <NuxtLink
                    class="flex justify-center items-center border-top pt-[20px] mt-[10px]"
                    to="/user/notification"
                >
                    <el-button link>
                        <span class="text-base mr-1">查看所有消息</span>
                        <Icon name="el-icon-ArrowRightBold" size="14"></Icon>
                    </el-button>
                </NuxtLink>
            </template>
            <template #reference>
                <el-badge
                    :value="data.all_unread"
                    :show-zero="!!data.all_unread"
                    :offset="[0, 2]"
                >
                    <img
                        src="@/assets/image/icon_notification.png"
                        class="w-[22px] h-[22px]"
                        alt=""
                    />
                </el-badge>
            </template>
        </el-popover>

    </div>
</template>
<script lang="ts" setup>
import {noticeLists, noticeAllRead} from '~/api/app'
import usePolling from '~/composables/usePolling'
import EmptyNotice from '~/assets/image/empty_notice.png'

const {data, refresh: refreshNotice} = useAsyncData(() => noticeLists({
    page_no: 1,
    page_size: 5
}), {
    default: () => {
        return []
    },
    lazy: true
})

// 轮询获取消息通知
const {start, end, result} = usePolling(refreshNotice, {
    key: 'notice',
    time: 20000,
    totalTime: 1200 * 10000,
    callback: () => {}
})

const handleAllRead = async () => {
    await noticeAllRead()
    await refreshNotice()
}

onMounted(() => {
    start()
})
</script>

<style lang="scss" scoped>
.notification {
    :deep() {
        .el-badge__content {
            border: none !important;
            background-color: #FA5151 !important;
        }

        .el-popper {
            padding: 20px !important;
        }
    }

    .border-bottom {
        border-bottom: 1px solid #E0E3EA;
    }

    .border-top {
        border-top: 1px solid #E0E3EA;
    }
}
</style>
