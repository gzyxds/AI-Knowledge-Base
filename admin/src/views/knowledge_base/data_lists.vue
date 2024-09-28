<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="关键词">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.keyword"
                        placeholder="请输入关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="知识库名称">
                    <div>
                        <el-input
                            class="w-[300px]"
                            v-model="queryParams.know"
                            placeholder="请输入知识库名称"
                            clearable
                            @keyup.enter="resetPage"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="创建用户">
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
                <el-form-item label="训练模型">
                    <div class="w-80">
                        <el-select v-model="queryParams.model" class="w-full" clearable>
                            <el-option
                                v-for="item in aiModels"
                                :key="item.model"
                                :label="item.name"
                                :value="item.model"
                            />
                        </el-select>
                    </div>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div class="mt-[-16px]">
                <el-tabs v-model="queryParams.status" @tab-change="resetPage()">
                    <el-tab-pane
                        v-for="item in tabsList"
                        :label="`${item.name}(${pager.extend?.[item.key] || 0})`"
                        :name="item.type"
                        :key="item.type"
                    />
                </el-tabs>
            </div>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="序号" min-width="60">
                    <template #default="{ $index }">
                        {{ $index }}
                    </template>
                </el-table-column>
                <el-table-column label="文档内容" min-width="200">
                    <template #default="{ row }">
                        <OverflowTooltip
                            :content="row.question"
                            disabled
                            :line="3"
                            :teleported="true"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="补充内容" min-width="200">
                    <template #default="{ row }">
                        <OverflowTooltip
                            :content="row.answer"
                            disabled
                            :line="3"
                            :teleported="true"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="学习状态" min-width="120">
                    <template #default="{ row }">
                        <div>
                            <el-tag v-if="row.status == 0" type="info">
                                {{ row.status_msg }}
                            </el-tag>
                            <el-tag v-else-if="row.status == 1" type="warning">{{
                                row.status_msg
                            }}</el-tag>
                            <el-tag v-else-if="row.status == 2" type="success">
                                {{ row.status_msg }}
                            </el-tag>
                            <template v-else-if="row.status == 3">
                                <el-tag class="cursor-pointer" type="danger">
                                    {{ row.status_msg }}
                                </el-tag>
                                <div v-if="row.error" class="text-error text-xs">
                                    {{ row.error }}
                                </div>
                            </template>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="所属知识库" prop="kb_name" min-width="150" />
                <el-table-column label="操作用户" prop="nickname" min-width="120" />
                <el-table-column label="训练模型" prop="model" min-width="120" />
                <el-table-column label="消耗电力值" prop="tokens" min-width="120" />
                <el-table-column label="最近更新时间" prop="update_time" min-width="180" />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleView(row)"> 查看 </el-button>
                        <el-button type="danger" link @click="delData(row)"> 删除 </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <DataView v-model:model-value="dataState.current" v-model:show="dataState.show" />
    </div>
</template>
<script lang="ts" setup name="knowledgeDataList">
import { usePaging } from '@/hooks/usePaging'
import { delFileData, getTeachLists } from '@/api/knowledge_training/manage'
import DataView from './components/data-view.vue'
import feedback from '@/utils/feedback'
import { getKeyPoolAiModel } from '@/api/ai_setting/ai_key'

const dataState = reactive({
    current: {} as any,
    show: false
})
const tabsList = reactive([
    {
        name: '全部',
        type: '',
        key: 'allCount'
    },
    {
        name: '学习成功',
        type: 2,
        key: 'okCount'
    },
    {
        name: '等待学习',
        type: 0,
        key: 'waitCount'
    },
    {
        name: '学习中',
        type: 1,
        key: 'ingCount'
    },
    {
        name: '学习失败',
        type: 3,
        key: 'failCount'
    }
])
const queryParams = reactive({
    status: '',
    keyword: '',
    know: '',
    user: '',
    model: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getTeachLists,
    params: queryParams
})
const aiModels = ref<any[]>([])

const getAiModel = async () => {
    try {
        const data = await getKeyPoolAiModel({
            type: 2
        })
        aiModels.value = data
    } catch (error) {
        console.log('获取ai模型失败=>', error)
    }
}

const handleView = (row: any) => {
    dataState.current = row
    dataState.show = true
}

const delData = async (row: any) => {
    await feedback.confirm('删除数据会影响到用户前台智能体的正常使用，请谨慎操作！')
    await delFileData({
        uuid: row.uuid
    })
    getLists()
}

getLists()
getAiModel()
</script>
