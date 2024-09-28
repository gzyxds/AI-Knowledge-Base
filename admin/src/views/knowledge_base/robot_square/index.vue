<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="智能体名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.name"
                        placeholder="请输入智能体名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="分享用户">
                    <div>
                        <el-input
                            class="w-[300px]"
                            v-model="queryParams.author"
                            placeholder="请输入用户编号/昵称"
                            clearable
                            @keyup.enter="resetPage"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="所属分类">
                    <el-select class="w-[280px]" v-model="queryParams.cid" clearable>
                        <el-option label="全部" :value="0" />
                        <el-option
                            v-for="item in optionsData.cate"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="是否显示">
                    <el-select class="w-[280px]" v-model="queryParams.is_show" clearable>
                        <el-option label="全部" value="" />
                        <el-option label="显示" :value="1" />
                        <el-option label="隐藏" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item label="公开时间">
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
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="智能体名称" min-width="200">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                radius="50%"
                                class="flex-none"
                                v-if="row.image"
                                :src="row.image"
                                :width="48"
                                :height="48"
                                :preview-src-list="[row.image]"
                                preview-teleported
                                fit="contain"
                            />
                            <div class="ml-1 line-clamp-2">{{ row.name }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="所属分类" min-width="180" prop="category" />
                <el-table-column label="分享者" min-width="150" prop="author" />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.is_show"
                            :active-value="1"
                            :inactive-value="0"
                            @change="handleStatus($event, row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="审核状态" min-width="140">
                    <template #default="{ row }">
                        <div>
                            <el-tag class="ml-2" v-if="row.verify_status == 1" type="success">
                                审核通过
                            </el-tag>
                            <el-tag class="ml-2" v-else-if="row.verify_status == 0" type="warning">
                                审核中
                            </el-tag>
                            <el-tag
                                class="ml-2 cursor-pointer"
                                v-else
                                type="danger"
                                @click="handleReply(row.verify_result)"
                            >
                                审核失败
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="排序" min-width="100">
                    <template #default="{ row }">
                        <div class="flex items-center group">
                            <div>
                                {{ row.sort }}
                            </div>
                            <div
                                class="opacity-0 group-hover:opacity-100 ml-1"
                                v-perms="['kb.square/setSort']"
                            >
                                <popover-input
                                    type="number"
                                    :limit="32"
                                    @confirm="handleSort($event, row.id)"
                                >
                                    <div class="cursor-pointer text-primary flex">
                                        <icon name="el-icon-EditPen" />
                                    </div>
                                </popover-input>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="公开时间" prop="create_time" min-width="180" />
                <el-table-column label="操作" width="160" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            type="primary"
                            v-perms="['kb.square/edit']"
                            link
                            @click="handleEdit(row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-if="!row.verify_status"
                            v-perms="['kb.square/verifyStatus']"
                            type="primary"
                            link
                            @click="handleAudit([row.id])"
                        >
                            审核
                        </el-button>
                        <el-button
                            type="danger"
                            v-perms="['kb.square/del']"
                            link
                            @click="handleDelete(row.id)"
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
        <Edit ref="editPopup" @success="getLists" :cate-lists="optionsData.cate" />
        <AuditPopup
            v-if="showAudit"
            ref="auditRef"
            @success="getLists"
            @close="showAudit = false"
        ></AuditPopup>
    </div>
</template>
<script lang="ts" setup name="robotSquare">
import {
    getRobotSquareLists,
    delRobotSquare,
    putRobotSquareStatus,
    putRobotSquareSort,
    getRobotCateAll
} from '@/api/knowledge_base/robot_square'
import { useDictOptions } from '@/hooks/useDictOptions'
import { usePaging } from '@/hooks/usePaging'
import Edit from './edit.vue'
import feedback from '@/utils/feedback'
import AuditPopup from "./audit.vue";

const editPopup = shallowRef()
const auditRef = shallowRef()
const showAudit = ref<boolean>(false)

const queryParams = reactive({
    name: '',
    author: '',
    cid: 0,
    is_show: '',
    start_time: '',
    end_time: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getRobotSquareLists,
    params: queryParams
})

const { optionsData } = useDictOptions<{
    cate: any[]
}>({
    cate: {
        api: getRobotCateAll
    }
})

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除？')
    await delRobotSquare({ id })
    getLists()
}

//打开编辑
const handleEdit = async (row: any) => {
    await nextTick()
    editPopup.value.open()
    editPopup.value.setFormData(row)
}

// 审核
const handleAudit = async (ids: number[]) => {
    showAudit.value = true
    await nextTick()
    auditRef.value?.open(ids)
}

const handleReply = async (result: string) => {
    await feedback.alert(result)
}

const handleStatus = async (is_show: number, id: number) => {
    try {
        await putRobotSquareStatus({ id, is_show })
    } finally {
        getLists()
    }
}
const handleSort = async (sort: string, id: number) => {
    await putRobotSquareSort({ id, sort })
    getLists()
}
getLists()
</script>
