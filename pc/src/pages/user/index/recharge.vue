<template>
    <div class="relative flex flex-col min-h-0 h-full">
        <ElScrollbar class="scrollbar rounded-[12px]">
            <div
                class="grid grid-cols-3 md:grid-cols-3 gap-4 h-[150px] bg-[#EEF2FF] py-[20px] rounded-[12px] flex-none dark:bg-body"
            >
                <div class="flex flex-col pl-[30px] items-start justify-center">
                    <div class="flex flex-col items-center">
                        <img
                            :src="userStore.userInfo.avatar"
                            class="w-[64px] h-[64px] rounded-full"
                        />
                        <div class="mt-[10px] text-sm">
                            用户ID: {{ userStore.userInfo.sn }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <div class="font-medium text-[25px] text-[#0256FF]">
                        {{ accountData.balance }}
                    </div>
                    <div class="mt-[4px]">剩余{{ appStore.getTokenUnit }}</div>
                </div>
                <div class="flex flex-col items-end justify-center pr-[30px]">
                    <div class="flex flex-col items-center">
                        <div class="font-medium text-[25px] text-[#0256FF]">
                            {{ accountData.robot_num }}
                        </div>
                        <div class="mt-[4px]">智能体数量</div>
                    </div>
                </div>
            </div>
            <div
                v-if="appStore.getIsShowRecharge"
                class="bg-[#EEF2FF] mt-[16px] rounded-[12px] flex-1 min-h-0 dark:bg-body"
            >
                <div class="p-[26px] pb-[120px] bg-[#EEF2FF] dark:bg-[#1D2025]">
                    <div class="flex flex-col">
                        <!--    套餐    -->
                        <div class="text-2xl font-medium">选择套餐</div>
                        <!--    套餐    -->
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
                                    class="absolute top-[-8px] left-[-1px] bg-[#FF7272] px-[12px] py-[2px] text-white rounded-tl-[10px] rounded-br-[10px] text-xs"
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
                                                'opacity-0':
                                                    item.line_price === '0.00'
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
                                            class="text-sm py-[8px]"
                                            v-for="(
                                                bItem, index
                                            ) in packageBenefits"
                                            :key="index"
                                        >
                                            <div class="flex">
                                                <span
                                                    class="text-tx-secondary mr-auto"
                                                >
                                                    {{ bItem.label }}
                                                </span>
                                                <div class="relative">
                                                    <span
                                                        v-if="
                                                            item[bItem.key] > 0
                                                        "
                                                    >
                                                        {{
                                                            Number(
                                                                item[bItem.key]
                                                            )
                                                        }}{{ bItem.unit }}
                                                    </span>
                                                    <span v-else>-</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            v-if="item.is_give"
                                            class="py-[8px] text-primary text-sm"
                                        >
                                            赠:
                                            <span
                                                v-if="item['give_chat_balance']"
                                            >
                                                {{ item['give_chat_balance']
                                                }}{{ appStore.getTokenUnit }}
                                                {{
                                                    item['give_robot_number']
                                                        ? '，'
                                                        : ''
                                                }}
                                            </span>
                                            <span
                                                v-if="item['give_robot_number']"
                                            >
                                                {{
                                                    item['give_robot_number']
                                                }}智能体
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--    支付方式    -->
                    <div class="mt-[10px]">
                        <div class="text-2xl font-medium mb-[5px]">
                            支付方式
                        </div>
                        <PaymentSelect v-model="payWay" from="recharge" />
                    </div>
                </div>
            </div>

            <div
                class="w-full h-full bg-[#EEF2FF] mt-[16px] p-[26px] rounded-[12px] flex items-center justify-center"
                v-else
            >
                <div class="text-xl">功能未开启!</div>
            </div>
        </ElScrollbar>

        <div
            v-if="appStore.getIsShowRecharge"
            class="absolute left-0 bottom-0 w-full px-[26px]"
        >
            <!--    支付    -->
            <div
                class="mt-[40px] flex justify-between rounded-[12px] bg-white py-[15px] px-[20px] dark:bg-page"
            >
                <div>
                    实付金额：
                    <Price
                        :content="currentPackage.sell_price"
                        main-size="24px"
                        minor-size="14px"
                        color="#FF7021"
                    />
                </div>
                <ElButton
                    type="primary"
                    size="large"
                    :loading="isLock"
                    @click="payNow"
                    style="border: none; border-radius: 6px; padding: 0 54px"
                >
                    立即购买
                </ElButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '~/stores/user'
import { useAppStore } from '~/stores/app'
import { getRechargeLists, rechargePlace } from '~/api/recharge'
import { ClientEnum } from '~/enums/appEnums'
import wechatoa from '~/utils/wechat'
import { prepay } from '~/api/pay'
import { pay, PayWayEnum } from '~/utils/pay'

const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()

const rechargeLists = ref<any[]>([])
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
const accountData = ref({
    balance: '',
    robot_num: '',
    video_num: ''
})
// 当前选择
const currentIndex = ref(0)

const runtimeConfig = useRuntimeConfig()
// 支付方式
const payWay = ref(PayWayEnum.WECHAT)

const currentPackage = computed(() => {
    return rechargeLists.value[currentIndex.value] || {}
})

const getData = async () => {
    const data = await getRechargeLists()
    rechargeLists.value = data.lists
    accountData.value = data.extend
    const selectIndex = rechargeLists.value.findIndex(
        (item) => item.is_recommend
    )
    currentIndex.value = selectIndex === -1 ? 0 : selectIndex
}
getData()

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
        redirect: `${runtimeConfig.app.baseURL}user/record?type=recharge`,
        code: wechatoa.getAuthData().code
    })
    await pay.run({
        payWay: payWay.value,
        orderId: orderInfo.order_id,
        from: orderInfo.from,
        config: payInfo.config
    })

    await userStore.getUser()
    await feedback.alertSuccess('支付成功')
    router.push({
        path: '/user/record',
        query: {
            id: orderInfo.order_id,
            type: 'recharge'
        }
    })
})

onMounted(async () => {
    if (
        getClient() == ClientEnum.WEIXIN_OA &&
        wechatoa.getAuthData().code == ''
    ) {
        await wechatoa.getUrl()
    }
})
</script>

<style scoped lang="scss">
.scrollbar {
    :deep(.el-scrollbar__view) {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
    }
}
.recharge-lists {
    margin: 10px -15px;

    .recharge-item {
        width: 210px;
        margin: 8px 15px;
        padding: 30px 20px;
        border-radius: 12px;
        border: 2px solid transparent;
        cursor: pointer;
        @apply bg-white dark:bg-page;

        &.active {
            border-color: #445df4;
            background: linear-gradient(
                90deg,
                rgba(112, 195, 236, 0.2) 0%,
                rgba(66, 109, 247, 0.2) 100%
            );
        }
    }
}
</style>
