<template>
    <div class="application cursor-pointer ml-[20px] ">
        <el-popover
            placement="bottom"
            :width="350"
            trigger="hover"
            :show-arrow="false"
            transition="custom-popover"
            :teleported="false"
        >
            <template #default>
                <div class="grid grid-cols-1 gap-y-[15px]">
                    <div
                        v-for="item in navList"
                        :key="item.background"
                        v-show="item.show"
                        class="card-item rounded-[12px] p-[20px]"
                        :style="{
                            'background-image': `url(${item.background})`
                        }"
                        @click="handleNavList(item)"
                    >
                        <div class=" text-[#333333] text-base font-medium">{{ item.title }}</div>
                        <div class="text-tx-secondary text-xs mt-[10px]">{{ item.description }}</div>
                    </div>
                </div>
            </template>
            <template #reference>
                <img
                    src="@/assets/image/icon_app.png"
                    class="w-[24px] h-[24px]"
                    alt=""
                />
            </template>
        </el-popover>

        <RedeemCodePop
            v-if="showRedeemCode"
            @close="showRedeemCode = false"
        />
    </div>
</template>
<script lang="ts" setup>
import {useAppStore} from '~/stores/app'
import {useUserStore} from '~/stores/user'

import DistributionBg from '~/assets/image/distribution_bg.png'
import RedeemCodeBg from '~/assets/image/redeem_code_bg.png'
import TaskRewardBg from '~/assets/image/task_reward_bg.png'

import RedeemCodePop from './redeem-code-pop.vue'

interface NavListType {
    title: string
    description: string
    background: string
    link: string
    show: boolean
}

const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()

const showRedeemCode = ref<boolean>(false)
const navList: NavListType[] = [
    {
        title: '分销推广',
        description: '邀请伙伴一起加入吧！',
        background: DistributionBg,
        link: '/user/promotion/distribution',
        show: appStore?.config?.distribution?.is_open
    },
    {
        title: '卡密兑换',
        description: `可兑换会员、套餐、${ appStore.getTokenUnit }！`,
        background: RedeemCodeBg,
        link: '',
        show: appStore?.getRedeemCode?.is_open
    },
    {
        title: '任务奖励',
        description: '邀请好友，分享绘画、音乐等！',
        background: TaskRewardBg,
        link: '/user/task_reward',
        show: true
    }
]

const handleNavList = async (row: NavListType) => {
    if (row.link.length) {
        router.push(row.link)
    } else {
        showRedeemCode.value = true
    }
}
</script>

<style lang="scss" scoped>
.application {
    :deep() {
        .el-popper {
            padding: 20px !important;
        }
    }

    .card-item {
        @apply bg-cover bg-center bg-no-repeat;
    }
}
</style>