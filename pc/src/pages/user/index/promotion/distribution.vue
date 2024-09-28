<template>
    <div class="flex flex-col min-h-0 h-full ">
        <ElScrollbar class="bg-white dark:bg-body rounded-[12px] p-[20px]">
            <div v-if="centerData?.is_open != 1" class="w-full flex-1">
                    <div class="text-xl">推广功能未开启!</div>
            </div>
            <div class="h-full w-full" v-if="centerData?.is_open == 1">

                    <!--     非分销商     -->
                    <div class="grid grid-cols-2 gap-x-[20px] " v-if="!centerData?.user?.is_distribution">
                        <div class="bg-page rounded-[12px] px-[30px] py-[25px]">
                            <div class="font-medium text-xl">分销规则</div>
                            <div class="mt-[23px] flex ">
                                <div
                                    v-if="centerData?.config.level >= 1"
                                    class="flex-1 bg-[#EDF6FE] p-[25px] rounded-[10px]"
                                >
                                    <div class="text-xl text-primary">
                                        一级分销（分佣{{ centerData?.config?.first_ratio }}%）
                                    </div>
                                    <div
                                        class="text-base mt-[12px] text-[#9E9E9E]"
                                    >
                                        成为分销商，下级消费您获得
                                        {{ centerData?.config?.first_ratio }}%奖励
                                    </div>
                                </div>
                                <div
                                    v-if="centerData?.config.level == 2"
                                    class="flex-1 bg-[#FFEAD5] p-[25px] rounded-[10px] ml-[20px]"
                                >
                                    <div class="text-xl text-[#FF8F1F]">
                                        二级分销（分佣{{ centerData?.config?.second_ratio }}%）
                                    </div>
                                    <div
                                        class="text-base mt-[12px] text-[#9E9E9E]"
                                    >
                                        成为分销商，下2级消费您获得
                                        {{ centerData?.config?.second_ratio }}%奖励
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col min-h-0 bg-page rounded-[12px] px-[30px] py-[25px]">
                            <div class="font-medium text-xl">申请分销</div>
                            <div
                                class="flex justify-between items-center h-full mt-[23px] p-[25px] rounded-[10px] bg-cover"
                                :style="{ 'background-image': `url(${ applyBg })` }"
                            >
                                <div class="text-black">您当前还不是分销商，请申请成为分销商</div>
                                <ElButton type="primary" @click="openApplyPop">申请成为分销商</ElButton>
                            </div>
                        </div>
                    </div>

                    <!--     分销商     -->
                    <div v-else>

                        <div class="grid grid-cols-2 xl:grid-cols-4 gap-x-[20px] gap-y-[20px]">
                            <div class="bg-page rounded-[12px] px-[30px] py-[25px]">
                                <div class="font-medium text-xl pt-[20px] pb-[15px]">可提现余额（元）</div>
                                <div
                                    class="flex justify-between items-center"
                                >
                                    <div
                                        class="text-primary text-[30px] font-medium"
                                    >
                                        {{ centerData?.user?.user_money }}
                                    </div>
                                    <ElButton
                                        type="primary"
                                        class="!border-none"
                                        style="--el-button-bg-color: #4A92FF"
                                        @click="openWithdrawPop"
                                    >
                                        去提现
                                    </ElButton>
                                </div>
                            </div>
                            <div class="bg-page rounded-[12px] px-[30px] py-[25px]">
                                <div class="font-medium text-xl pt-[20px] pb-[15px]">今日收益（元）</div>
                                <div
                                    class="flex justify-between items-center"
                                >
                                    <div
                                        class="text-primary text-[30px] font-medium"
                                    >
                                        {{ centerData?.user?.today_money }}
                                    </div>
                                </div>
                            </div>
                            <div class="bg-page rounded-[12px] px-[30px] py-[25px]">
                                <div class="font-medium text-xl pt-[20px] pb-[15px]">已提现（元）</div>
                                <div
                                    class="flex justify-between items-center"
                                >
                                    <div
                                        class="text-primary text-[30px] font-medium"
                                    >
                                        {{ centerData?.user?.withdrawn_money }}
                                    </div>
                                    <ElButton
                                        type="warning"
                                        class="!border-none"
                                        style="--el-button-bg-color: #FFA64C"
                                        @click="withdrawRecordPopRef.open()"
                                    >
                                        提现记录
                                    </ElButton>
                                </div>
                            </div>
                            <div class="bg-page rounded-[12px] px-[30px] py-[25px]">
                                <div class="font-medium text-xl pt-[20px] pb-[15px]">累计收益（元）</div>
                                <div
                                    class="flex justify-between items-center"
                                >
                                    <div
                                        class="text-primary text-[30px] font-medium"
                                    >
                                        {{ centerData?.user?.total_user_money }}
                                    </div>
                                    <ElButton
                                        type="warning"
                                        class="!border-none"
                                        style="--el-button-bg-color: #F5BE0A"
                                        @click="openIncomePop"
                                    >
                                        收益明细
                                    </ElButton>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-[20px] mt-4">
                            <div class="w-full bg-page rounded-[12px] px-[30px] py-[25px]">
                                <div class="font-medium text-xl">分销规则</div>
                                <div class="mt-[23px] flex">
                                    <div
                                        v-if="centerData?.config.level >= 1"
                                        class="flex-1 bg-[#EDF6FE] p-[25px] rounded-[10px]"
                                    >
                                        <div class="text-xl text-primary">
                                            一级分销（分佣{{ centerData?.config?.first_ratio }}%）
                                        </div>
                                        <div
                                            class="text-base mt-[12px] text-[#9E9E9E]"
                                        >
                                            成为分销商，下级消费您获得
                                            {{ centerData?.config?.first_ratio }}%奖励
                                        </div>
                                    </div>
                                    <div
                                        v-if="centerData?.config.level == 2"
                                        class="flex-1 bg-[#FFEAD5] p-[25px] rounded-[10px] ml-[20px]"
                                    >
                                        <div class="text-xl text-[#FF8F1F]">
                                            二级分销（分佣{{ centerData?.config?.second_ratio }}%）
                                        </div>
                                        <div
                                            class="text-base mt-[12px] text-[#9E9E9E]"
                                        >
                                            成为分销商，下2级消费您获得
                                            {{ centerData?.config?.second_ratio }}%奖励
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col min-h-0 w-full bg-page rounded-[12px] px-[30px] py-[25px]">
                                <div class="font-medium text-xl">推广方式</div>
                                <div class="grid grid-cols-2 gap-x-[20px] h-full">
                                    <div
                                        class="flex flex-col mt-[23px] p-[25px] rounded-[10px] bg-cover relative"
                                        :style="{ 'background-image': `url(${ linkBg })` }"
                                    >
                                        <div class="text-xl text-[#333333]">邀请链接</div>
                                        <div
                                            class="line-clamp-2 text-[#9E9E9E] mt-[12px]"
                                            style="word-break: break-all;"
                                        >
                                            {{ centerData?.config?.pc_promotion_url }}
                                        </div>
                                        <div
                                            class="absolute top-[15px] right-[15px]"
                                        >
                                            <ElButton
                                                type="primary"
                                                size="small"
                                                class="!border-none"
                                                style="--el-button-bg-color: #4A92FF"
                                                @click="copyLink"
                                            >
                                                复制
                                            </ElButton>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-col mt-[23px] p-[25px] rounded-[10px] bg-cover relative"
                                        :style="{ 'background-image': `url(${ posterBg })` }"
                                    >
                                        <div class="text-xl text-[#333333]">分享海报</div>
                                        <div class="text-[#9E9E9E] mt-[12px]">
                                            扫描二维码，一起加入吧！
                                        </div>
                                        <div
                                            class="absolute top-[15px] right-[15px]"
                                        >

                                            <Poster class="inline-block">
                                                <template #trigger>
                                                    <ElButton
                                                        type="primary"
                                                        size="small"
                                                        class="!border-none"
                                                        style="--el-button-bg-color: #4A92FF"
                                                    >
                                                        分享
                                                    </ElButton>
                                                </template>
                                            </Poster>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-page rounded-[12px] px-[30px] py-[25px] mt-4">
                            <div class="font-medium text-xl">
                                邀请列表
                            </div>
                            <!--                            <div class="flex mt-2">-->
                            <el-tabs v-model="params.level" class="mt-2" @tab-change="getLists">
                                <el-tab-pane :label="`全部(${pager.extend.all})`" name=""/>
                                <el-tab-pane :label="`直接用户(${pager.extend.first})`" :name="1"/>
                                <el-tab-pane :label="`间接用户(${pager.extend.second})`" :name="2"/>
                            </el-tabs>

                            <div class="w-full mt-1 bg-white dark:bg-page pb-[20px]">
                                <el-table
                                    size="large"
                                    :data="pager.lists"
                                    v-loading="pager.loading"
                                    :style="{
                                        '--el-table-header-bg-color': isDark ? '#000' : '#EFEFEF'
                                    }"
                                >
                                    <el-table-column label="用户昵称">
                                        <template #default="{ row }">
                                            <div class="flex items-center">
                                                <el-image
                                                    class="w-[48px] h-[48px]"
                                                    :src="row.avatar"
                                                ></el-image>
                                                <div class="ml-2">
                                                    {{ row.nickname }}
                                                </div>
                                            </div>
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="订单量" prop="order_num">
                                        <template #default="{ row }">
                                            {{ row.order_num ? row.order_num : '0' }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="累计消费" prop="total_amount"/>
                                    <el-table-column label="邀请人数" prop="invite_num"/>
                                    <el-table-column label="分销资格" prop="is_distribution_desc"/>
                                    <el-table-column label="注册时间" prop="create_time"/>
                                </el-table>

                                <div class="flex justify-end mt-5 mr-4">
                                    <pagination v-model="pager" @change="getLists"/>
                                </div>
                            </div>
                        </div>

                    </div>

                <!--      申请分销      -->
                <ApplyPop
                    v-if="showApplyPop"
                    ref="applyPopRef"
                    @close-pop="closePop"
                ></ApplyPop>
                <!--      申请提现      -->
                <WithdrawApply
                    v-if="showWithdrawApplyPop"
                    ref="withdrawApplyPopRef"
                    @close-pop="closePop"
                ></WithdrawApply>
                <!--      收益详情      -->
                <IncomeDetail
                    v-if="showIncomeDetailPop"
                    ref="incomeDetailPopRef"
                    @close-pop="closePop"
                ></IncomeDetail>
                <!--      提现记录      -->
                <WithdrawRecord ref="withdrawRecordPopRef"></WithdrawRecord>
            </div>
        </ElScrollbar>
    </div>
</template>

<script setup lang="ts">
import {useDark} from '@vueuse/core'
import {distributionCenter, fansList} from '@/api/promotion'
import applyBg from '@/assets/image/user/distribution_apply_bg.png'
import linkBg from '@/assets/image/user/distribution_url_bg.png'
import posterBg from '@/assets/image/user/distribution_poster_bg.png'
import Poster from './_components/poster.vue'
import ApplyPop from './_components/apply.vue'
import IncomeDetail from './_components/income-detail.vue'
import WithdrawRecord from './_components/withdraw/record.vue'
import WithdrawApply from './_components/withdraw/apply.vue'

const isDark = useDark()
const router = useRouter()

const showApplyPop = ref(false)
const showIncomeDetailPop = ref(false)
const showWithdrawApplyPop = ref(false)
//申请分销弹框
const applyPopRef = shallowRef<InstanceType<typeof ApplyPop>>()
const incomeDetailPopRef = shallowRef<InstanceType<typeof IncomeDetail>>()
const withdrawRecordPopRef = shallowRef<InstanceType<typeof WithdrawRecord>>()
const withdrawApplyPopRef = shallowRef<InstanceType<typeof WithdrawApply>>()

//请求参数
const params = reactive({
    level: '' //1-直接用户 2-间接用户
})

//分页组件
const {pager, getLists, resetPage, resetParams} = usePaging({
    fetchFun: fansList,
    params: params
})

const tabChange = () => {
    getLists()
}

//分销数据
const centerData: any = ref()
const getCenterData = async () => {
    centerData.value = await distributionCenter()
}

//打开收益明细
const openIncomePop = async () => {
    showIncomeDetailPop.value = true
    await nextTick()
    incomeDetailPopRef.value.open()
}

//打开申请分销弹框
const openApplyPop = async () => {
    if (centerData.value.apply_detail?.status == 1) {
        feedback.msgWarning('正在审核中！')
        return
    }
    showApplyPop.value = true
    await nextTick()
    applyPopRef.value.open()
}

// 打开提现弹窗
const openWithdrawPop = async () => {
    if (centerData.value.withdraw_config.type.length == 0) {
        feedback.alertWarning('后台未设置提现方式！')
        return
    }
    showWithdrawApplyPop.value = true
    await nextTick()
    withdrawApplyPopRef.value.open()
}

//复制链接
const copyLink = () => {
    copy(centerData.value.config.pc_promotion_url)
}

//关闭弹框
const closePop = () => {
    getLists()
    getCenterData()
    showApplyPop.value = false
    showIncomeDetailPop.value = false
    showWithdrawApplyPop.value = false
}

onMounted(() => {
    getLists()
    getCenterData()
})
</script>
