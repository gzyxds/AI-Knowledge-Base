<template>
    <div class="p-[20px] flex bg-body rounded-[12px] flex-col h-full">
        <!--        标题头部        -->
        <div class="flex justify-between relative">
            <!--            <div class="text-2xl text-tx-primary font-medium">-->
            <!--                <div>系统通知（{{ pager?.count || 0 }}）</div>-->
            <el-tabs
                class="flex-1"
                v-model="queryParams.type"
                @tab-change="getLists"
            >
                <el-tab-pane label="系统通知" :name="1">
                    <template #label>
                        <el-badge
                            class="flex-none text-xl font-medium"
                            :value="pager.system_unread"
                            :show-zero="!!pager.system_unread"
                            :offset="[10, 0]"
                        >
                            系统通知
                        </el-badge>
                    </template>
                </el-tab-pane>
                <el-tab-pane label="审核通知" :name="2">
                    <template #label>
                        <el-badge
                            class="flex-none text-xl font-medium"
                            :value="pager.audit_unread"
                            :show-zero="!!pager.audit_unread"
                            :offset="[10, 0]"
                        >
                            审核通知
                        </el-badge>
                    </template>
                </el-tab-pane>
            </el-tabs>
            <ElButton
                class="absolute right-0 top-[10px]"
                type="primary"
                :link="true"
                :disabled="!pager.unread"
                @click="handleAllRead"
            >
                全部已读
            </ElButton>
        </div>

        <div class="flex-1 min-h-0 flex flex-col">
            <div class="flex-1 min-h-0">
                <!--        通知卡片        -->
                <ElScrollbar height="100%" v-if="pager?.lists?.length">
                    <NotificationCard
                        v-for="item in pager.lists"
                        :key="item.id"
                        :data="item"
                        size="large"
                        @read="getLists"
                    />
                </ElScrollbar>

                <!--        空状态时        -->
                <div v-else class="flex items-center justify-center h-full">
                    <el-empty
                        :image="EmptyNotice"
                        :image-size="250"
                        description="暂无消息通知"
                    />
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { noticeLists, noticeAllRead } from '~/api/app'
import EmptyNotice from '~/assets/image/empty_notice.png'

const queryParams = reactive({
    type: 1
})

const { pager, getLists, resetPage } = usePaging({
    fetchFun: noticeLists,
    params: queryParams
})

const handleAllRead = async () => {
    await noticeAllRead()
    await getLists()
}

onMounted(() => {
    getLists()
})
</script>

<style scoped lang="scss">
.border-bottom {
    border-bottom: 1px solid #e0e3ea;
}
</style>