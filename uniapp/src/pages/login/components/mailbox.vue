<template>
    <view>
        <view class="mb-[60rpx]">
            <u-form border-bottom>
                <u-form-item>
                    <u-icon
                        class="mr-2"
                        :size="36"
                        name="/static/images/icon/icon_mobile.png"
                    />
                    <u-input
                        class="flex-1"
                        v-model="formData.email"
                        :border="false"
                        placeholder="请输入邮箱账号"
                    />
                </u-form-item>

                <u-form-item v-if="isCodeLogin">
                    <u-icon
                        class="mr-2"
                        :size="36"
                        name="/static/images/icon/icon_code.png"
                    />
                    <u-input
                        class="flex-1"
                        v-model="formData.code"
                        placeholder="请输入验证码"
                        :border="false"
                    />
                    <view
                        class="border-l border-solid border-0 border-light pl-3 leading-4 ml-3 w-[180rpx]"
                        @click="sendEmail"
                    >
                        <u-verification-code
                            ref="uCodeRef"
                            :seconds="60"
                            @change="codeChange"
                            change-text="x秒"
                        />
                        <text
                            :class="
                                isValidMailBox ? 'text-primary' : 'text-muted'
                            "
                        >
                            {{ codeTips }}
                        </text>
                    </view>
                </u-form-item>
                <u-form-item v-if="isPasswordLogin">
                    <u-icon
                        class="mr-2"
                        :size="36"
                        name="/static/images/icon/icon_password.png"
                    />
                    <u-input
                        class="flex-1"
                        v-model="formData.password"
                        type="password"
                        placeholder="请输入密码"
                        :border="false"
                    />
                    <router-navigate
                        to="/packages/pages/forget_pwd/forget_pwd?type=2"
                    >
                        <view
                            class="border-l border-solid border-0 border-light pl-3 text-muted leading-4 ml-3"
                        >
                            忘记密码？
                        </view>
                    </router-navigate>
                </u-form-item>
            </u-form>
        </view>

        <view class="mb-[40rpx]">
            <agreement ref="agreementRef" />
        </view>
        <u-button
            type="primary"
            shape="circle"
            hover-class="none"
            @click="handleLogin"
        >
            登 录
        </u-button>

        <view class="text-content flex justify-between mt-[40rpx]">
            <view class="flex-1">
                <view
                    v-if="isCodeLogin"
                    @click="changeLoginScene(EmailSceneEnum.PASSWORD)"
                >
                    邮箱密码登录
                </view>
                <view
                    v-if="isPasswordLogin"
                    @click="changeLoginScene(EmailSceneEnum.CODE)"
                >
                    邮箱验证码登录
                </view>
            </view>

            <router-navigate
                v-if="hasMailboxRegister"
                to="/packages/pages/register/register?type=2"
            >
                注册账号
            </router-navigate>
        </view>
    </view>
</template>
<script setup lang="ts">
import {reactive, shallowRef, computed, ref} from 'vue'
import { useAppStore } from '@/stores/app'
import {sendEmailCode} from "@/api/account";
import {SMSEnum} from "@/enums/appEnums";

const appStore = useAppStore()
const emit = defineEmits<{
    (event: 'login', value: any): void
}>()
const agreementRef = shallowRef()
enum EmailSceneEnum {
    CODE = 4,
    PASSWORD = 2
}


const formData = reactive({
    scene: EmailSceneEnum.PASSWORD,
    email: '',
    captcha: '',
    code: '',
    password: ''
})

const isCodeLogin = computed(() => formData.scene === EmailSceneEnum.CODE)
const isPasswordLogin = computed(
    () => formData.scene === EmailSceneEnum.PASSWORD
)

const hasMailboxRegister = computed(() =>
    appStore.getLoginConfig.register_way?.includes('2')
)

const isValidMailBox = computed(() => uni.$u.test.email(formData.email))
const changeLoginScene = (scene: EmailSceneEnum) => {
    formData.scene = scene
}


const uCodeRef = shallowRef()
const codeTips = ref('')
const codeChange = (text: string) => {
    codeTips.value = text
}

const sendEmail = async () => {
    console.log(isValidMailBox.value)
    if (!formData.email) {
        uni.$u.toast('请输入邮箱')
        return Promise.reject()
    }
    if (!isValidMailBox.value) {
        uni.$u.toast('请输入正确的邮箱地址')
        return Promise.reject()
    }
    await sendEmailCode({
        scene: SMSEnum.LOGIN,
        email: formData.email
    })
    uni.$u.toast('发送成功')
    uCodeRef.value?.start()
}

const handleLogin = () => {
    if (!agreementRef.value?.checkAgreement()) {
        return
    }
    if (!formData.email) return uni.$u.toast('请输入邮箱号码')
    if (formData.scene == EmailSceneEnum.PASSWORD) {
        if (!formData.password) return uni.$u.toast('请输入密码')
    }
    if (formData.scene == EmailSceneEnum.CODE) {
        if (!formData.code) return uni.$u.toast('请输入验证码')
    }

    emit('login', {
        ...formData
    })
}
</script>
