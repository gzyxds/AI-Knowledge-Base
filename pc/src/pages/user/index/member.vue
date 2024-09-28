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
                    <div class="font-medium text-[18px] text-[#0256FF]">
                        {{ userStore.userInfo.package_name || '-' }}
                    </div>
                    <div class="mt-[4px] text-[16px]">当前等级</div>
                </div>
                <div class="flex flex-col items-end justify-center pr-[30px]">
                    <div class="flex flex-col items-center">
                        <div class="font-medium text-[18px] text-[#0256FF]">
                            {{ userStore.userInfo.package_time || '-' }}
                            <span v-if="userStore.userInfo?.package_is_overdue"
                                >(已到期)</span
                            >
                        </div>
                        <div class="mt-[4px] text-[16px]">有效期</div>
                    </div>
                </div>
            </div>
            <!--            v-if="appStore.getIsShowMember"-->
            <div
                v-if="appStore.getIsShowMember"
                class="h-full bg-[#EEF2FF] mt-[16px] rounded-[12px] dark:bg-body"
            >
                <!--      套餐切换      -->
                <div class="flex pt-4 ml-4 flex-none">
                    <el-segmented
                        v-model="memberActiveIndex"
                        :block="false"
                        class="segmented !p-[8px] h-[70px] !rounded-[12px] !bg-white dark:!bg-page"
                        :options="
                            memberLists.map((item, i) => ({
                                name: item.name,
                                value: i,
                                desc: item.describe
                            }))
                        "
                    >
                        <template #default="{ item, index }">
                            <div class="flex flex-col items-center gap-1 p-2">
                                <div class="text-xl">{{ item.name }}</div>
                                <div
                                    class="text-xs text-tx-secondary tabs-active_desc"
                                >
                                    {{ item.desc }}
                                </div>
                            </div>
                        </template>
                    </el-segmented>
                </div>
                <div
                    class="px-[26px] pb-[120px] bg-[#EEF2FF] dark:bg-[#1D2025]"
                >
                    <div class="flex flex-col mt-4">
                        <!--    套餐    -->
                        <div class="text-2xl font-medium">选择套餐</div>
                        <!--    套餐    -->
                        <div class="member-lists flex flex-wrap">
                            <div
                                v-for="(item, index) in memberPackageActiveData"
                                :key="item.id"
                                class="member-item relative"
                                :class="{
                                    active: index === memberPackageActiveIndex
                                }"
                                @click="memberPackageActiveIndex = index"
                            >
                                <div
                                    v-if="item.tags != ''"
                                    class="absolute top-[-8px] left-[-1px] bg-[#FF7272] px-[12px] py-[2px] text-white rounded-tl-[10px] rounded-br-[10px] text-xs"
                                >
                                    {{ item.tags }}
                                </div>
                                <div>
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="text-xl font-medium mb-[10px] line-clamp-1"
                                        >
                                            {{
                                                memberDurationMap[
                                                    item.duration_type
                                                ]
                                                    ? item.duration +
                                                      memberDurationMap[
                                                          item.duration_type
                                                      ]
                                                    : '永久'
                                            }}
                                        </div>
                                        <Price
                                            :content="item.sell_price"
                                            main-size="28px"
                                            minor-size="16px"
                                        />
                                        <div
                                            :class="{
                                                'opacity-0':
                                                    item.lineation_price ===
                                                    '0.00'
                                            }"
                                            class="mb-[20px]"
                                        >
                                            <Price
                                                prefix="原价"
                                                :content="item.lineation_price"
                                                main-size="14px"
                                                line-through
                                                color="#999"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="border-t border-solid border-br-light pt-[10px]"
                                    >
                                        <div class="font-medium text-xl pt-1">
                                            会员权益
                                        </div>
                                        <div
                                            class="text-base py-[8px]"
                                            v-for="(
                                                bItem, index
                                            ) in memberActiveData.benefits_list"
                                            :key="index"
                                        >
                                            <div class="flex justify-between">
                                                <div class="flex flex-1">
                                                    <img
                                                        :src="bItem.image"
                                                        class="w-[18px] h-[18px]"
                                                    />
                                                    <span
                                                        class="text-tx-primary ml-[10px] line-clamp-1"
                                                        >{{ bItem.name }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="text-primary flex-none ml-2"
                                                >
                                                    {{ bItem.describe }}
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            v-if="item.is_give"
                                            class="text-base mt-2 p-[8px] bg-[#f7f7f7] rounded-[12px] dark:!bg-[#2d2d2d]"
                                            :style="{
                                                background:
                                                    index ===
                                                    memberPackageActiveIndex
                                                        ? 'linear-gradient(90deg, rgba(179, 217, 242, 0.5) 0%, rgba(159, 181, 249, 0.5) 100%)'
                                                        : '#f7f7f7'
                                            }"
                                        >
                                            <div
                                                class="pb-[8px]"
                                                v-if="item.give_balance != 0"
                                            >
                                                <span
                                                    >赠送{{
                                                        appStore.getTokenUnit
                                                    }}：</span
                                                >
                                                <span>{{
                                                    item.give_balance
                                                }}</span>
                                            </div>
                                            <div v-if="item.give_robot != 0">
                                                <span>赠送智能体：</span>
                                                <span>{{
                                                    item.give_robot
                                                }}</span>
                                            </div>
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
            v-if="appStore.getIsShowMember"
            class="absolute left-0 bottom-0 w-full px-[26px]"
        >
            <!--    支付    -->
            <div
                class="mt-[40px] flex justify-between rounded-[12px] bg-white py-[15px] px-[20px] dark:bg-page"
            >
                <div>
                    实付金额：
                    <Price
                        :content="
                            memberPackageActiveData[memberPackageActiveIndex]
                                ?.sell_price
                        "
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
import { memberPackageList, memberPackageBuy } from '~/api/member'
import { ClientEnum } from '~/enums/appEnums'
import wechatoa from '~/utils/wechat'
import { prepay } from '~/api/pay'
import { pay, PayWayEnum } from '~/utils/pay'

const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()
const runtimeConfig = useRuntimeConfig()

const { data: memberLists } = await useAsyncData(() => memberPackageList(), {
    default() {
        return []
    },
    lazy: true
})

const a = ref('')

// 支付方式
const payWay = ref<number>(PayWayEnum.WECHAT)
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
    return memberLists.value[memberActiveIndex.value]
})

const memberPackageActiveData = computed(() => {
    if (
        memberPackageActiveIndex.value === -1 &&
        memberActiveData.value?.price_list?.length
    ) {
        const i =
            memberActiveData.value?.price_list.findIndex(
                (item: { is_recommend: number }) => item.is_recommend
            ) || 0
        memberPackageActiveIndex.value = i !== -1 ? i : 0
    }
    return memberActiveData.value?.price_list || []
})

const { lockFn: payNow, isLock } = useLockFn(async () => {
    if (!memberPackageActiveData.value[memberPackageActiveIndex.value].id) {
        feedback.msgError('请选择会员套餐')
    }
    if (!payWay.value) {
        feedback.msgError('请选择支付方式')
    }
    const orderInfo = await memberPackageBuy({
        member_price_id:
            memberPackageActiveData.value[memberPackageActiveIndex.value].id
    })
    const payInfo = await prepay({
        ...orderInfo,
        pay_way: payWay.value,
        redirect: `${runtimeConfig.app.baseURL}user/record?type=member`,
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
            type: 'member'
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
.segmented {
    :deep(.el-segmented__item) {
        border-radius: 12px;
        margin-right: 15px;
    }
    :deep(.is-selected) .tabs-active_desc {
        color: white;
    }

    :deep(.el-segmented__item-selected) {
        border-radius: 12px;
        background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
    }
}

.scrollbar {
    :deep(.el-scrollbar__view) {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
    }
}

.member-lists {
    margin: 10px -15px;

    .member-item {
        width: 330px;
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
