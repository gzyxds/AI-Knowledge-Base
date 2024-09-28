<template>
    <div class="mt-4 flex-1 flex flex-col min-h-0">
        <div class="flex-1 min-h-0">
            <el-table
                v-loading="pager.loading"
                height="100%"
                size="large"
                :data="pager.lists"
            >
                <el-table-column
                    label="订单编号"
                    prop="order_sn"
                    min-width="130"
                >
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.order_sn || '-' }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="套餐名称" prop="name" min-width="130" />
                <el-table-column label="套餐内容" min-width="200">
                    <template #default="{ row }">
                        <div>
                            {{ appStore.getTokenUnit }}数量：{{
                                row.chat_balance
                            }}
                            <span v-if="row.give_chat_balance > 0">
                                (赠送{{ row.give_chat_balance }})
                            </span>
                        </div>
                        <div>
                            智能体数量：{{ row.robot_number }}
                            <span v-if="row.give_robot_number > 0">
                                (赠送{{ row.give_robot_number }}个)
                            </span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="购买方式" min-width="100">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.channel_text }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="支付方式"
                    prop="pay_way_text"
                    min-width="100"
                />
                <el-table-column label="实付金额" min-width="100">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{
                                row.channel == 2 ? '-' : '¥' + row.order_amount
                            }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="支付状态"
                    prop="pay_status_text"
                    min-width="100"
                >
                    <template #default="{ row }">
                        <div class="flex flex-col">
                            <span>{{ row.pay_status_text }}</span>
                            <span
                                v-if="row.refund_status == 1"
                                class="text-warning"
                                >已退款</span
                            >
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="支付/操作时间"
                    prop="pay_time"
                    min-width="160"
                >
                    <template #default="{ row }">
                        {{
                            row?.pay_time == '-'
                                ? row?.create_time
                                : row?.pay_time
                        }}
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { getRechargeRecord } from '@/api/recharge'
import { useAppStore } from '@/stores/app'
import { usePaging } from '@/composables/usePaging'

const appStore = useAppStore()

const { pager, getLists } = usePaging({
    fetchFun: getRechargeRecord
})

onMounted(() => {
    getLists()
})
</script>
