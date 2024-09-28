<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="前景状态">
                    <el-select class="w-[280px]" v-model="queryParams.status">
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
            <el-button type="primary" v-perms="['digital.preposition/add']" @click="addFg"
                >+ 新增前景</el-button
            >
            <el-button
                v-perms="['digital.preposition/batchDel']"
                @click="batchDEl"
                :disabled="!btnStatus"
                >批量删除</el-button
            >
            <el-table
                class="mt-2"
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                ref="tableRef"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="50" />
                <el-table-column label="前景图" min-width="120">
                    <template #default="{ row }">
                        <el-image
                            :preview-teleported="true"
                            :preview-src-list="[row.url]"
                            class="w-[80px]"
                            :src="row.url"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-perms="['digital.preposition/status']"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="BtnChangeStatus(row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="180" />
                <el-table-column label="创建时间" prop="create_time" min-width="180" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            type="primary"
                            v-perms="['digital.preposition/edit']"
                            link
                            @click="openEdit(row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['digital.preposition/del']"
                            type="danger"
                            @click="del(row.id)"
                            link
                        >
                            删除</el-button
                        >
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <add-pop
            ref="addPopRef"
            v-if="popShow"
            @success="
                () => {
                    popShow = false
                    getLists()
                }
            "
            @close="popShow = false"
        />
    </div>
</template>

<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import { ref } from 'vue'
import addPop from './components/addPop.vue'
import { getFGList, changeStatus, deLFG, batchDeLFG } from '@/api/digital_human/fg'
import feedback from '@/utils/feedback'
import { ElTable } from 'element-plus'

const queryParams: any = ref({
    name: '',
    status: ''
})
const popShow = ref(false)
const addPopRef = shallowRef()
const tableRef = shallowRef<InstanceType<typeof ElTable>>()

//新增背景
const addFg = async () => {
    popShow.value = true
    await nextTick()
    addPopRef.value.open()
}

//打开编辑
const openEdit = async (row: any) => {
    popShow.value = true
    await nextTick()
    addPopRef.value.open(row)
}

//修改状态
const BtnChangeStatus = async (id: number) => {
    await changeStatus({ id })
}

//删除前景
const del = async (id: number) => {
    await feedback.confirm('是否确认删除！')
    await deLFG({ id })
    getLists()
}

//批量删除
const batchDEl = async () => {
    const id = tableRef.value?.getSelectionRows().map((item: any) => item.id)
    await feedback.confirm('是否确定批量删除！')
    await batchDeLFG({ id })
    getLists()
}

//按钮状态
const btnStatus = ref(false)
const handleSelectionChange = (value: any[]) => {
    console.log(value)
    btnStatus.value = value.length != 0
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getFGList,
    params: queryParams.value
})

onMounted(() => {
    getLists()
})
</script>

<style lang="scss" scoped></style>
