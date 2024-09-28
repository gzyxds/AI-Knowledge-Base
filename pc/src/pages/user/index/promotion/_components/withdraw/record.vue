<template>
    <ElDialog
        v-model="recordShowPop"
        width="1000px"
        title="提现记录"
        :close-on-click-modal="false"
        @close="closePop"
    >
        <el-table :data="pager.lists" v-loading="pager.loading" height="500px">
            <el-table-column label="提现单号" prop="sn" />
            <el-table-column label="提现金额" prop="money" />
            <el-table-column label="手续费">
                <template #default="{ row }">
                    <div>
                        {{ row.handling_fee }}({{ row.handling_fee_ratio }})
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="到账金额" prop="left_money" />
            <el-table-column label="提现方式" prop="type_desc" />
            <el-table-column label="提现状态">
                <template #default="{ row }">
                    <el-tag type="success" v-if="row.status_desc == '提现成功'">
                        {{ row.status_desc || '-' }}
                    </el-tag>
                    <el-tag v-else-if="row.status_desc == '待审核'">
                        {{ row.status_desc || '-' }}
                    </el-tag>
                    <el-tag type="danger" v-else-if="row.status_desc == '提现失败'">
                        {{ row.status_desc || '-' }}
                    </el-tag>
                    <el-tag type="warning" v-else>
                        {{ row.status_desc || '-' }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column label="申请时间" prop="create_time" />
            <el-table-column label="操作">
                <template #default="{ row }">
                    <div>
                        <el-button
                            type="primary"
                            link
                            @click="toDetail(row.id)"
                        >
                            详情
                        </el-button>
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <div class="flex justify-end mt-4">
            <pagination
                v-model="pager"
                @change="getLists"
            />
        </div>

        <Detail ref="detailRef"></Detail>
    </ElDialog>
</template>

<script setup lang="ts">
import {withdrawList} from '@/api/promotion'
import Detail from './detail.vue'

const emit = defineEmits(['closePop'])

//弹框显示/隐藏
const recordShowPop = ref<boolean>(false)
const detailRef = ref<InstanceType<typeof Detail>>()
const formData = ref({
    type: 'money',
    change_type: '',
    start_time: '',
    end_time: ''
})

const {pager, getLists, resetPage, resetParams} = usePaging({
    fetchFun: withdrawList,
    params: formData.value
})

const toDetail = (id: any) => {
    detailRef.value.open({ id })
}

//打开弹框
const open = () => {
    recordShowPop.value = true
    getLists()
}

//关闭弹框
const closePop = () => {
    recordShowPop.value = false
    emit('closePop')
}

defineExpose({open})
</script>

<style scoped lang="scss"></style>
