<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="'订单详情'"
            :async="true"
            width="600px"
            :cancelButtonText="'关闭'"
            :confirmButtonText="'申请退款'"
            @confirm="clickConfirm"
        >
            <el-form label-width="84px" :inline="true">
                <el-form-item label="订单编号">
                    <div class="w-[150px]">
                        {{ formData.order_sn }}
                    </div>
                </el-form-item>
                <el-form-item label="订单来源">
                    <div class="w-[150px]">
                        {{ formData.order_terminal_text }}
                    </div>
                </el-form-item>
                <el-form-item label="用户信息">
                    <div class="w-[150px]">
                        {{ formData.nickname }}
                    </div>
                </el-form-item>
                <el-form-item label="订单类型">
                    <div class="w-[150px]">
                        {{ formData.order_type_text }}
                    </div>
                </el-form-item>
                <el-form-item label="支付状态">
                    <div class="w-[150px]">{{ formData.pay_status_text }}</div>
                </el-form-item>
                <el-form-item label="支付方式">
                    <div class="w-[150px]">{{ formData.pay_way_text }}</div>
                </el-form-item>
                <el-form-item label="提交时间">
                    <div class="w-[150px]">{{ formData.create_time }}</div>
                </el-form-item>
                <el-form-item label="支付时间">
                    <div class="w-[150px]">{{ formData.pay_time }}</div></el-form-item
                >
                <el-form-item label="退款状态">
                    <div class="w-[150px]">{{ formData.refund_status_text }}</div>
                </el-form-item>
                <el-form-item label="退款流水号" v-if="formData.refund_status">
                    <div class="w-[150px]">
                        {{ formData.refund_sn }}
                    </div>
                </el-form-item>
                <el-table size="large" :data="[formData]">
                    <el-table-column label="套餐名称" prop="name" min-width="140" />
                    <el-table-column label="套餐内容" min-width="180">
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
                    <el-table-column label="实付金额" min-width="100">
                        <template #default="{ row }">
                            <div>¥ {{ row.order_amount }}</div>
                        </template>
                    </el-table-column>
                </el-table>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import Popup from '@/components/popup/index.vue'
import feedback from '@/utils/feedback'
import { getrechargeDetail } from '@/api/order/recharge'
import { RechargeOrderRefund } from '@/api/order/recharge'

//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const id = ref('')
const emits = defineEmits(['refresh'])

//表单数据
const formData: any = ref({
    sn: '', //订单编号
    terminal_text: '', //订单来源
    user: {}, //用户信息
    order_type_text: '', //订单类型
    number: '', //充值条数
    order_amount: '', //实付金额
    pay_status_text: '', //支付状态
    pay_way_text: '', //支付方式
    create_time: '', //提交时间
    pay_time_text: '', //支付时间
    refund_status_text: '', //退款状态
    refund_transaction_id: '' //退款流水号
})

//打开弹框
const open = async (value: any) => {
    id.value = value
    await popupRef.value?.open()
    await getData(value)
}

//点击确定按钮
const clickConfirm = async () => {
    await feedback.confirm('是否确认退款')
    await RechargeOrderRefund({ id: id.value })
    await emits('refresh')
    popupRef.value?.close()
}

const getData = async (id: any) => {
    formData.value = await getrechargeDetail({ id })
}

defineExpose({
    open
})
</script>
