<template>
    <div>
        <el-card shadow="never" class="!border-none mt-4">
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
                <el-form-item label="分类状态">
                    <el-select class="!w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="开启" value="1" />
                        <el-option label="关闭" value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-tabs v-model="currentTab" @tabChange="changeTabs">
                <el-tab-pane
                    v-for="item in tabsMap"
                    :label="item.name + `(${ pager.extend[item.alis] })`"
                    :name="item.type"
                    :index="item.type"
                    :key="item.type"
                >
                </el-tab-pane>
            </el-tabs>

            <el-button
                v-perms="['square.squareCategory/add']"
                type="primary"
                class="mb-4"
                @click="handleAdd"
            >
                + 新增
            </el-button>
            <el-button
                v-perms="['square.squareCategory/del']"
                class="mb-4"
                @click="handleDelete(selectData)"
                :disabled="!selectData.length"
            >
                批量删除
            </el-button>
            <el-table
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="分类名称" prop="name" min-width="120" />
                <el-table-column label="所属应用" prop="name" min-width="120">
                    <template #default="{ row }">
                        {{ typeMap[row.type] }}
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-perms="['square.squareCategory/status']"
                            @change="changeStatus(row.id)"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="已被关联" min-width="100" prop="relevance_num" />
                <el-table-column label="排序" prop="sort" min-width="100" />
                <el-table-column label="创建时间" prop="create_time" sortable min-width="100" />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['square.squareCategory/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['square.squareCategory/del']"
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
        <Edit v-if="showEdit" ref="editRef" :type="queryParams.type || 1" @success="getLists" @close="showEdit = false" />
    </div>
</template>
<script setup lang="ts" name="aiSquareCate">
import { usePaging } from '@/hooks/usePaging'
import Edit from './edit.vue'
import { getSquareCategory, deleteSquareCategory, putSquareCategoryStatus } from '@/api/ai_application/ai_square'
import feedback from '@/utils/feedback'

const editRef = shallowRef<InstanceType<typeof Edit>>()
//搜索参数
const queryParams = reactive<{
    name: string,
    status: string,
    type?: number | string
}>({
    name: '',
    status: '',
    type: ''
})
const showEdit = ref(false)
const selectData = ref<any[]>([])

const tabsMap = [
    {
        name: '全部',
        type: '',
        alis: 'all_count'
    },
    {
        name: 'AI绘画',
        type: 1,
        alis: 'draw_count'
    },
    {
        name: 'AI音乐',
        type: 2,
        alis: 'music_count'
    },
    {
        name: 'AI视频',
        type: 3,
        alis: 'video_count'
    }
]
const typeMap = {
    1: 'AI绘画',
    2: 'AI音乐',
    3: 'AI视频'
}
const currentTab = ref('')
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map((item) => item.id)
}

const changeTabs = (type: string) => {
    queryParams.type = type
    getLists()
}

//添加
const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}
//编辑
const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.setFormData(data)
}
//删除
const handleDelete = async (id: number[] | number) => {
    await feedback.confirm('确定要删除？')
    await deleteSquareCategory({ id })
    getLists()
}

//修改状态
const changeStatus = (id: any) => {
    putSquareCategoryStatus({ id })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getSquareCategory,
    params: queryParams
})

getLists()
</script>
