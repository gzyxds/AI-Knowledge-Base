<template>
    <view
        class="  flex px-[50rpx] justify-between pt-[50rpx] pb-[140rpx] "
    >
        <view class="flex items-center relative w-full">
            <u-image
                width="120"
                height="120"
                :src="userInfo.avatar"
                borderRadius="50%"
            />
            <view class="text-btn-text ml-[20rpx] flex-1">
                <view class="text-xl text-main w-[400rpx]">{{ userInfo.nickname }}</view>
                <view class="text-xs text-muted mt-[18rpx]">
                    我的邀请人：{{ userInfo.first_leader_nickname || '系统' }}
                </view>
            </view>

            <router-navigate to="/packages/pages/invite_poster/invite_poster">
                <view class="absolute right-[-50rpx] top-[-20rpx] flex items-center bg-[#FFE5CC] rounded-l-full py-[10rpx] px-[20rpx]">
                    <u-icon name="share" color="#FF8F1F" size="28"></u-icon>
                    <text class="text-[#FF8F1F] text-base ml-[10rpx]">分享</text>
                </view>
            </router-navigate>
        </view>
    </view>
    <view class="m-[20rpx] px-[30rpx] pb-[30rpx] pt-[20rpx] bg-white rounded-lg mt-[-100rpx]">
        <view class="mt-[20rpx]">
            <view class="flex">
                <view class="w-full">
                    <view class="text-muted text-base">可提现收入(元)</view>
                    <view class="text-primary text-[45rpx] font-medium mt-[10rpx]">
                        {{ userInfo.user_money }}
                    </view>
                </view>

                <view class="flex-none">
                    <view class="text-muted text-base">今日收入(元)</view>
                    <view class="text-primary text-[45rpx] font-medium mt-[10rpx]">
                        {{ userInfo.today_money }}
                    </view>
                </view>
            </view>

            <view class="flex mt-3">
                <view class="w-full">
                    <view class="text-muted text-base">已提现(元)</view>
                    <view class="text-primary text-[45rpx] font-medium mt-[10rpx]">
                        {{ userInfo.withdrawn_money }}
                    </view>
                </view>

                <view class="flex-none">
                    <view class="text-muted text-base">累计收入(元)</view>
                    <view class="text-primary text-[45rpx] font-medium mt-[10rpx]">
                        {{ userInfo.total_user_money }}
                    </view>
                </view>
            </view>
        </view>
        <view class="mt-4 w-full">
            <view class="entry-btn" @click="toWithDraw">立即提现</view>
        </view>
    </view>
    <view class="m-[20rpx] p-[30rpx] bg-white rounded-lg">
        <view class="flex">
            <view class="w-full">
                <router-navigate to="/packages/pages/distribution_order/distribution_order">
                    <view class="flex items-center">
                        <u-image
                            width="64"
                            height="64"
                            :src="distribution_orders"
                        />
                        <view class="ml-[20rpx]">
                            <view class="text-main text-base">分销订单</view>
                            <view class="text-muted text-sm mt-[15rpx]">{{ userInfo.distribution_order_num || 0 }}笔</view>
                        </view>
                    </view>
                </router-navigate>
            </view>
            <view class="flex-none w-[240rpx]">
                <router-navigate to="/packages/pages/earnings_detail/earnings_detail">
                    <view class="flex items-center">
                        <u-image
                            width="64"
                            height="64"
                            :src="commission_detail"
                        />
                        <view class="ml-[20rpx]">
                            <view class="text-main text-base">佣金明细</view>
                            <view class="text-muted text-sm mt-[15rpx]">收入/支出</view>
                        </view>
                    </view>
                </router-navigate>
            </view>
        </view>


        <view class="flex mt-[50rpx]">
            <view class="w-full">
                <router-navigate to="/packages/pages/team_fans/team_fans">
                    <view class="flex items-center">
                        <u-image
                            width="64"
                            height="64"
                            :src="team_fans"
                        />
                        <view class="ml-[20rpx]">
                            <view class="text-main text-base">我的团队</view>
                            <view class="text-muted text-sm mt-[15rpx]">{{ userInfo.below_num || 0 }}人</view>
                        </view>
                    </view>
                </router-navigate>
            </view>
            <view class="flex-none w-[240rpx]">
                <view class="flex justify-center items-center" @click="showRule = true">
                    <u-image
                        width="64"
                        height="64"
                        :src="distribution_rule"
                    />
                    <view class="ml-[20rpx]">
                        <view class="text-main text-base">分销规则</view>
                        <view class="text-muted text-sm mt-[15rpx]">分销规则明细</view>
                    </view>
                </view>
            </view>
        </view>

        <view class="flex  mt-[50rpx]">
            <view class="w-full">
                <router-navigate to="/packages/pages/invite_poster/invite_poster">
                    <view class="flex items-center">
                        <u-image
                            width="64"
                            height="64"
                            :src="invite_friends"
                        />
                        <view class="ml-[20rpx]">
                            <view class="text-main text-base">分享</view>
                            <view class="text-muted text-sm mt-[15rpx]">邀请好友加入</view>
                        </view>
                    </view>
                </router-navigate>
            </view>
            <view class="flex-none w-[240rpx]">
                <router-navigate to="/packages/pages/withdraw_record/withdraw_record">
                    <view class="flex items-center">
                        <u-image
                            width="64"
                            height="64"
                            :src="withdraw_record"
                        />
                        <view class="ml-[20rpx]">
                            <view class="text-main text-base">提现记录</view>
                            <view class="text-muted text-sm mt-[15rpx]">记录明细</view>
                        </view>
                    </view>
                </router-navigate>
            </view>
        </view>
    </view>

    <u-popup
        v-model="showRule"
        mode="center"
        :custom-style="{
            backgroundColor: 'transparent',
        }"
    >
        <view class="h-[720rpx] pt-[60px]">
            <view class="w-[690rpx] p-[30rpx] rounded-[20rpx] bg-white">
                <view class="text-xl font-medium">分销规则</view>

                <view class="bg-[#F4F8FD] rounded-md mt-4 py-[40rpx] px-[30rpx]">
                    <view class="flex items-center">
                        <u-image
                            width="64"
                            height="64"
                            :src="group1"
                        />
                        <view class="ml-[30rpx]">
                            <view class="text-main text-md">
                                一级分销 <text class="text-primary">（分佣{{ config?.first_ratio }}%）</text>
                            </view>
                            <view class="text-muted text-sm mt-[25rpx]">
                                成为分销商，下级消费您获得
                                {{ config?.first_ratio }}%奖励
                            </view>
                        </view>
                    </view>

                    <view class="flex items-center mt-[40rpx]">
                        <u-image
                            width="64"
                            height="64"
                            :src="group2"
                        />
                        <view class="ml-[30rpx]">
                            <view class="text-main text-md">
                                二级分销 <text class="text-primary">（分佣{{ config?.second_ratio }}%）</text>
                            </view>
                            <view class="text-muted text-sm mt-[25rpx]">
                                成为分销商，下2级消费您获得
                                {{ config?.second_ratio }}%奖励
                            </view>
                        </view>
                    </view>
                </view>

                <view
                    class="absolute right-[50%] bottom-[0rpx]"
                    style="transform: translateX(50%)"
                >
                    <u-icon
                        name="close-circle"
                        size="60"
                        color="#ffffff"
                        @click="showRule = false"
                    >
                    </u-icon>
                </view>
            </view>
        </view>
    </u-popup>
</template>
<script setup lang="ts">
import {ref} from 'vue'
import router from '@/router'
import group1 from '@/packages/static/images/group_1.png'
import group2 from '@/packages/static/images/group_2.png'
import distribution_orders from '@/packages/static/images/distribution_orders.png'
import invite_friends from '@/packages/static/images/invite_friends.png'
import distribution_rule from '@/packages/static/images/distribution_rule.png'
import team_fans from '@/packages/static/images/team_fans.png'
import withdraw_record from '@/packages/static/images/withdraw_record.png'
import commission_detail from '@/packages/static/images/commission_detail.png'

const props = defineProps<{
    userInfo: Record<string, any>
    config: any
    withdrawConfig: any
}>()

const showRule = ref<boolean>(false)

//去提现
const toWithDraw = () => {
    if (props.withdrawConfig.type.length == 0) {
        uni.$u.toast('后台未设置提现方式！')
        return
    }
    // uni.navigateTo({ url: '/packages/pages/withdraw/withdraw' })
    router.navigateTo('/packages/pages/withdraw/withdraw')
}
</script>

<style lang="scss" scoped>
.entry-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 82rpx;
    border-radius: 30px;
    font-weight: 500;
    font-size: 30rpx;
    background: linear-gradient(90deg, #70c3ec 0%, #426df7 100%);
    box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.2);
    @apply text-white;
}
</style>
