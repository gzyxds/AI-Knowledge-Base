<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="知识库名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.name"
                        placeholder="请输入知识库名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="创建人">
                    <div>
                        <el-input
                            class="w-[300px]"
                            v-model="queryParams.user"
                            placeholder="请输入创建人昵称/用户编号"
                            clearable
                            @keyup.enter="resetPage"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select class="w-[280px]" v-model="queryParams.is_enable">
                        <el-option label="全部" value />
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
            <div class="mt-4">
                <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                    <el-table-column label="ID" prop="id" width="80" />
                    <el-table-column label="封面" width="100">
                        <template #default="{ row }">
                            <el-image class="w-[60px] h-[60px]" :src="row.image" />
                        </template>
                    </el-table-column>
                    <el-table-column label="知识库名称" prop="name" min-width="160" />

                    <el-table-column label="知识库创建者" min-width="180">
                        <template #default="{ row }">
                            <div>{{ row.create_user }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column label="当前所有者" min-width="180">
                        <template #default="{ row }">
                            <el-popover placement="right" width="220px" trigger="hover">
                                <div class="flex items-center">
                                    <span class="mr-4">头像: </span>
                                    <el-avatar :size="50" :src="row?.user?.avatar" />
                                </div>
                                <div class="mt-[20px]">
                                    <span class="mr-4"> 昵称: </span>
                                    <span>{{ row?.user?.nickname }}</span>
                                </div>
                                <div class="mt-[20px]">
                                    <span class="mr-4">编号: </span>
                                    <span>{{ row?.user.sn }}</span>
                                </div>
                                <template #reference>
                                    {{ row?.user?.nickname }}
                                </template>
                            </el-popover>
                        </template>
                    </el-table-column>
                    <el-table-column label="状态" min-width="120">
                        <template #default="{ row }">
                            <el-switch
                                v-model="row.is_enable"
                                :active-value="1"
                                :inactive-value="0"
                                v-perms="['kb.know/changeStatus']"
                                @change="changStatus(row.id)"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column label="最新更新时间" prop="create_time" min-width="150" />
                    <el-table-column label="操作" prop="id" min-width="280" fixed="right">
                        <template #default="{ row }">
                            <el-button
                                type="primary"
                                link
                                v-perms="['kb.know/files']"
                                v-if="row.create_type != 2"
                            >
                                <RouterLink
                                    :to="{
                                        path: '/konwledge_base/knowledge_base/study_data',
                                        query: {
                                            id: row.id,
                                            name: row.name
                                        }
                                    }"
                                >
                                    数据学习
                                </RouterLink>
                            </el-button>
                            <el-button
                                type="primary"
                                link
                                @click="userPickerOpen(row)"
                                v-perms="['kb.know/del']"
                            >
                                转移所有权
                            </el-button>
                            <el-button
                                type="danger"
                                link
                                @click="delData(row)"
                                v-perms="['kb.know/del']"
                            >
                                删除</el-button
                            >
                        </template>
                    </el-table-column>
                </el-table>
                <div class="flex justify-end mt-4">
                    <pagination v-model="pager" @change="getLists" />
                </div>
            </div>
        </el-card>

        <UserPickerPopup
            v-if="showUserPicker"
            ref="userPickerRef"
            type="single"
            :maxNum="1"
            :disabled="false"
            @close="handleTransfer"
        >
        </UserPickerPopup>

        <el-dialog
            v-model="isTransferModalVisible"
            width="450px"
            class="!rounded-[12px]"
            center
            draggable
            destroy-on-close
            close-on-click-modal="false"
        >
            <template #header>
                <div class="w-full text-left">
                    <div class="text-lg font-medium">成员处理方式</div>
                </div>
            </template>
            <div>
                <el-checkbox
                    v-model="transferType"
                    true-label="all"
                    label="同步转移所有者的所有成员。"
                    size="large"
                />
                <el-checkbox
                    v-model="transferType"
                    true-label="kb"
                    label="不同步转移所有者的所有成员，只转移知识库。"
                    size="large"
                />
            </div>
            <template #footer>
                <div class="flex justify-end">
                    <el-button @click="isTransferModalVisible = false"
                    >取消</el-button
                    >
                    <el-button
                        type="primary"
                        :loading="isTransferInProgress"
                        @click="transferOwnership"
                    >确认
                    </el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { nextTick, ref, shallowRef } from 'vue'
import { usePaging } from '@/hooks/usePaging'
import {
    knowKnowledgeList,
    knowKnowledgeDel,
    knowKnowledgeStatus,
    transferFileData
} from '@/api/knowledge_training/manage'
import feedback from '@/utils/feedback'

const showUserPicker = ref(false)
const userPickerRef = shallowRef()
const kb_id = ref('')
//搜索参数
const queryParams: any = ref({
    name: '',
    is_enable: '',
    user: ''
})

const isTransferModalVisible = ref(false)
const transferType = ref<string>('all')
const isTransferInProgress = ref(false)
const transferTarget = reactive({ id: -1 })

//分页组件
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: knowKnowledgeList,
    params: queryParams.value
})

//删除数据
const delData = async (row: any) => {
    await feedback.customConfirm('确定删除 ', ' 知识库吗？', row.name, 'color:red')
    await knowKnowledgeDel({ id: row.id })
    getLists()
}

//修改状态
const changStatus = async (id: number) => {
    await knowKnowledgeStatus({ id })
}

const userPickerOpen = async (row: any) => {
    kb_id.value = row.id
    showUserPicker.value = true
    await nextTick()
    userPickerRef.value?.open()
}

const handleTransfer = async (value: any) => {
    transferTarget.id = value.id
    showUserPicker.value = false
    isTransferModalVisible.value = true
}

const transferOwnership = async () => {
    isTransferInProgress.value = true
    try {
        await transferFileData({
            id: kb_id.value,
            user_id: transferTarget.id,
            type: transferType.value
        })
        getLists()
        isTransferModalVisible.value = false
    } finally {
        isTransferInProgress.value = false
    }
}

onMounted(() => {
    getLists()
})
</script>

<style scoped lang="scss"></style>
