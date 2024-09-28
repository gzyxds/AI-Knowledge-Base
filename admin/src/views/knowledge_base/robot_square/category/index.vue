<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="分类名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.name"
                        placeholder="请输入分类名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>

                <el-form-item label="状态">
                    <el-select class="w-[280px]" v-model="queryParams.is_enable">
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-button type="primary" v-perms="['kb.robotCate/add']" @click="openPop">
                + 新增
            </el-button>
            <el-table class="mt-4" size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="分类图标" min-width="100">
                    <template #default="{ row }">
                        <el-image v-if="row.image" :src="row.image" class="w-[44px] h-[44px]" />
                    </template>
                </el-table-column>
                <el-table-column label="类别名称" prop="name" min-width="120" />
                <el-table-column label="智能体数量" prop="example_sum" min-width="100" />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            @change="changeStatus(row.id)"
                            v-model="row.is_enable"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="120" />
                <el-table-column label="创建时间" prop="create_time" min-width="100" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <div>
                            <el-button
                                type="primary"
                                v-perms="['kb.robotCate/edit']"
                                link
                                @click="openEdit(row)"
                            >
                                编辑
                            </el-button>
                            <el-button
                                type="danger"
                                v-perms="['kb.robotCate/del']"
                                @click="del(row.id)"
                                link
                            >
                                删除
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <Edit ref="editPopup" @success="getLists" />
    </div>
</template>

<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import Edit from './edit.vue'

import feedback from '@/utils/feedback'
import {
    delRobotCate,
    putRobotCateStatus,
    getRobotCateLists
} from '@/api/knowledge_base/robot_square'

const queryParams: any = ref({
    name: '',
    is_enable: ''
})

const editPopup = shallowRef()
const openPop = async () => {
    await nextTick()
    editPopup.value.open()
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getRobotCateLists,
    params: queryParams.value
})

//修改分类状态
const changeStatus = async (id: number) => {
    await putRobotCateStatus({ id })
}

//打开编辑
const openEdit = async (row: any) => {
    await nextTick()
    editPopup.value.open('edit')
    editPopup.value.setFormData(row)
}

//删除
const del = async (id: number) => {
    await feedback.confirm('确定删除？')
    await delRobotCate({ id })
    getLists()
}

onMounted(() => {
    getLists()
})
</script>

<style lang="scss" scoped></style>
