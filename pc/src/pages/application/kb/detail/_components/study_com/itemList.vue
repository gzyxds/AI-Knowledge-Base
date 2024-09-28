<template>
    <div>
        <div class="pb-[20px] flex items-center font-bold cursor-pointer">
            <div @click="$emit('back')" class="flex items-center">
                <Icon name="el-icon-Back" size="16"></Icon>
                <span class="ml-2">{{ itemName }}</span>
            </div>
        </div>
        <div class="flex">
            <div>
                <el-button
                    type="primary"
                    :disabled="knowDetail.power === 3"
                    @click="toImportData"
                >
                    录入数据
                </el-button>
                <export-data
                    class="mx-2.5 inline-block"
                    :fetch-fun="itemFileDataList"
                    :params="queryParams"
                    :page-size="pager.size"
                >
                    <template #trigger>
                        <el-button :disabled="knowDetail.power === 3"> 一键导出 </el-button>
                    </template>
                </export-data>
                <el-button
                    :disabled="!tableRef?.getSelectionRows().length"
                    @click="batchRetry"
                >
                    批量重试
                </el-button>
                <el-button
                    :disabled="!tableRef?.getSelectionRows().length"
                    @click="batchDelete"
                >
                    批量删除
                </el-button>
            </div>
            <div class="ml-auto flex">
                <div class="min-w-[240px]">
                    <el-input
                        v-model="queryParams.keyword"
                        placeholder="请输入问题/回答内容关键词进行搜索"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </div>
                <div class="min-w-[180px] ml-2">
                    <el-select
                        v-model="queryParams.status"
                        @change="selectChange"
                    >
                        <el-option label="全部" value />
                        <el-option label="等待学习" :value="0" />
                        <el-option label="学习中" :value="1" />
                        <el-option label="学习失败" :value="3" />
                        <el-option label="学习成功" :value="2" />
                    </el-select>
                </div>
            </div>
        </div>
        <el-table
            ref="tableRef"
            v-loading="pager.loading"
            class="mt-4"
            :data="pager.lists"
            size="large"
            row-class-name="h-[70px]"
        >
            <el-table-column type="selection" width="55" />
            <!-- <el-table-column label="ID" prop="uuid" min-width="150" /> -->
            <el-table-column label="文档内容" prop="question" min-width="200">
                <template #default="{ row }">
                    <OverflowTooltip
                        :content="row.question"
                        :line="2"
                        :teleported="true"
                        effect="light"
                        placement="right"
                    />
                </template>
            </el-table-column>
            <el-table-column label="补充内容" prop="answer" min-width="200">
                <template #default="{ row }">
                    <OverflowTooltip
                        :content="row.answer || '-'"
                        :line="2"
                        :teleported="true"
                        effect="light"
                        placement="right"
                    />
                </template>
            </el-table-column>
            <el-table-column label="学习状态" prop="source" min-width="150">
                <template #default="{ row }">
                    <div v-if="row.status == 0">等待学习</div>
                    <div v-if="row.status == 1" class="text-warning">
                        学习中
                    </div>
                    <div v-if="row.status == 2" class="text-success">
                        学习成功
                    </div>
                    <div v-if="row.status == 3" class="text-danger">
                        学习失败
                    </div>
                    <div v-if="row.status == 3" class="text-danger">
                        原因：{{ row.error }}
                    </div>
                </template>
            </el-table-column>
            <el-table-column
                :label="`消耗${appStore.getTokenUnit}`"
                prop="tokens"
                min-width="150"
            />
            <el-table-column
                label="最后更新时间"
                prop="update_time"
                min-width="150"
            />
            <el-table-column label="操作" prop="source" min-width="180">
                <template #default="{ row }">
                    <el-button
                        type="primary"
                        @click="toEdit(row.uuid)"
                        v-if="row.status != 1"
                        link
                        >修正</el-button
                    >
                    <el-button
                        type="primary"
                        @click="toRetry([row.uuid])"
                        v-if="row.status == 3"
                        link
                        >重试</el-button
                    >
                    <el-button type="danger" @click="toDel([row.uuid])" link
                        >删除</el-button
                    >
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination
                v-model="pager"
                @change="
                    () => {
                        start()
                        getLists()
                    }
                "
            />
        </div>
        <edit-pop
            ref="popEntryRef"
            v-if="popEntryShow"
            @success="
                () => {
                    popEntryShow = false
                    getLists()
                    start()
                }
            "
            @close="popEntryShow = false"
        ></edit-pop>
    </div>
</template>

<script setup lang="ts">
import { ElTable } from 'element-plus'
import editPop from './editPop.vue'
import {
    itemFileDataList,
    itemDataDel,
    itemDataRetry,
    checkData
} from '@/api/my_database'
import usePolling from '@/composables/usePolling'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '~/stores/user'

const route = useRoute()
const appStore = useAppStore()
const userStore = useUserStore()
const kb_id = route.query.id

const tableRef = ref<InstanceType<typeof ElTable>>()

const props = defineProps({
    itemId: {
        type: Number,
        default: 0
    },
    itemName: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['back'])

const knowDetail = inject('knowDetail')
const popEntryShow = ref(false)
const popEntryRef = shallowRef()

const queryParams = ref({
    keyword: '',
    status: '',
    fd_id: props.itemId,
    kb_id: route.query.id
})

//分页组件
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: itemFileDataList,
    params: queryParams.value
})

const waitinglen = ref(0)
//检测等待数据
const checkWaitingData = async () => {
    const uuids = pager.lists.map((item) => item.uuid)
    const { tasks, lists } = await checkData({ ...queryParams.value, uuids })
    pager.lists.map((item) => {
        const index = lists.findIndex((item1: any) => item.uuid == item1.uuid)
        item.status = lists[index].status
        item.tokens = lists[index].tokens
    })
    waitinglen.value = tasks.length
    if (tasks.length == 0) {
        end()
        userStore.getUser()
    }
}

//开始轮训
const startPolling = async () => {
    checkWaitingData()
}

const { start, end } = usePolling(startPolling, { time: 3000, key: 'kb_id' })

//导入数据
const toImportData = async () => {
    popEntryShow.value = true
    await nextTick()
    popEntryRef.value.open({ kb_id: route.query.id, fd_id: props.itemId })
}

//修正
const toEdit = async (uuid: string) => {
    popEntryShow.value = true
    await nextTick()
    popEntryRef.value.open({ kb_id: route.query.id, fd_id: props.itemId, uuid })
}

//重试
const toRetry = async (uuids: string[]) => {
    await itemDataRetry({ kb_id, uuids })
    getLists()
    start()
}

//删除
const toDel = async (uuids: string[]) => {
    await feedback.confirm('请确认是否删除！')
    await itemDataDel({ kb_id, uuids })
    getLists()
    start()
}

//批量删除
const batchDelete = async () => {
    const uuids = tableRef.value
        ?.getSelectionRows()
        .map((item: any) => item.uuid)
    await toDel(uuids)
}

//批量重试
const batchRetry = async () => {
    const uuids = tableRef.value
        ?.getSelectionRows()
        .map((item: any) => item.uuid)
    await toRetry(uuids)
}

const selectChange = () => {
    resetPage()
}

onMounted(async () => {
    await getLists()
    start()
})
</script>
