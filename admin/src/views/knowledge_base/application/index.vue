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
                <el-form-item label="创建人">
                    <div>
                        <el-input
                            class="w-[300px]"
                            v-model="queryParams.keyword"
                            placeholder="请输入创建人昵称/用户编号"
                            clearable
                            @keyup.enter="resetPage"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="智能体状态">
                    <el-select
                        class="w-[280px]"
                        v-model="queryParams.is_enable"
                        placeholder="请选择智能体状态"
                        clearable
                    >
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item label="是否公开">
                    <el-select class="w-[280px]" v-model="queryParams.is_public" clearable>
                        <el-option label="全部" value="" />
                        <el-option label="公开" :value="1" />
                        <el-option label="私有" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="图标" min-width="100">
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
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="智能体名称"
                    prop="name"
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="创建人" min-width="180">
                    <template #default="{ row }">
                        <div>{{ row.user.nickname }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="关联知识库" min-width="150">
                    <template #default="{ row }">
                        <div class="flex flex-wrap mx-[-5px]">
                            <router-link
                                v-for="item in row.knows"
                                :key="item.id"
                                :to="{
                                    path: getRoutePath('kb.know/files'),
                                    query: {
                                        id: item.id
                                    }
                                }"
                            >
                                <div
                                    class="bg-primary-light-9 text-primary px-[10px] py-[2px] rounded mx-[5px] my-[5px]"
                                >
                                    <OverflowTooltip :content="item.name" />
                                </div>
                            </router-link>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.is_enable"
                            :active-value="1"
                            :inactive-value="0"
                            @change="handleStatus(row.id)"
                        />
                    </template>
                </el-table-column>

                <el-table-column label="是否公开" min-width="100">
                    <template #default="{ row }">
                        {{ row.is_public ? '公开' : '私有' }}
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="100" />
                <el-table-column label="创建时间" prop="create_time" min-width="180" />
                <el-table-column label="操作" width="160" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link>
                            <router-link
                                :to="{
                                    path: getRoutePath('kb.robot/detail'),
                                    query: {
                                        id: row.id
                                    }
                                }"
                                v-perms="['kb.robot/detail']"
                            >
                                查看
                            </router-link>
                        </el-button>
                        <el-button
                            type="danger"
                            v-perms="['kb.robot/del']"
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
    </div>
</template>
<script lang="ts" setup name="knowledgeBaseApplication">
import {
    getApplyManageLists,
    applyManageDelete,
    applyManageStatus
} from '@/api/knowledge_base/application'
import { usePaging } from '@/hooks/usePaging'
import { getRoutePath } from '@/router'
import feedback from '@/utils/feedback'

const router = useRouter()

const queryParams = reactive({
    name: '',
    is_enable: '',
    keyword: '',
    create_type: '',
    is_public: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getApplyManageLists,
    params: queryParams
})

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除？')
    await applyManageDelete({ id })
    getLists()
}

const handleStatus = async (id: number) => {
    try {
        await applyManageStatus({ id })
    } finally {
        getLists()
    }
}

getLists()
</script>
