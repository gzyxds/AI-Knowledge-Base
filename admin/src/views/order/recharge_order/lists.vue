<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="flex flex-col justify-center items-center">
                    <div class="font-medium text-[24px]">
                        {{ pager.extend.rechargeOrderNumber }}
                    </div>
                    <div class="text-tx-regular">充值订单数</div>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <div class="font-medium text-[24px]">
                        {{ pager.extend.totalRechargeAmount }}
                    </div>
                    <div class="text-tx-regular">累计充值金额</div>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <div class="font-medium text-[24px]">
                        {{ pager.extend.refundOrderNumber }}
                    </div>
                    <div class="text-tx-regular">退款订单</div>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <div class="font-medium text-[24px]">
                        {{ pager.extend.totalRefundAmount }}
                    </div>
                    <div class="text-tx-regular">累计退款金额</div>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <div class="font-medium text-[24px]">
                        {{ pager.extend.netIncomeAmount }}
                    </div>
                    <div class="text-tx-regular">净收入</div>
                </div>
            </div>
        </el-card>
        <el-card shadow="never" class="!border-none mt-4">
            <el-form
                ref="formRef"
                class="mb-[-16px]"
                :model="queryParams"
                :inline="true"
            >
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[200px]"
                        v-model="queryParams.user_info"
                        placeholder="请输入用户ID编号/用户昵称"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="订单来源">
                    <el-select
                        class="!w-[200px]"
                        v-model="queryParams.terminal"
                    >
                        <el-option label="全部" value />
                        <el-option label="微信小程序" value="1" />
                        <el-option label="H5浏览器" value="3" />
                        <el-option label="微信公众号" value="2" />
                        <el-option label="PC Web端" value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item label="支付时间">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item label="退款状态">
                    <el-select
                        class="!w-[200px]"
                        v-model="queryParams.refund_status"
                    >
                        <el-option label="全部" value />
                        <el-option label="未退款" value="0" />
                        <el-option label="已退款" value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage"
                        >查询</el-button
                    >
                    <el-button @click="resetParams">重置</el-button>
                    <!-- <export-data
                        class="ml-2.5"
                        :fetch-fun="getrechargeLists"
                        :params="queryParams"
                        :page-size="pager.size"
                    /> -->
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-table
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
            >
                <el-table-column
                    label="订单编号"
                    prop="order_sn"
                    min-width="180"
                />
                <el-table-column label="用户信息" min-width="210">
                    <template #default="{ row }">
                        <el-popover
                            placement="top"
                            width="220px"
                            trigger="hover"
                        >
                            <div class="flex items-center">
                                <span class="mr-4">头像: </span>
                                <el-avatar :size="50" :src="row?.avatar" />
                            </div>
                            <div class="mt-[4px]">
                                <span class="mr-4"> 昵称: </span>
                                <span>{{ row.nickname }}</span>
                            </div>
                            <div class="mt-[4px]">
                                <span class="mr-4"> 编号: </span>
                                <span>{{ row.sn }}</span>
                            </div>
                            <template #reference>
                                <div class="flex items-center">
                                    <el-avatar :size="50" :src="row?.avatar">
                                        {{ row.nickname }}
                                    </el-avatar>
                                    <div class="ml-[10px] flex-[1_0_0]">
                                        {{ row.nickname }}
                                    </div>
                                </div>
                            </template>
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column label="充值套餐" min-width="120" prop="name" />
                <el-table-column label="套餐内容" min-width="220">
                    <template #default="{ row }">
                        <div>
                            电力值数量：{{ row.chat_balance }}
                            <span v-if="row.give_chat_balance">
                                (赠送{{ row.give_chat_balance }})
                            </span>
                        </div>
                        <div>
                            智能体数量：{{ row.robot_number }}
                            <span v-if="row.give_robot_number">
                                (赠送{{ row.give_robot_number }})
                            </span>
                            个
                        </div>
                        <!-- <div>
                            形象时长：{{ row.video_duration }}
                            <span v-if="row.give_video_duration">
                                (赠送{{ row.give_video_duration }})
                            </span>
                            分钟
                        </div> -->
                    </template>
                </el-table-column>
                <el-table-column label="实付金额" min-width="120">
                    <template #default="{ row }">
                        ￥{{ row.order_amount }}</template
                    >
                </el-table-column>
                <el-table-column label="支付状态" min-width="100">
                    <template #default="{ row }">
                        <div>
                            {{ row.pay_status_text }}
                        </div>
                        <div v-if="row.refund_status == 1" class="text-warning">
                            已退款
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="支付时间"
                    prop="pay_time"
                    min-width="120"
                />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-button
                                v-perms="['recharge.order/detail']"
                                type="primary"
                                link
                                @click="handleDetial(row.id)"
                            >
                                订单详情
                            </el-button>
                            <el-button
                                v-if="row.refund_status == 0"
                                v-perms="['recharge.order/refund']"
                                type="warning"
                                link
                                @click="handleRefund(row.id)"
                            >
                                退款
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <Detial
            v-if="showEdit"
            ref="editRef"
            @success="getLists"
            @close="showEdit = false"
            @refresh="getLists"
        />
    </div>
</template>

<script setup lang="ts">
import Detial from "./detial.vue";
import feedback from "@/utils/feedback";
import { getrechargeLists, RechargeOrderRefund } from "@/api/order/recharge";
import { usePaging } from "@/hooks/usePaging";

/**
 * 处理弹出框
 */
const editRef = shallowRef<InstanceType<typeof Detial>>();
const showEdit = ref(false);
const handleDetial = async (id: any) => {
    showEdit.value = true;
    await nextTick();
    editRef.value?.open(id);
};
/**
 * 退款处理
 */
const handleRefund = async (id: any) => {
    await feedback.confirm("是否确认退款");
    await RechargeOrderRefund({ id });
    getLists();
};
/**
 * 初始化数据
 */
const queryParams = reactive({
    user_info: "", //用户信息
    terminal: "", //订单来源
    refund_status: "", //退款状态
    start_time: "",
    end_time: "",
});

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getrechargeLists,
    params: queryParams,
});
getLists();
</script>
