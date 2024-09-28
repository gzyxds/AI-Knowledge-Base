<template>
    <div class="shop-pages h-[458px]">
        <div class="link-list flex flex-wrap">
            <div
                class="link-item border border-br px-5 py-[5px] rounded-[3px] cursor-pointer mr-[10px] mb-[10px]"
                v-for="(item, index) in linkList"
                :class="{
                    'border-primary text-primary':
                        modelValue.path == item.path && modelValue.name == item.name
                }"
                :key="index"
                @click="handleSelect(item)"
            >
                {{ item.name }}
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import { LinkTypeEnum, type Link } from '.'

const props = defineProps({
    modelValue: {
        type: Object as PropType<Link>,
        default: () => ({})
    },
    type: {
        type: String,
        default: 'pc'
    },
    isTab: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: Link): void
}>()

const pcLink = [
    {
        name: '首页',
        path: '/',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'AI对话',
        path: '/dialogue/chat',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'AI创作',
        path: '/creation',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'AI智能体',
        path: '/application/layout/robot',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'AI搜索',
        path: '/search',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '智能体广场',
        path: '/robot_square',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'SD绘画',
        path: '/draw/sd',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'DALLE绘画',
        path: '/draw/dalle',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'MJ绘画',
        path: '/draw/mj',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'AI音乐',
        path: '/music',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'AI视频',
        path: '/video',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '充值中心',
        path: '/user/recharge',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '会员中心',
        path: '/user/member',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '我的作品',
        path: '/user/works',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/mind_map',
        name: '思维导图',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/user/promotion/distribution',
        name: '分销推广',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/app_center',
        name: '应用中心',
        type: LinkTypeEnum.SHOP_PAGES
    }
]

const mobileLink = [
    {
        name: '首页',
        path: '/pages/index/index',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'AI对话',
        path: '/packages/pages/dialogue/dialogue',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'AI创作',
        path: '/pages/ai_creation/ai_creation',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'AI智能体',
        path: '/pages/kb/kb',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'AI音乐',
        path: '/packages/pages/music/music',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'AI视频',
        path: '/packages/pages/video/video',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'AI搜索',
        path: '/packages/pages/ai_search/ai_search',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: 'SD绘画',
        path: '/packages/pages/draw/sd',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'DALLE绘画',
        path: '/packages/pages/draw/dalle',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: 'MJ绘画',
        path: '/packages/pages/draw/mj',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: '个人中心',
        path: '/pages/user/user',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: '个人信息',
        path: '/packages/pages/user_set/user_set',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: false
    },
    {
        name: '充值中心',
        path: '/packages/pages/recharge/recharge',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '会员中心',
        path: '/packages/pages/member_center/member_center',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '智能体广场',
        path: '/packages/pages/robot_square/robot_square',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        name: '联系客服',
        path: '/packages/pages/customer_service/customer_service',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '关于我们',
        path: '/packages/pages/as_us/as_us',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '隐私协议',
        path: '/packages/pages/agreement/agreement',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '购买记录',
        path: '/packages/pages/buy_record/buy_record',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        name: '我的作品',
        path: '/packages/pages/user_works/user_works',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/mind_map/mind_map',
        name: '思维导图',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/promotion_center/promotion_center',
        name: '分销推广',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/invite_poster/invite_poster',
        name: '邀请海报',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/redeem_code/redeem_code',
        name: '卡密兑换',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/notification/notification',
        name: '消息通知',
        type: LinkTypeEnum.SHOP_PAGES
    },
    {
        path: '/packages/pages/task_reward/task_reward',
        name: '任务奖励',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    },
    {
        path: '/packages/pages/app_center/app_center',
        name: '应用中心',
        type: LinkTypeEnum.SHOP_PAGES,
        canTab: true
    }
]

const linkList = computed(() =>
    props.type == 'pc' ? pcLink : mobileLink.filter((item) => (props.isTab ? item.canTab : true))
)

const handleSelect = (value: Link) => {
    emit('update:modelValue', value)
}
</script>
