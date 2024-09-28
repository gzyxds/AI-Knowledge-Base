<template>
    <div class="mt-4 flex-1 flex flex-col min-h-0">
        <div class="flex-1 min-h-0">
            <el-table
                v-loading="loading"
                height="100%"
                size="large"
                :data="memberLog"
            >
                <!-- 根据会员订单的字段进行相应调整 -->
                <el-table-column label="订单编号" prop="order_sn" width="170" >
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.order_sn || '-' }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="会员等级" prop="name" min-width="130" >
                <template #default="{ row }">
                    <div class="flex items-center">
                        <span>{{ row.package_name || row.name || '-' }}</span>
                    </div>
                </template>
                </el-table-column>
                <el-table-column label="会员时长" prop="original_package_long_time" min-width="100" >
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.original_package_long_time || '-' }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="有效期至" prop="package_long_time" min-width="160" >
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.package_long_time || '-' }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="购买方式" prop="channel_text" min-width="100" />
                <el-table-column label="支付方式" prop="pay_way_text" min-width="100" >
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.pay_way_text || '-' }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="实付金额" min-width="100">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span v-if="row.order_amount">¥{{ row.order_amount }}</span>
                            <span v-else>-</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="支付状态" prop="pay_status_text" min-width="100">
                    <template #default="{ row }">
                        <div class="flex flex-col">
                            <span>{{ row.pay_status_text || '-' }}</span>
                            <span v-if="row.refund_status == 1" class="text-warning">已退款</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="支付/操作时间" prop="pay_time_text" min-width="160">
                    <template #default="{ row }">
                        {{ row?.pay_time_text || row?.create_time }}
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { memberPackageRecord } from '@/api/member'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
const memberLog = ref<any>([])
const loading = ref<boolean>(false)

const getLists = async () => {
    loading.value = true
    try {
        memberLog.value = await memberPackageRecord()
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    getLists()
})
</script>