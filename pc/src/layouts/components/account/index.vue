<template>
    <ElDialog
        v-model="showLogin"
        width="auto"
        class="login-popup"
        append-to-body
        :show-close="userStore.loginPopupType !== LoginPopupTypeEnum.BIND_MOBILE"
        :close-on-click-modal="false"
        style="border-radius: 16px; overflow: hidden; padding: 0"
    >
        <div class="flex">
            <div
                v-if="
                  appStore.getWebsiteConfig.pc_login_image &&
                  userStore.loginPopupType == LoginPopupTypeEnum.LOGIN &&
                  !appStore.isMobile
                "
            >
                <ElImage
                    class="w-[320px] h-full"
                    fit="cover"
                    :src="appStore.getWebsiteConfig.pc_login_image"
                />
            </div>
            <div class="flex-1 text-tx-primary flex flex-col w-[400px]">
                <!--        登录页面        -->
                <Login v-if="userStore.loginPopupType == LoginPopupTypeEnum.LOGIN"/>
                <!--        忘记密码        -->
                <ForgotPwd
                    v-if="
                        userStore.loginPopupType == LoginPopupTypeEnum.FORGOT_PWD_MAILBOX ||
                        userStore.loginPopupType == LoginPopupTypeEnum.FORGOT_PWD_MOBILE
                    "
                />
                <!--        注册账号        -->
                <Register
                    v-if="userStore.loginPopupType == LoginPopupTypeEnum.REGISTER"
                />
                <!--        绑定手机        -->
                <BindMobile
                    v-if="
                        userStore.loginPopupType ==
                        LoginPopupTypeEnum.BIND_MOBILE
                    "
                />
                <!--        绑定微信        -->
                <BindWeixin
                    v-if="
                        userStore.loginPopupType ==
                        LoginPopupTypeEnum.BIND_WEIXIN
                    "
                />
            </div>
        </div>
    </ElDialog>
</template>
<script lang="ts" setup>
import Login from './login/index.vue'
import ForgotPwd from './forgot-pwd.vue'
import Register from './register/index.vue'
import BindMobile from './bind/bind-mobile.vue'
import BindWeixin from './bind/bind-weixin.vue'
import {useUserStore} from '@/stores/user'
import {LoginPopupTypeEnum} from '~/enums/appEnums'
import {useAppStore} from '~/stores/app'

const userStore = useUserStore()
const appStore = useAppStore()
const showLogin = computed({
    get() {
        return userStore.showLogin
    },
    set(value) {
        userStore.showLogin = value
    }
})
watch(
    () => userStore.showLogin,
    (value) => {
        if (!value) userStore.temToken = null
    }
)
</script>

<style lang="scss">
.login-popup {
    .el-dialog__header {
        padding: 0;
    }

    .el-dialog__body {
        padding: 0 !important;
    }
}
</style>
