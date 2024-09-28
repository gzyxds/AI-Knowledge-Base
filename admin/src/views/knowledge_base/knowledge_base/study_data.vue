<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="($route.query.name as string)" @back="$router.back()" />
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-button @click="batchDel" :disabled="tableRef?.getSelectionRows().length == 0"
                >批量删除</el-button
            >
            <el-table
                ref="tableRef"
                class="mt-4"
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="文件名称" prop="name" min-width="150" />
                <el-table-column label="操作用户" min-width="120">
                    <template #default="{ row }">
                        {{ row.user?.nickname }}
                    </template>
                </el-table-column>
                <el-table-column label="所属知识库" prop="id" min-width="120">
                    <template #default>
                        <div>{{ $route.query.name }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="待训练" prop="wait_sum" min-width="120" />
                <el-table-column label="已训练" prop="ok_sum" min-width="120" />
                <el-table-column label="数据总量" prop="total_sum" min-width="120" />
                <el-table-column label="训练模型" prop="model" min-width="120" />
                <el-table-column label="最后更新时间" prop="update_time" min-width="120" />
                <el-table-column fixed="right" label="操作" min-width="150">
                    <template #default="{ row }">
                        <div>
                            <el-button
                                type="primary"
                                @click="openDetail(row.id)"
                                link
                                v-perms="['kb.know/fileDatas']"
                                >数据详情</el-button
                            >
                            <el-button
                                type="danger"
                                @click="del([row.id])"
                                link
                                v-perms="['kb.know/fileRemove']"
                                >删除</el-button
                            >
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <trainDataPop
            @close="popShow = false"
            :kb_id="kb_id"
            :fd_id="fd_id"
            v-if="popShow"
            ref="popRef"
        />
    </div>
</template>

<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import trainDataPop from './components/trainDataPop.vue'
import { fileList, fileDel } from '@/api/knowledge_training/manage'
import { onMounted } from 'vue'
import feedback from '@/utils/feedback'
import type { ElTable } from 'element-plus'

const popShow = ref(false)
const popRef = shallowRef()

const tableRef = ref<InstanceType<typeof ElTable>>()

const route = useRoute()
const kb_id: any = route.query.id
const fd_id = ref(-1)

//搜索参数
// const queryParams: any = ref({
//     name: '',
//     is_enable: '',
//     keyword: '',
//     create_type: 2
// })

//分页组件
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: fileList,
    params: { kb_id }
})

//打开详情
const openDetail = async (fdId: number) => {
    fd_id.value = fdId
    popShow.value = true
    await nextTick()
    popRef.value.open()
}

//删除
const del = async (fids: number[]) => {
    await feedback.confirm('是否确认删除！')
    fileDel({ kb_id, fids })
    getLists()
}

//批量删除
const batchDel = async () => {
    const ids = tableRef.value?.getSelectionRows().map((item: any) => item.id)
    del(ids)
}

onMounted(() => {
    getLists()
})
</script>

<style scoped lang="scss"></style>
