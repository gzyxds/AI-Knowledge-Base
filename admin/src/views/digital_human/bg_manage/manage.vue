<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="版型类型">
                    <el-select class="w-[280px]" v-model="queryParams.type">
                        <el-option label="竖版" :value="1" />
                        <el-option label="横版" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="所属分类">
                    <el-select class="w-[280px]" v-model="queryParams.category_id">
                        <el-option label="无分类" :value="0" />
                        <el-option
                            v-for="(item, key) in categoryList"
                            :key="key"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
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
            <el-button type="primary" v-perms="['digital.background/add']" @click="addBg"
                >+ 新增背景</el-button
            >
            <el-button
                @click="adjustClass"
                v-perms="['digital.background/batchEdit']"
                :disabled="!btnStatus"
                >批量调整</el-button
            >
            <el-button
                v-perms="['digital.background/batchDel']"
                @click="batchDelete"
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
                <el-table-column type="selection" width="55" />
                <el-table-column label="背景图" prop="sn" min-width="120">
                    <template #default="{ row }">
                        <el-image
                            :preview-teleported="true"
                            :preview-src-list="[row.url]"
                            :src="row.url"
                            class="w-[80px]"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="版型类型" prop="type_desc" min-width="100" />
                <el-table-column label="所属分类" min-width="100">
                    <template #default="{ row }">
                        <span>{{ row.category_name || '无分类' }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-perms="['digital.background/status']"
                            @change="changeStatus(row.id)"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="100" />

                <el-table-column label="创建时间" prop="create_time" min-width="120" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <div>
                            <el-button
                                type="primary"
                                v-perms="['digital.background/edit']"
                                link
                                @click="openEdit(row)"
                            >
                                编辑
                            </el-button>
                            <el-button
                                type="danger"
                                link
                                @click="del(row.id)"
                                v-perms="['digital.background/del']"
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
        <add-pop
            v-if="popShow"
            ref="addPopRef"
            @close="popShow = false"
            @success="
                () => {
                    popShow = false
                    getLists()
                }
            "
        />
        <adjustClassPopVue
            v-if="popShow"
            ref="adjustPopRef"
            @success="
                () => {
                    popShow = false
                    getLists()
                }
            "
        />
    </div>
</template>

<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import addPop from './components/addPop.vue'
import adjustClassPopVue from './components/adjustClassPop.vue'
import {
    getBGList,
    changBGStatus,
    delBG,
    getCategoryLists,
    batchDel,
    batchEdit
} from '@/api/digital_human/bg'

import { ref } from 'vue'
import feedback from '@/utils/feedback'

const queryParams: any = ref({
    type: '',
    status: '',
    category_id: ''
})

const categoryList: any = ref([])

//弹框
const popShow = ref(false)
const addPopRef = shallowRef()
const adjustPopRef = shallowRef()

//表格ref
const tableRef = shallowRef()

//按钮状态
const btnStatus = ref(false)

//新增背景
const addBg = async () => {
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
const changeStatus = async (id: number) => {
    await changBGStatus({ id })
}

//删除
const del = async (id: number) => {
    await feedback.confirm('确定删除？')
    await delBG({ id })
    getLists()
}

//获取分类列表
const getCategory = async () => {
    categoryList.value = await getCategoryLists()
}

//批量修改
const adjustClass = async () => {
    popShow.value = true
    await nextTick()
    const ids = tableRef.value.getSelectionRows().map((item: any) => {
        return item.id
    })

    adjustPopRef.value.open(ids)
}

//批量删除
const batchDelete = async () => {
    const ids = tableRef.value.getSelectionRows().map((item: any) => {
        return item.id
    })
    await feedback.confirm('是否确认批量删除！')
    await batchDel({ id: ids })
    getLists()
}

const handleSelectionChange = (value: any[]) => {
    console.log(value)
    btnStatus.value = value.length != 0
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getBGList,
    params: queryParams.value
})

onMounted(async () => {
    await getLists()
    await getCategory()
})
</script>

<style lang="scss" scoped></style>
