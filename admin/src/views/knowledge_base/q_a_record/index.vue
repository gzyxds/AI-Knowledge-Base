<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.user"
                        placeholder="请输入用户ID/用户昵称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.keyword"
                        placeholder="请输入关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="审核状态">
                    <el-select
                        class="w-[280px]"
                        v-model="queryParams.censor_status"
                        placeholder="请选择审核状态"
                        clearable
                    >
                        <el-option label="全部" value="" />
                        <el-option label="未审核" :value="0" />
                        <el-option label="合规" :value="1" />
                        <el-option label="不合规" :value="2" />
                        <el-option label="疑似" :value="3" />
                        <el-option label="审核失败" :value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item label="智能体名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.robot"
                        placeholder="请输入智能体名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="提问时间">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <ElButton
                    :disabled="selectData.length <= 0"
                    v-perms="['kb.robot/chatClean']"
                    @click="handleDelete(selectData)"
                    class="!mb-4"
                >
                    批量删除
                </ElButton>
            </div>

            <el-table
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="用户信息" min-width="180">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="mr-4 flex-none"
                                radius="50%"
                                v-if="row.user?.avatar"
                                :src="row.user?.avatar"
                                :width="48"
                                :height="48"
                                :preview-src-list="[row.user?.avatar]"
                                preview-teleported
                                fit="contain"
                            />
                            <span>{{ row.user?.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="提问时间"
                    prop="create_time"
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="用户提问" prop="ask" min-width="250">
                    <template #default="{ row }">
                        <OverflowTooltip :content="row.ask" :line="3" :teleported="true" />
                    </template>
                </el-table-column>
                <el-table-column label="所属智能体" prop="robot_name" min-width="180" />

                <el-table-column label="访问渠道" prop="channel" min-width="100" />
                <el-table-column label="对话模型" prop="model" min-width="140" />
                <el-table-column label="消耗电力值" prop="tokens" min-width="100" />
                <el-table-column label="审核状态" min-width="180">
                    <template #default="{ row }">
                        <div>
                            <el-tag v-if="row.censor_status == 1" type="success">
                                {{ row.censor_status_desc }}
                            </el-tag>
                            <template v-else-if="row.censor_status >= 2">
                                <el-tag class="cursor-pointer" type="danger">
                                    {{ row.censor_status_desc }}
                                </el-tag>
                                <el-button
                                    type="danger"
                                    :link="true"
                                    @click="handleShowAuditPopup(row)"
                                >
                                    查看原因
                                </el-button>
                            </template>
                            <el-tag v-else-if="row.censor_status == 0" type="warning">{{
                                row.censor_status_desc
                            }}</el-tag>
                            <el-tag v-else type="danger">
                                {{ row.censor_status_desc }}
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleShowReplyPopup(row.reply)">
                            查看回复
                        </el-button>
                        <el-button
                            v-perms="['kb.robot/chatClean']"
                            type="danger"
                            link
                            @click="handleDelete([row.id])"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <AuditPopup ref="auditRef" />
        <ReplyPopup ref="popRef" />
    </div>
</template>
<script lang="ts" setup name="QARecord">
import { getKnowRecordsLists, knowRecordsDelete } from '@/api/knowledge_base/q_a_record'

import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import AuditPopup from './components/audit-popup.vue'
import ReplyPopup from './components/reply-popup.vue'

const queryParams = reactive({
    user: '', //用户信息
    keyword: '', //关键词
    start_time: '',
    end_time: '',
    robot: '',
    censor_status: '' // [0=未审核, 1=合规, 2=不合规, 3=疑似, 4=审核失败]
})

//弹框ref
const popRef = shallowRef()
// 审核弹窗
const auditRef = shallowRef()

const handleShowReplyPopup = (reply: any) => {
    popRef.value.open(reply)
}

const handleShowAuditPopup = (record: any) => {
    auditRef.value.open(record)
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getKnowRecordsLists,
    params: queryParams
})
const selectData = ref<number[]>([])
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map((item) => item.id)
}
const handleDelete = async (ids: number[]) => {
    await feedback.confirm('确定要删除？')
    await knowRecordsDelete({ ids })
    getLists()
}

getLists()
</script>
