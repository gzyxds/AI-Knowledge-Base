<template>
    <div class="user-info">
        <el-popover
            placement="bottom"
            trigger="hover"
            :teleported="false"
            :show-arrow="false"
            transition="custom-popover"
            :width="390"
        >
            <template #reference>
                <slot />
            </template>
            <template #default>
                <div class="p-[8px]">
                    <div class="flex items-center">
                        <div class="flex-1 flex items-center">
                            <ElAvatar
                                class="flex-none"
                                :size="50"
                                :src="userStore.userInfo.avatar"
                            />
                            <div class="ml-[10px]">
                                <div class="text-lg line-clamp-1">
                                    {{ userStore.userInfo.nickname }}
                                </div>
                                <div
                                    class="text-xs text-tx-secondary mt-1 cursor-pointer flex items-center"
                                    @click="copy(userStore.userInfo.sn)"
                                >
                                    ID：{{ userStore.userInfo.sn }}
                                    <Icon
                                        class="ml-1"
                                        size="12"
                                        name="el-icon-CopyDocument"
                                    ></Icon>
                                </div>
                            </div>
                        </div>
                        <div>
                            <NuxtLink to="/user/center">
                                <el-button text bg round>个人中心</el-button>
                            </NuxtLink>
                        </div>
                    </div>
                    <div
                        class="bg-[#eff2fe] mt-[20px] py-[20px] px-[5px] rounded-[10px] dark:bg-[#333]"
                    >
                        <NuxtLink
                            to="/user/member"
                            v-if="appStore.getIsShowMember"
                        >
                            <div class="flex justify-between px-[20px]">
                                <div>
                                    <div
                                        class="text-xl font-medium text-tx-primary"
                                    >
                                        {{
                                            userStore.userInfo.package_name ||
                                            decorate?.content?.title
                                        }}
                                    </div>
                                    <div
                                        class="text-tx-secondary text-sm mt-[10px]"
                                    >
                                        {{
                                            userStore.userInfo.package_time
                                                ? `有效期至：${userStore.userInfo.package_time}`
                                                : decorate?.content?.sub_title
                                        }}
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <el-button
                                        text
                                        bg
                                        round
                                        class="dark:!bg-[#1b1c1d] !bg-white"
                                    >
                                        <span
                                            v-if="
                                                userStore.userInfo
                                                    ?.package_is_overdue
                                            "
                                        >
                                            {{
                                                userStore.userInfo.package_name
                                                    ? '立即开通'
                                                    : decorate?.content?.btn
                                            }}
                                        </span>
                                        <span v-else>
                                            {{
                                                userStore.userInfo.package_name
                                                    ? '立即续费'
                                                    : decorate?.content?.btn
                                            }}
                                        </span>
                                    </el-button>
                                </div>
                            </div>
                        </NuxtLink>
                        <div class="flex mt-[20px]">
                            <div
                                class="flex-1 flex flex-col items-center px-[5px]"
                            >
                                <span
                                    class="text-md text-primary text-center font-bold"
                                >
                                    {{ userStore.userInfo.balance }}
                                </span>
                                <span class="text-xs mt-[5px]"
                                    >{{ appStore.getTokenUnit }}数量</span
                                >
                            </div>
                            <div
                                class="flex-1 flex flex-col items-center px-[5px]"
                            >
                                <span
                                    class="text-md text-primary text-center font-bold"
                                >
                                    {{ userStore.userInfo.robot_num }}
                                </span>
                                <span class="text-xs mt-[5px]">智能体</span>
                            </div>
                            <!-- <div class="flex-1 flex flex-col items-center px-[5px]">
                                <span class="text-md text-[#F7A40A] font-bold">{{
                                  userStore.userInfo.video_num
                                }}</span>
                                <span class="text-xs mt-[5px]">形象时长</span>
                              </div> -->
                        </div>
                    </div>
                    <div class="py-[20px] flex">
                        <NuxtLink
                            class="w-[33.3%] flex"
                            v-for="item in menu"
                            :key="item.path"
                            :to="item.path"
                        >
                            <div class="flex flex-col items-center w-full">
                                <div class="text-tx-regular">
                                    <Icon :name="item.icon" :size="20"></Icon>
                                </div>
                                <div class="mt-2">{{ item.name }}</div>
                            </div>
                        </NuxtLink>
                    </div>
                    <div
                        class="border-t border-solid border-br-light pt-[20px]"
                    >
                        <div class="flex justify-end">
                            <ElButton link @click="handleLogout">
                                退出登录
                            </ElButton>
                        </div>
                    </div>
                </div>
            </template>
        </el-popover>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user'
import { logout } from '@/api/account'
import { useAppStore } from '@/stores/app'
import { getChatRecord } from '~/api/chat'
import { getDecorate } from '~/api/app'

const userStore = useUserStore()
const { copy } = useCopy()
const appStore = useAppStore()
const menu = ref([
    {
        icon: 'local-icon-user_works',
        name: '我的作品',
        path: '/user/works'
    },
    {
        icon: 'local-icon-head_goumai',
        name: '购买记录',
        path: '/user/record'
    },

    {
        icon: 'local-icon-head_shiyong',
        name: '余额明细',
        path: '/user/balance'
    }
])

const { data: decorate } = await useAsyncData(() => getDecorate({ id: 2 }), {
    default() {
        return []
    },
    transform(data) {
        return JSON.parse(data.data)[1]
    },
    lazy: true
})

const handleLogout = async () => {
    await feedback.confirm('确定退出登录吗？')
    await logout()
    userStore.logout()
    window.location.reload()
}
</script>
<style scoped lang="scss">
.user-info {
    :deep() {
        .el-popper {
            padding: 20px !important;
        }
    }
}
</style>