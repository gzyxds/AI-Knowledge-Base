<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status class="h-full" v-if="paySetup.is_open" :status="status">
        <view
            v-if="appStore.config?.member_package_open"
            class="h-full flex flex-col min-h-full"
        >
            <view
                class="member-account-info flex items-center pl-[40rpx] pt-[30rpx]"
            >
                <u-avatar
                    :src="userStore.userInfo.avatar"
                    :size="120"
                ></u-avatar>
                <view class="text-main ml-[30rpx]">
                    <view class="flex items-center">
                        <view
                            class="max-w-[220rpx] text-2xl font-medium line-clamp-1"
                            :style="{
                                maxWidth: userStore.userInfo.package_name
                                    ? '300rpx'
                                    : '100%'
                            }"
                        >
                            {{ userStore.userInfo.nickname }}
                        </view>
                        <view
                            v-if="userStore.userInfo.package_name"
                            class="flex-none bg-[#FFE5CC] ml-[16rpx] text-xs text-[#FF8F1F] rounded-[6rpx] px-[10rpx] py-[6rpx]"
                        >
                            {{ userStore.userInfo.package_name }}
                        </view>
                    </view>
                    <view class="text-xs text-content mt-[18rpx]">
                        有效期至：{{ userStore.userInfo.package_time || '-' }}
                    </view>
                </view>
            </view>

            <view class="p-[30rpx]">
                <u-tabs
                    v-model="memberActiveIndex"
                    :is-scroll="true"
                    bgColor="none"
                    :list="memberLists"
                    :active-color="$theme.primaryColor"
                />
            </view>

            <view class="member-center flex-1 min-h-0">
                <scroll-view scroll-x class="whitespace-nowrap pt-[30rpx]">
                    <view
                        class="member-item inline-flex relative items-center mr-[30rpx]"
                        v-for="(item, index) in memberPackageActiveData"
                        :key="item.id"
                        :class="{
                            '!pt-[60rpx]': item.tags,
                            'member-item--active':
                                index == memberPackageActiveIndex
                        }"
                        @click="memberPackageActiveIndex = index"
                    >
                        <view
                            class="bg-[#FE8484] text-white rounded-tl-lg rounded-r-[5rpx] absolute top-[-1px] left-[-1px] px-[12rpx] py-[6rpx] text-xs"
                            v-if="item.tags"
                        >
                            <span>{{ item.tags }}</span>
                        </view>
                        <view
                            class="flex flex-col items-center w-[200rpx] px-[20rpx]"
                        >
                            <view
                                class="text-base font-medium mb-[10px] line-clamp-1"
                            >
                                {{
                                    memberDurationMap[item.duration_type]
                                        ? item.duration +
                                          memberDurationMap[item.duration_type]
                                        : '永久'
                                }}
                            </view>
                            <view>
                                <price
                                    :content="item.sell_price"
                                    color="#000"
                                    fontWeight="500"
                                    mainSize="45rpx"
                                    minorSize="32rpx"
                                />
                            </view>
                            <view v-if="item.lineation_price > 0">
                                <price
                                    color="#999999"
                                    lineThrough
                                    :content="item.lineation_price"
                                    mainSize="28rpx"
                                    minorSize="28rpx"
                                >
                                </price>
                            </view>
                        </view>
                    </view>
                </scroll-view>

                <view class="">
                    <view class="font-medium text-xl pt-1">会员权益</view>
                    <view
                        class="text-base py-[16rpx]"
                        v-for="(bItem, index) in memberActiveData.benefits_list"
                        :key="index"
                    >
                        <view class="flex justify-between">
                            <view class="flex flex-1">
                                <image
                                    :src="bItem.image"
                                    class="w-[36rpx] h-[36rpx]"
                                />
                                <text
                                    class="text-tx-primary ml-[20rpx] line-clamp-1"
                                    >{{ bItem.name }}</text
                                >
                            </view>
                            <view class="text-primary flex-none ml-2">{{
                                bItem.describe
                            }}</view>
                        </view>
                    </view>
                    <view
                        v-if="memberPackageActiveData[memberPackageActiveIndex]?.is_give"
                        class="text-base mt-4 p-[8px] bg-[#F2F6FA] rounded-xl"
                    >
                        <view
                            class="pb-[8px]"
                            v-if="
                                memberPackageActiveData[
                                    memberPackageActiveIndex
                                ]?.give_balance != 0
                            "
                        >
                            <text>赠送{{ appStore.getTokenUnit }}：</text>
                            <text>{{
                                memberPackageActiveData[
                                    memberPackageActiveIndex
                                ]?.give_balance
                            }}</text>
                        </view>
                        <view
                            v-if="
                                memberPackageActiveData[
                                    memberPackageActiveIndex
                                ]?.give_robot != 0
                            "
                        >
                            <text>赠送智能体：</text>
                            <text>{{
                                memberPackageActiveData[
                                    memberPackageActiveIndex
                                ]?.give_robot
                            }}</text>
                        </view>
                    </view>
                </view>

                <view class="bg-white fixed bottom-0 w-full left-0">
                    <view class="p-[20rpx]">
                        <view
                            class="w-full rounded-full member-center-btn py-[20rpx] text-center text-white"
                            @click="rechargeLock"
                        >
                            立即支付
                        </view>
                        <router-navigate
                            class="text-center text-info text-sm mt-2"
                            to="/packages/pages/agreement/agreement?type=payment"
                        >
                            支付即视为您同意《会员支付协议》
                        </router-navigate>
                    </view>
                </view>
            </view>
        </view>
        <view v-else class="py-[400rpx]">
            <u-empty text="功能未开启"></u-empty>
        </view>
        <template #error>
            <u-empty text="加载出错～"></u-empty>
        </template>

        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->

        <payment
            v-model:show="payState.showPay"
            :order-id="payState.orderId"
            v-model:showCheck="payState.showCheck"
            :from="payState.from"
            redirect="/mobile/packages/pages/member_center/member_center"
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
                                <text>继续</text>
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

    <tabbar />
</template>

<script setup lang="ts">
import { memberPackageList, memberPackageBuy } from '@/api/member'
import { getIosPayConfig } from '@/api/pay'

import { PageStatusEnum } from '@/enums/appEnums'
import { useLockFn } from '@/hooks/useLockFn'

import { onLoad, onPullDownRefresh, onShow } from '@dcloudio/uni-app'

import { computed, nextTick, reactive, ref } from 'vue'
import { useAppStore } from '@/stores/app'
import { useRouter } from 'uniapp-router-next'
import { useUserStore } from '@/stores/user'

import payment from '@/packages/components/payment/payment.vue'
import {isWeixinClient} from "@/utils/client";
const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()

const status = ref(PageStatusEnum.LOADING)

const memberLists = ref<any[]>([])
const system = uni.getSystemInfoSync()

const paySetup = ref({
    is_open: 1,
    tips: '立即支付'
})

const payState = reactive({
    orderId: '',
    from: 'member',
    showPay: false,
    orderAmount: '',
    showCheck: false,
    showPaySuccess: false
})

// 当前会员选择
const memberActiveIndex = ref<number | string>(-1)
// 当前套餐选择
const memberPackageActiveIndex = ref<number | string>(-1)
// 会员时长映射map
const memberDurationMap = { '1': '天', '2': '个月' }

const memberActiveData = computed(() => {
    if (memberActiveIndex.value === -1 && memberLists.value?.length) {
        const i = memberLists.value.findIndex(
            (item: { is_recommend: number }) => item.is_recommend
        )
        memberActiveIndex.value = i !== -1 ? i : 0
    }
    return memberLists.value[memberActiveIndex.value as number] || {}
})

const memberPackageActiveData = computed(() => {
    if (memberActiveData.value?.price_list?.length) {
        const i =
            memberActiveData.value?.price_list.findIndex(
                (item: { is_recommend: number }) => item.is_recommend
            ) || 0
        memberPackageActiveIndex.value = i !== -1 ? i : 0
    }
    return memberActiveData.value?.price_list || []
})

const { isLock, lockFn: rechargeLock } = useLockFn(async () => {
    uni.showLoading({
        mask: true,
        title: '请稍后...'
    })

    try {
        const data = await memberPackageBuy({
            member_price_id:
                memberPackageActiveData.value[memberPackageActiveIndex.value].id
        })
        uni.hideLoading()
        payState.orderId = data.order_id
        payState.showPay = true
        payState.orderAmount = data.order_amount
    } catch (error) {
        setTimeout(() => uni.hideLoading(), 1000)
    }
})

const handlePaySuccess = async () => {
    payState.showPay = false
    payState.showPaySuccess = true
    userStore.getUser()
}

const memberPackageListData = async () => {
    const data = await memberPackageList()
    memberLists.value = []
    // 修复 tabs计算错位的问题
    setTimeout(() => {
        memberLists.value = data
    }, 250)
}

const getData = async () => {
    try {
        await Promise.all([memberPackageListData()])
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

const back = () => {
    router.navigateBack()
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
    // try {
    //     await getData()
    // } catch (error) {}
    // uni.stopPullDownRefresh()
})
</script>

<style lang="scss">
page {
    background-color: #f5f8fd;
    height: 100%;
    min-height: 100%;
}

.member-account-info {
    background: url('../../static/images/member_bg.png');
    background-size: contain;
    background-position: right 40rpx;
    background-repeat: no-repeat;
}

.member-center {
    padding: 0 20rpx;
    padding-bottom: 200rpx;
    overflow-y: auto;
    border-radius: 20rpx 20rpx 0 0;
    background-color: #ffffff;

    .member-item {
        width: 244rpx;
        height: 244rpx;
        padding: 30rpx;
        border-radius: 16rpx;
        background: #f4f4f4;
        margin-bottom: 30rpx;
        border: 1px solid #f4f4f4;
        &--active {
            background: $u-type-primary-light;
            @apply border-primary;
        }
    }
    .member-center-btn {
        background: linear-gradient(90deg, $u-minor-color, $u-type-primary);
    }
}
</style>
