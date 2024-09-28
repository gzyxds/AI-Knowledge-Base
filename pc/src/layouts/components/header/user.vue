<template>
    <div class="flex flex-none">
        <div class="flex items-center">
            <template v-if="userStore.isLogin">
                <NuxtLink v-if="!isHidden" to="/user/balance">
                    <div
                        class="bg-[#F0F1F3] flex items-center py-[5px] px-[8px] rounded-[8px] mr-[10px] dark:bg-[#333]"
                    >
                        <img
                            class="w-[10px] h-[14px] mr-1"
                            src="@/assets/image/icon_lightning.png"
                            alt=""
                        />
                        {{ userStore.userInfo.balance }}
                    </div>
                </NuxtLink>
                <NuxtLink v-if="!isHidden" to="/user/balance">
                    <div
                        class="bg-[#F0F1F3] flex items-center py-[5px] px-[8px] rounded-[8px] mr-[10px] dark:bg-[#333]"
                    >
                        <img
                            class="w-[16px] h-[16px] mr-1"
                            src="@/assets/image/icon_robot.png"
                            alt=""
                        />
                        {{ userStore.userInfo.robot_num }}
                    </div>
                </NuxtLink>

                <div
                    v-if="!isHidden"
                    class="mr-[10px] bg-[#FFFBF3] rounded-full flex items-center"
                >
                    <MemberBtn v-if="appStore.getIsShowMember" />
                </div>
            </template>

            <!--      应用合集      -->
            <Application v-if="!isHidden"></Application>

            <!--      切换色彩模式      -->
            <div
                v-if="!isHidden"
                class="mx-[20px] cursor-pointer"
                @click="toggleDark"
            >
                <Icon v-if="isDark" name="local-icon-dark" :size="22" />
                <Icon v-else name="local-icon-light" :size="22" />
            </div>

            <template v-if="userStore.isLogin">
                <!--      消息通知      -->
                <Notification v-if="!isHidden"></Notification>

                <!--      个人信息      -->
                <UserInfo>
                    <div class="flex items-center">
                        <ElAvatar :size="25" :src="userStore.userInfo.avatar" />
                        <div class="ml-1 flex items-center">
                            <div
                                v-if="!appStore.isMobile"
                                class="line-clamp-1 max-w-[100px] mr-2"
                            >
                                {{ userStore.userInfo.nickname }}
                            </div>
                            <Icon name="el-icon-ArrowDown" />
                        </div>
                    </div>
                </UserInfo>
            </template>
        </div>
        <ElButton
            v-if="!userStore.isLogin"
            type="primary"
            @click="handleToLogin"
        >
            登录/注册
        </ElButton>
    </div>
</template>
<script lang="ts" setup>
import MemberBtn from './member-btn.vue'
import UserInfo from './user-info.vue'
import Notification from './notification.vue'
import Application from './application.vue'
import { useUserStore } from '@/stores/user'
import { LoginPopupTypeEnum } from '@/enums/appEnums'
import { useAppStore } from '~/stores/app'
import { useSettingStore } from '~/stores/setting'
import { useDark } from '@vueuse/core'

const props = defineProps<{
    isHidden?: boolean
}>()

const settingStore = useSettingStore()
const isDark = useDark()
const userStore = useUserStore()
const appStore = useAppStore()

const toggleDark = async (event: MouseEvent) => {
    // 判断浏览器是否兼容此api
    if (typeof document.startViewTransition !== 'function') {
        isDark.value = !isDark.value
        settingStore.isDark = isDark.value
        settingStore.setTheme()
        return
    }

    const x = event.clientX
    const y = event.clientY
    const endRadius = Math.hypot(
      Math.max(x, innerWidth - x),
      Math.max(y, innerHeight - y)
    )

    // @ts-ignore
    const transition = document.startViewTransition(() => {
        const root = document.documentElement
        isDark.value = root.classList.contains('dark')
        root.classList.remove(isDark.value ? 'dark' : 'light')
        root.classList.add(isDark.value ? 'light' : 'dark')
    })

    transition.ready.then(() => {
        const clipPath = [
            `circle(0px at ${x}px ${y}px)`,
            `circle(${endRadius}px at ${x}px ${y}px)`
        ]
        const animation = document.documentElement.animate(
          {
              clipPath: isDark.value ? [...clipPath].reverse() : clipPath
          } as any,
          {
              duration: 500,
              easing: 'ease-in',
              pseudoElement: isDark.value
                ? '::view-transition-old(root)'
                : '::view-transition-new(root)'
          } as any
        )

        // 在动画开始过渡时再更新状态
        isDark.value = !isDark.value
        settingStore.isDark = isDark.value
        settingStore.setTheme()
    })
}


const handleToLogin = () => {
    userStore.setLoginPopupType(LoginPopupTypeEnum.LOGIN)
    userStore.toggleShowLogin(true)
}
</script>

<style lang="scss">
::view-transition-old(root),
::view-transition-new(root) {
    animation: none;
    mix-blend-mode: normal;
}

/* 进入dark模式和退出dark模式时，两个图像的位置顺序正好相反 */
.dark::view-transition-old(root) {
    z-index: 1;
}

.dark::view-transition-new(root) {
    z-index: 999;
}

::view-transition-old(root) {
    z-index: 999;
}

::view-transition-new(root) {
    z-index: 1;
}
</style>