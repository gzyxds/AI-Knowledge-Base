<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-button
                v-perms="['user.grouping/add']"
                type="primary"
                @click="showPop({ id: '', name: '' })"
                >+ 新增分组</el-button
            >
            <el-button
                v-perms="['user.grouping/del']"
                :disabled="isSelet.length == 0"
                @click="groupDel(isSelet)"
                >删除</el-button
            >
            <div class="mt-4">
                <el-table
                    ref="tableRef"
                    class="mt-2"
                    size="large"
                    :data="optionsData.dataList"
                    @selection-change="handleSelectionChange"
                >
                    <el-table-column type="selection" width="55" />
                    <el-table-column label="分组名称" prop="name" min-width="120" />
                    <el-table-column label="用户数" prop="user_sum" min-width="120" />
                    <!-- <el-table-column label="关联智能体" prop="apply_sum" min-width="120" /> -->
                    <el-table-column label="操作" prop="sn" min-width="120">
                        <template #default="{ row }">
                            <div>
                                <el-button
                                    type="primary"
                                    v-perms="['user.grouping/edit']"
                                    link
                                    @click="showPop(row)"
                                    >编辑</el-button
                                >
                                <el-button
                                    type="danger"
                                    v-perms="['user.grouping/del']"
                                    link
                                    @click="groupDel([row.id])"
                                    >删除</el-button
                                >
                            </div>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-card>
        <addPop v-if="popShow" ref="popRef" @success="popClose" />
    </div>
</template>

<script setup lang="ts">
import { shallowRef } from 'vue'
import { userGroupingList, userGroupingDel } from '@/api/consumer'
import { useDictOptions } from '@/hooks/useDictOptions'
import addPop from './components/addPop.vue'
import { ElTable } from 'element-plus'
import feedback from '@/utils/feedback'

//弹框ref
const popRef = shallowRef()
const popShow = ref(false)
//表格
const tableRef = shallowRef<InstanceType<typeof ElTable>>()

const { optionsData, refresh } = useDictOptions<{ dataList: any }>({
    dataList: {
        api: userGroupingList
    }
})

const isSelet = ref([])

const handleSelectionChange = (value: any) => {
    isSelet.value = value.map((item: any) => item.id)
}

//删除
const groupDel = async (id: string[]) => {
    await feedback.confirm('确认删除？')
    await userGroupingDel({ id: id })
    refresh()
}

const showPop = async (row?: any) => {
    popShow.value = true
    await nextTick()
    popRef.value.open({ id: row.id, name: row.name })
}

const popClose = () => {
    popShow.value = false
    refresh()
}
</script>

<style lang="scss" scoped></style>
