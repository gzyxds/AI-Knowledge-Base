<template>
    <div class="h-full relative">
        <div
            class="w-[200px] bg-body rounded-[12px] h-full px-[16px] py-[20px]"
        >
            <div class="text-base">个人中心</div>
            <div class="mt-[10px]">
                <div
                    class="py-[10px] cursor-pointer"
                    v-for="(item, index) in menu"
                    :key="index"
                    v-show="item.show"
                    @click="toSelect(index)"
                >
                    <div
                        :class="{ isSelect: isSelect == index }"
                        class="flex items-center h-[40px] leading-[40px] w-full pl-[15px]"
                    >
                        <icon
                            :name="`local-icon-${item.icon}`"
                            size="16px"
                        ></icon>
                        <span class="ml-[10px]">{{ item.name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { useVModel } from '@vueuse/core'
import { useUserStore } from '@/stores/user'
import { copy } from '@/utils/util'
import { userEdit } from '@/api/user'

enum UserFieldEnum {
    NONE = '',
    AVATAR = 'avatar',
    USERNAME = 'account',
    NICKNAME = 'nickname',
    SEX = 'sex'
}

const router = useRouter()
const route = useRoute()

const appStore = useAppStore()
const userStore = useUserStore()

const isSelect = ref(0)

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits(['update:modelValue'])

const drawer = useVModel(props, 'modelValue', emit)

const menu = ref([
    {
        name: '充值中心',
        icon: 'chongzhi',
        path: '/user/recharge',
        show: appStore?.getIsShowRecharge || false
    },
    {
        name: '会员中心',
        icon: 'open_vip',
        path: '/user/member',
        show: appStore?.getIsShowMember || false
    },
    {
        name: '分销推广',
        icon: 'distribution',
        path: '/user/promotion/distribution',
        show: true
    },
    {
        name: '任务奖励',
        icon: 'task_reward',
        path: '/user/task_reward',
        show: true
    },
    {
        name: '购买记录',
        icon: 'goumaijilu',
        path: '/user/record',
        show: true
    },
    {
        name: '我的作品',
        icon: 'my_works',
        path: '/user/works',
        show: true
    },
    {
        name: '余额明细',
        icon: 'yuemingxi',
        path: '/user/balance',
        show: true
    },
    {
        name: '消息通知',
        icon: 'notice',
        path: '/user/notification',
        show: true
    },
    {
        name: '个人信息',
        icon: 'gerenzhongxin',
        path: '/user/center',
        show: true
    }
])

//选中菜单
const toSelect = (index: number) => {
    router.push(menu.value[index].path)
}
watch(
    () => route.path,
    (value) => {
        console.log(value)

        //const i = menu.value.findIndex(item => item.path == value)
        const i = menu.value.findIndex((item) => value.includes(item.path))
        isSelect.value = i
    },
    {
        immediate: true
    }
)

const setUserInfo = async (value: any, field: any) => {
    try {
        await userEdit({ value, field })
        userStore.getUser()
    } catch (error) {
        feedback.msgError(error)
    }
}
</script>

<style scoped lang="scss">
.isSelect {
    color: #ffffff;
    border-radius: 6px;
    background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
}
</style>
