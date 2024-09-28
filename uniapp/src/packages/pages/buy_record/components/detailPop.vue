<template>
    <view>
        <view @click="popShow = true">
            <slot></slot>
        </view>
        <u-popup v-model="popShow" mode="center" border-radius="14">
            <view class="w-[90vw]">
                <view
                    class="border border-solid border-b border-[#f3f3f3] py-[26rpx] text-xl font-bold text-center"
                    >{{ type == 1 ? detailData?.name : '订单详情' }}</view>
            </view>
            <view class="p-[30rpx]">
                <!--        如果是充值套餐就显示        -->
                <view class="flex py-[20rpx]" v-if="type == 1">
                    <view class="text-#555555">套餐内容</view>
                    <view class="ml-2 text-[#333333]">
                        <view class="flex">
                            <view
                                >{{ appStore.getTokenUnit }}数量：{{
                                    detailData?.chat_balance
                                }}</view
                            ><view
                                class="text-error"
                                v-if="detailData?.give_chat_balance"
                                >（赠送{{ appStore.getTokenUnit }}
                                {{ detailData?.give_chat_balance }}）</view
                            >
                        </view>
                        <view class="flex">
                            <view
                                >智能体数量：{{
                                    detailData?.robot_number
                                }}</view
                            >
                            <view
                                class="text-error"
                                v-if="detailData?.give_robot_number"
                                >（赠送智能体
                                {{ detailData?.give_chat_balance }}）</view
                            >
                        </view>
                    </view>
                </view>
                <view class="flex py-[20rpx] items-center" v-if="detailData?.order_sn">
                    <view class="text-#555555">订单编号</view>
                    <view class="ml-2 text-[#333333]">
                        {{ detailData?.order_sn || '-' }}
                    </view>
                </view>
                <!--        如果是开通会员就显示        -->
                <template v-if="type == 0">
                    <view class="flex py-[20rpx] items-center">
                        <view class="text-#555555">会员等级</view>
                        <view class="ml-2 text-[#333333]">
                            {{ detailData?.package_name  }}
                        </view>
                    </view>
                    <view class="flex py-[20rpx] items-center">
                        <view class="text-#555555">会员时长</view>
                        <view class="ml-2 text-[#333333]">
                            {{ detailData?.original_package_long_time }}
                        </view>
                    </view>
                   <view class="flex py-[20rpx] items-center">
                       <view class="text-#555555">会员有效期至</view>
                       <view class="ml-2 text-[#333333]">
                           {{ detailData?.package_long_time }}
                       </view>
                   </view>
                </template>
                <view class="flex py-[20rpx] items-center" v-if="detailData?.channel_text">
                    <view class="text-#555555">购买方式</view>
                    <view class="ml-2 text-[#333333]">
                        {{ detailData?.channel_text || '-' }}
                    </view>
                </view>
                <view class="flex py-[20rpx] items-center" v-if="detailData?.pay_way_text">
                    <view class="text-#555555">支付方式</view>
                    <view class="ml-2 text-[#333333]">
                        {{ detailData?.pay_way_text }}
                    </view>
                </view>
                <view class="flex py-[20rpx] items-center" v-if="detailData?.order_amount">
                    <view class="text-#555555">实付金额</view>
                    <view class="ml-2 text-[#333333]">
                        {{ detailData?.order_amount || '-' }}
                    </view>
                </view>
                <view class="flex py-[20rpx] items-center" v-if="detailData?.pay_status_text">
                    <view class="text-#555555">支付状态</view>
                    <view class="ml-2 text-[#333333]">
                        {{ detailData?.pay_status_text || '-' }}
                    </view>
                </view>
                <view class="flex py-[20rpx] items-center" v-if="type == 1">
                    <view class="text-#555555">{{ detailData?.pay_time != '-' ? '支付时间' : '兑换时间' }}</view>
                    <view class="ml-2 text-[#333333]">
                        {{ detailData.pay_time == '-' ? detailData.create_time : detailData.pay_time }}
                    </view>
                </view>
                <view class="flex py-[20rpx] items-center" v-else>
                    <view class="text-#555555" v-if="detailData.channel == 2 && detailData.channel_text != '系统调整'">兑换时间</view>
                    <view class="text-#555555" v-else>{{ detailData?.pay_time_text ? '支付时间' : '调整时间' }}</view>
                    <view class="ml-2 text-[#333333]">
                        {{ detailData?.pay_time_text || detailData?.create_time }}
                    </view>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { ref } from 'vue'

const  appStore = useAppStore()

const prop = defineProps({
    detailData: {
        type: Object,
        default: {} as any
    },
    type: {
        type: Number
    }
})

const popShow = ref(false)
</script>

<style lang="scss" scoped></style>
