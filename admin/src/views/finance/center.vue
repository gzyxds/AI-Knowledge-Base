<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <template #header>
                <span class="font-medium">经营状况</span>
            </template>
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.business_overview?.total_order_amount }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        累计收入金额
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="所有充值订单和会员订单实付金额总和（包含已退款金额）"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.business_overview?.total_order_num }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        累计订单数（笔）
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="所有充值订单和会员订单成交订单总数（包含已退款订单）"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <!-- <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.business_overview?.total_amount }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        累计充值金额
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="用户累计充值实付金额总和（包含退款的）"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.business_overview?.total_num }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        充值订单数
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="用户累计充值订单总数（包含退款的）"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div> -->
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.business_overview?.total_refund_amount }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        累计退款金额
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="充值订单累计已退款的金额总和"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.business_overview?.total_refund_num }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        退款订单
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="充值订单累计已退款的订单数量"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.business_overview?.net_income }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        累计净收入
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="除掉退款金额，实际获得的收入金额"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
            </div>
        </el-card>

        <el-card shadow="never" class="!border-none mt-4">
            <template #header>
                <span class="font-medium">用户概况</span>
            </template>
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.user_overview?.user_num }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        用户总人数
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="平台用户总数"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.user_overview?.total_recharge_num }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        累计充值人数
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="充值的人数总和（包含已退款的，按照用户ID来计算，即一个用户ID充值多次也只算一个人）"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.user_overview?.user_total_amount }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        用户累计消费金额
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="用户累计消费的金额总和（含充值和开通会员）"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.user_overview?.user_total_quiz }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        用户累计提问次数

                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="用户累计提问对话的次数总和"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.user_overview?.user_balance_chat }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        用户剩余电力值数量
                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="用户剩余的电力值数量总和"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>

                <div class="flex flex-col items-start justify-center">
                    <div class="font-medium text-[24px]">
                        {{ centerData.user_overview?.user_robot_num }}
                    </div>
                    <div class="text-[14px] text-[#666666] flex items-center">
                        用户剩余智能体数

                        <el-tooltip
                            class="box-item"
                            effect="dark"
                            content="用户剩余智能体数总和"
                            placement="bottom"
                        >
                            <img
                                src="@/assets/images/question.png"
                                alt=""
                                class="w-[16px] h-[16px] ml-[5px]"
                            />
                        </el-tooltip>
                    </div>
                </div>
            </div>
        </el-card>
    </div>
</template>
<script setup lang="ts">
import { financeCenter } from '@/api/finance';

const centerData: any = ref([]);

const getData = async () => {
    centerData.value = await financeCenter();
};

onMounted(() => {
    getData();
});
</script>

<style scoped lang="scss"></style>
