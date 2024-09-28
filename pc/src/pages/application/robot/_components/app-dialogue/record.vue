<template>
    <div class="p-main h-full flex flex-col">
        <div class="flex justify-between">
            <ElButton
                :disabled="selectData.length <= 0"
                class="!mb-4"
                @click="handleDelete(selectData)"
            >
                批量删除
            </ElButton>
            <div class="flex">
                <div class="pt-[6px] px-[10px]">问题反馈</div>
                <el-select
                    v-model="queryParams.is_feedback"
                    style="width: 240px"
                    @change="getLists"
                >
                    <el-option
                        label="全部"
                        :value="-1"
                    />
                    <el-option
                        label="未反馈"
                        :value="0"
                    />
                    <el-option
                        label="已反馈"
                        :value="1"
                    />
                </el-select>
            </div>
        </div>
        <div class="flex-1 min-h-0">
            <el-table
                v-loading="pager.loading"
                height="100%"
                size="large"
                :data="pager.lists"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55"/>
                <el-table-column label="ID" prop="id" min-width="80"/>
                <el-table-column label="用户信息" min-width="180">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.user?.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>

                <el-table-column label="提问" prop="ask" min-width="300">
                    <template #default="{ row }">
                        <OverflowTooltip
                            :content="row.ask"
                            :line="2"
                            :teleported="true"
                            effect="light"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="回答" prop="answer" min-width="300">
                    <template #default="{ row }">
                        <span
                            class="line-clamp-2 cursor-pointer"
                            @click="handleShowReplyPopup(row.reply)"
                        >{{ row.reply }}</span
                        >
                    </template>
                </el-table-column>
                <el-table-column label="用户内容" prop="feedback" min-width="100">
                    <template #default="{ row }">
                        <span
                            v-if="row.feedback"
                            class="line-clamp-2 cursor-pointer text-primary"
                            @click="handleShowFeedBackPopup(row.feedback)"
                        >反馈内容</span>
                        <span v-else>-</span>
                    </template>
                </el-table-column>
                <el-table-column
                    label="提问时间"
                    prop="create_time"
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="correctRef?.open(row)">
                            修正
                        </el-button>
                        <el-button
                            type="primary"
                            link
                            @click="handleShowReplyPopup(row.reply)"
                        >
                            查看回复
                        </el-button>
                        <el-button type="danger" link @click="handleDelete([row.id])">
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists"/>
        </div>
        <ReplyPopup ref="popRef"/>
        <CorrectPopup ref="correctRef" @confirm="handleAdjust"/>
    </div>
</template>
<script lang="ts" setup>
import ReplyPopup from './reply-popup.vue'
import CorrectPopup from './correct-popup.vue'

import feedback from '@/utils/feedback'
import {
    delRobotChatRecord,
    getRobotDataRecord,
    robotRecordsCorrect
} from '@/api/robot'

const props = defineProps<{
    appId: string | number
}>()
const {appId} = toRefs(props)
const queryParams = reactive({
    robot_id: appId,
    is_feedback: -1
})

const popRef = shallowRef()
const correctRef = shallowRef()

const handleShowReplyPopup = (reply: any) => {
    popRef.value.open(reply, '查看回复')
}

const handleShowFeedBackPopup = (msg: any) => {
    popRef.value.open(msg, '反馈内容')
}

const {pager, getLists, resetPage} = usePaging({
    fetchFun: getRobotDataRecord,
    params: queryParams
})
const selectData = ref<number[]>([])
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map((item) => item.id)
}
const handleDelete = async (ids: number[]) => {
    await feedback.confirm('确定要删除？')
    await delRobotChatRecord({ids, robot_id: appId.value})
    getLists()
}
const handleAdjust = async (data: any) => {
    await robotRecordsCorrect(data)
    correctRef.value?.close()
}

getLists()

watch(
    () => props.appId,
    () => {
        resetPage()
    }
)
</script>
