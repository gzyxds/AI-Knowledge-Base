<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status v-if="paySetup.is_open" :status="status">
        <view
            class="task-center pb-[180rpx]"
            v-if="appStore.getSwitchConfig.recharge_status"
        >
            <view class="account-info">
                <view class="text-xl">我的帐号</view>
                <view class="mt-[30rpx] flex">
                    <view class="flex-1 flex flex-col items-center">
                        <view class="text-[36rpx]">
                            {{ rechargeData.extend?.balance }}
                        </view>
                        <view class="text-muted mt-[10rpx]">
                            {{ appStore.getTokenUnit }}数量
                        </view>
                    </view>
                    <view class="flex-1 flex flex-col items-center">
                        <view class="text-[36rpx]">
                            {{ rechargeData.extend?.robot_num }}
                        </view>
                        <view class="text-muted mt-[10rpx]"> 智能体 </view>
                    </view>
                </view>
            </view>
            <view class="mt-[30rpx] text-xl"> 套餐选择 </view>
            <view>
                <view
                    class="recharge-template flex relative items-center !pl-0"
                    v-for="item in rechargeData.lists"
                    :key="item.id"
                    :class="{
                        'recharge-template--active': item.id == currentId
                    }"
                    @click="currentId = item.id"
                >
                    <view
                        style="background: #ff7272"
                        class="text-white rounded-bl-lg absolute top-0 right-0 px-[12rpx] py-[6rpx] text-xs"
                        v-if="item.tags"
                    >
                        <span>{{ item.tags }}</span>
                    </view>
                    <view
                        class="flex flex-col items-center w-[200rpx] px-[20rpx]"
                    >
                        <view>
                            <price
                                :content="item.sell_price"
                                color="#000"
                                fontWeight="500"
                                mainSize="42rpx"
                                minorSize="32rpx"
                            />
                        </view>
                        <view v-if="item.line_price > 0">
                            <price
                                color="#999999"
                                lineThrough
                                :content="item.line_price"
                                mainSize="28rpx"
                                minorSize="28rpx"
                            >
                                <template #prefix>
                                    <div>原价￥</div>
                                </template>
                            </price>
                        </view>
                    </view>
                    <view class="flex-1 min-w-0">
                        <view class="text-xl line-clamp-1 font-medium">
                            {{ item.name }}
                        </view>
                        <view class="mt-[10rpx] flex">
                            <view class="text-sm text-info flex-none">
                                {{ appStore.getTokenUnit }}数量：
                            </view>
                            <view class="text-sm flex flex-wrap items-center">
                                <span class="mr-[10rpx]">
                                    {{ item.chat_balance }}
                                </span>
                            </view>
                        </view>
                        <view class="mt-[10rpx] flex">
                            <view class="text-sm text-info flex-none">
                                智能体个数：
                            </view>
                            <view class="text-sm flex flex-wrap items-center">
                                <span class="mr-[10rpx]">
                                    {{ item.robot_number }}
                                </span>
                            </view>
                        </view>

                        <view
                            v-if="item.is_give"
                            class="mt-[10rpx] text-primary text-sm"
                        >
                            赠:
                            <text v-if="item['give_chat_balance']">
                                {{ item['give_chat_balance']
                                }}{{ appStore.getTokenUnit }}
                                {{ item['give_robot_number'] ? '，' : '' }}
                            </text>
                            <text v-if="item['give_robot_number']">
                                {{ item['give_robot_number'] }}智能体
                            </text>
                        </view>
                    </view>
                </view>
            </view>

            <view class="bg-white fixed bottom-0 w-full left-0">
                <view class="p-[20rpx]">
                    <view
                        class="w-full rounded-full recharge-btn py-[20rpx] text-center text-white"
                        @click="rechargeLock"
                    >
                        立即支付
                    </view>
                    <router-navigate
                        class="text-center text-info text-sm mt-2"
                        to="/packages/pages/agreement/agreement?type=pay"
                    >
                        支付即视为您同意《会员支付协议》
                    </router-navigate>
                </view>
            </view>
        </view>
        <view v-else class="py-[400rpx]">
            <u-empty text="功能未开启"></u-empty>
        </view>
        <template #error>
            <u-empty text="加载出错～"></u-empty>
        </template>
        <payment
            v-model:show="payState.showPay"
            :order-id="payState.orderId"
            v-model:showCheck="payState.showCheck"
            :from="payState.from"
            redirect="/mobile/packages/pages/recharge/recharge"
            :order-amount="payState.orderAmount"
            @success="handlePaySuccess"
        />
        <u-popup
            v-model="payState.showPaySuccess"
            :safe-area-inset-bottom="true"
            :mask-close-able="false"
            border-radius="14"
            :z-index="899"
            round
            mode="center"
            width="600"
        >
            <view class="pt-[20rpx]">
                <view class="px-[30rpx] py-[40rpx]">
                    <view class="text-success text-center">
                        <u-icon name="checkmark-circle-fill" size="100" />
                    </view>
                    <view class="text-xl font-medium mt-[20rpx] text-center">
                        支付成功
                    </view>
                    <view class="flex mt-[60rpx]">
                        <view class="flex-1">
                            <view
                                class="h-[72rpx] leading-[72rpx] rounded-full bg-page text-center"
                                @click="payState.showPaySuccess = false"
                            >
                                <text>继续充值</text>
                            </view>
                        </view>
                        <view class="flex-1 ml-[20rpx]">
                            <router-navigate
                                class="h-[72rpx] leading-[72rpx] rounded-full bg-primary text-center text-btn-text"
                                to="/pages/index/index"
                                nav-type="reLaunch"
                            >
                                <text>返回首页</text>
                            </router-navigate>
                        </view>
                    </view>
                </view>
            </view>
        </u-popup>
    </page-status>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->

    <tabbar />
</template>

<script setup lang="ts">
import { getRechargeConfig, recharge } from '@/api/recharge'
import { getIosPayConfig } from '@/api/pay'

import { PageStatusEnum } from '@/enums/appEnums'
import { useLockFn } from '@/hooks/useLockFn'

import { onLoad, onPullDownRefresh, onShow } from '@dcloudio/uni-app'

import { reactive, ref } from 'vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
import { useAppStore } from '@/stores/app'

import payment from '@/packages/components/payment/payment.vue'
const appStore = useAppStore()

const status = ref(PageStatusEnum.LOADING)

const rechargeData = ref<any>({
    lists: []
})
const system = uni.getSystemInfoSync()
const paySetup = ref({
    is_open: 1,
    tips: '立即支付'
})
const currentId = ref()

const payState = reactive({
    orderId: '',
    from: 'recharge',
    showPay: false,
    orderAmount: '',
    showCheck: false,
    showPaySuccess: false
})

const { isLock, lockFn: rechargeLock } = useLockFn(async () => {
    uni.showLoading({
        mask: true,
        title: '请稍后...'
    })

    try {
        const data = await recharge({
            package_id: currentId.value
        })
        payState.orderId = data.order_id
        payState.showPay = true
        payState.orderAmount = data.order_amount
    } catch (error) {
    } finally {
        uni.hideLoading()
    }
})

const handlePaySuccess = async () => {
    payState.showPay = false
    payState.showPaySuccess = true
}

const getRechargeConfigData = async () => {
    rechargeData.value = await getRechargeConfig({
        page_type: 0
    })
    currentId.value =
        rechargeData.value.lists.find((item: any) => item.is_recommend)?.id ||
        ''
}

const getData = async () => {
    try {
        await Promise.all([getRechargeConfigData()])
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
}

// 获取支付配置
const getPaySetup = async () => {
    try {
        paySetup.value = await getIosPayConfig()
        console.log(paySetup.value)
        if (paySetup.value.is_open == 0) {
            uni.showModal({
                title: '提示',
                content: paySetup.value.tips,
                showCancel: false,
                success: () => uni.navigateBack()
            })
        }
    } catch (error) {
        console.log('获取支付设置错误=>', error)
    }
}

onShow(async () => {
    getData()

    // #ifdef MP|| APP-PLUS
    if (system.system.indexOf('iOS') !== -1) {
        await getPaySetup()
    }
    // #endif
})

onLoad((options: any) => {
    // h5支付用于弹起手动确认支付弹窗
    if (
        (options?.id || options['amp;id']) &&
        (options.from || options['amp;from'])
    ) {
        payState.orderId = options?.id || options['amp;id']
        payState.from = options.from || options['amp;from']
    }

    if (options?.checkPay) {
        payState.showCheck = true
    }
})

onPullDownRefresh(async () => {
    try {
        await getData()
    } catch (error) {}
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss">
page {
    background-color: #fff;
    min-height: 100%;
}

.task-center {
    padding: 24rpx 20rpx 180rpx 20rpx;

    .account-info {
        padding: 30rpx;
        background: url('../../static/images/robot.png'), #cee3fb;
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.1);
        border-radius: 16rpx;
        background-size: contain;
        background-position: center center;
        background-repeat: no-repeat;
    }
    .recharge-template {
        padding: 30rpx;
        border-radius: 16rpx;
        background: #ffffff;
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.1);
        margin-top: 30rpx;
        overflow: hidden;
        border: 1px solid transparent;
        &--active {
            background: $u-type-primary-light;

            @apply border-primary;
        }
    }
    .recharge-btn {
        background: linear-gradient(90deg, $u-minor-color, $u-type-primary);
    }
}
</style>
