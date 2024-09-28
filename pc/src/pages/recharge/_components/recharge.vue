<template>
    <div class="bg-white">
        <div class="text-tx-primary sm:py-[40px] sm:px-[60px] p-[20px]">
            <div class="py-4 px-5 rounded-[16px] bg-[#FFF5ED] mb-5">
                <div class="font-medium">我的账户</div>
                <div class="flex mt-4">
                    <div class="flex-1">
                        剩余{{ appStore.getTokenUnit }}：{{
                            accountData.balance
                        }}
                    </div>
                    <div class="flex-1">
                        智能体数量：{{ accountData.robot_num }}
                    </div>
                    <!-- <div class="flex-1">形象时长：{{ accountData.video_num }}</div> -->
                </div>
            </div>
            <div class="recharge-lists flex flex-wrap">
                <div
                    v-for="(item, index) in rechargeLists"
                    :key="item.id"
                    class="recharge-item relative"
                    :class="{
                        active: index === currentIndex
                    }"
                    @click="currentIndex = index"
                >
                    <div
                        v-if="item.tags != ''"
                        class="absolute top-[-1px] left-[-1px] bg-[#FF7021] px-[12px] py-[2px] text-white rounded-tl-[15px] rounded-br-[15px]"
                    >
                        {{ item.tags }}
                    </div>
                    <div>
                        <div>
                            <div
                                class="text-xl font-medium mb-[10px] line-clamp-1"
                            >
                                {{ item.name }}
                            </div>
                            <Price
                                :content="item.sell_price"
                                main-size="28px"
                                minor-size="16px"
                            />
                            <div
                                :class="{
                                    'opacity-0': item.line_price === '0.00'
                                }"
                                class="mb-[20px]"
                            >
                                <Price
                                    prefix="原价"
                                    :content="item.line_price"
                                    main-size="14px"
                                    line-through
                                    color="#999"
                                />
                            </div>
                        </div>

                        <div
                            class="border-t border-solid border-br-light pt-[10px]"
                        >
                            <div
                                class="text-sm flex py-[8px]"
                                v-for="(bItem, index) in packageBenefits"
                                :key="index"
                            >
                                <span class="text-tx-secondary mr-auto">{{
                                    bItem.label
                                }}</span>
                                <div class="relative">
                                    <span v-if="item[bItem.key] > 0">
                                        {{ Number(item[bItem.key])
                                        }}{{ bItem.unit }}
                                    </span>
                                    <span v-else>-</span>
                                    <div
                                        v-if="
                                            item.is_give &&
                                            Number(item[bItem.giveKey])
                                        "
                                        class="absolute right-0 top-0 translate-y-[-100%] translate-x-[100%] bg-[#FF7021] px-[8px] py-[1px] rounded-r-full rounded-tl-full whitespace-nowrap text-white text-xs"
                                    >
                                        赠{{ item[bItem.giveKey] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-[30px]">
                <div class="text-lg mb-[5px]">支付方式</div>
                <PaymentSelect v-model="payWay" from="recharge" />
            </div>
            <div class="mt-[20px] flex">
                <ElButton
                    type="primary"
                    size="large"
                    :loading="isLock"
                    @click="payNow"
                    style="
                        background: linear-gradient(
                            90deg,
                            #ffcb58 0%,
                            #f7630e 100%
                        );
                        border: none;
                        padding: 0 54px;
                    "
                >
                    立即购买
                </ElButton>
                <div class="ml-[20px]">
                    实付金额：
                    <Price
                        :content="currentPackage.sell_price"
                        main-size="24px"
                        minor-size="14px"
                        color="#FF7021"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { getRechargeLists, rechargePlace } from '@/api/recharge'
import { PayWayEnum, pay } from '@/utils/pay'
import { prepay } from '@/api/pay'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '@/stores/app'
import wechatoa from '@/utils/wechat'
import { ClientEnum } from '@/enums/appEnums'

const userStore = useUserStore()
const appStore = useAppStore()
const router = useRouter()
const rechargeLists = ref<any[]>([])
const accountData = ref({
    balance: '',
    robot_num: '',
    video_num: ''
})

const packageBenefits = computed(() => [
    {
        key: 'chat_balance',
        giveKey: 'give_chat_balance',
        label: `${appStore.getTokenUnit}数量`,
        unit: appStore.getTokenUnit
    },
    {
        key: 'robot_number',
        giveKey: 'give_robot_number',
        label: '智能体个数',
        unit: '个'
    }
    // {
    //   key: 'video_duration',
    //   giveKey: 'give_video_duration',
    //   label: '形象合成时长',
    //   unit: '分钟'
    // }
])

const currentIndex = ref(0)
const getData = async () => {
    const data = await getRechargeLists()
    rechargeLists.value = data.lists
    accountData.value = data.extend
    const selectIndex = rechargeLists.value.findIndex(
        (item) => item.is_recommend
    )
    currentIndex.value = selectIndex === -1 ? 0 : selectIndex
}
const runtimeConfig = useRuntimeConfig()
const payWay = ref(PayWayEnum.WECHAT)
const currentPackage = computed(() => {
    return rechargeLists.value[currentIndex.value] || {}
})

const { lockFn: payNow, isLock } = useLockFn(async () => {
    if (!currentPackage.value.id) {
        feedback.msgError('请选择充值套餐')
    }
    if (!payWay.value) {
        feedback.msgError('请选择支付方式')
    }
    const orderInfo = await rechargePlace({
        package_id: currentPackage.value.id,
        pay_way: payWay.value
    })
    const payInfo = await prepay({
        ...orderInfo,
        pay_way: payWay.value,
        redirect: `${runtimeConfig.app.baseURL}user/record`,
        code: wechatoa.getAuthData().code
    })
    await pay.run({
        payWay: payWay.value,
        orderId: orderInfo.order_id,
        from: orderInfo.from,
        config: payInfo.config
    })

    await feedback.alertSuccess('支付成功')
    userStore.getUser()
    router.push({
        path: '/user/record',
        query: {
            id: orderInfo.order_id
        }
    })
})
getData()

onMounted(async () => {
    if (
        getClient() == ClientEnum.WEIXIN_OA &&
        wechatoa.getAuthData().code == ''
    ) {
        await wechatoa.getUrl()
    }
})
</script>

<style lang="scss" scoped>
.recharge-lists {
    margin: 0 -15px;

    .recharge-item {
        width: 240px;
        margin: 8px 15px;
        text-align: center;
        padding: 30px 20px;
        border-radius: 16px;
        border: 2px solid #e6e9ed;
        cursor: pointer;
        @apply bg-white;

        &.active {
            border-color: #ff7021;
            background-color: #fff7f3;
        }
    }
}
</style>
