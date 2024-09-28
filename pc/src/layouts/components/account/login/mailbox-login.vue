<template>
    <div class="pt-[10px]">
        <div>
            <ElForm ref="formRef" size="large" :model="formData" :rules="formRules">
                <ElFormItem prop="email">
                    <ElInput v-model="formData.email" placeholder="请输入邮箱账号"/>
                </ElFormItem>
                <ElFormItem v-if="isPasswordLogin" prop="password">
                    <ElInput
                        v-model="formData.password"
                        type="password"
                        show-password
                        placeholder="请输入密码"
                    />
                </ElFormItem>
                <ElFormItem v-if="isCodeLogin" prop="code">
                    <ElInput v-model="formData.code" placeholder="请输入验证码">
                        <template #suffix>
                            <div
                                class="flex justify-center leading-5 w-[90px] pl-2.5 border-l border-br"
                            >
                                <VerificationCode
                                    ref="verificationCodeRef"
                                    @click-get="sendEmail"
                                />
                            </div>
                        </template>
                    </ElInput>
                </ElFormItem>
                <div class="flex justify-end">

                    <div class="flex-1 flex">
                        <ElButton
                            v-if="isPasswordLogin"
                            type="primary"
                            link
                            @click="changeLoginScene(MailboxSceneEnum.CODE)"
                        >
                            邮箱验证码登录
                        </ElButton>
                        <ElButton
                            v-if="isCodeLogin"
                            type="primary"
                            link
                            @click="changeLoginScene(MailboxSceneEnum.PASSWORD)"
                        >
                            邮箱密码登录
                        </ElButton>
                    </div>

                    <ElButton
                        link
                        @click="
                          userStore.setLoginPopupType(LoginPopupTypeEnum.FORGOT_PWD_MAILBOX)
                        "
                    >
                        忘记密码？
                    </ElButton>
                </div>

                <ElFormItem class="my-[30px]">
                    <ElButton
                        class="w-full"
                        type="primary"
                        :loading="isLock"
                        @click="loginLock"
                    >
                        登录
                    </ElButton>
                </ElFormItem>
            </ElForm>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type {FormInstance, FormRules} from 'element-plus'
import {LoginPopupTypeEnum, SMSEnum} from '@/enums/appEnums'
import {login, sendEmailCode} from '@/api/account'
import {useUserStore} from '@/stores/user'
import {terminal} from '~/config'
import {useAppStore} from '~/stores/app'
import {smsSend} from "~/api/app";

enum MailboxSceneEnum {
    CODE = 4,
    PASSWORD = 2
}

const userStore = useUserStore()
const appStore = useAppStore()
const formRef = shallowRef<FormInstance>()

const formRules: FormRules = {
    email: [
        {
            required: true,
            message: '请输入邮箱账号'
        },
        {type: 'email', message: '请输入正确的邮箱账号'}
    ],
    password: [
        {
            required: true,
            message: '请输入密码'
        }
    ],
    code: [
        {
            required: true,
            message: '请输入验证码'
        }
    ]
}
const formData = reactive({
    code: '',
    email: '',
    password: '',
    scene: MailboxSceneEnum.CODE,
    terminal
})

const isCodeLogin = computed(() => formData.scene === MailboxSceneEnum.CODE)
const isPasswordLogin = computed(
    () => formData.scene === MailboxSceneEnum.PASSWORD
)

const changeLoginScene = (scene: MailboxSceneEnum) => {
    formData.scene = scene
}

const verificationCodeRef = shallowRef()
const sendEmail = async () => {
    await formRef.value?.validateField(['email'])
    await sendEmailCode({
        scene: SMSEnum.LOGIN,
        email: formData.email
    })
    verificationCodeRef.value?.start()
}

const {lockFn: loginLock, isLock} = useLockFn(async () => {
    await formRef.value?.validate()
    const data = await login(formData)
    if (!data.mobile && appStore.getLoginConfig.coerce_mobile) {
        userStore.temToken = data.token
        userStore.setLoginPopupType(LoginPopupTypeEnum.BIND_MOBILE)
    } else {
        userStore.login(data.token)
        userStore.setUser(data)
        userStore.toggleShowLogin(false)
        location.reload()
    }
})
</script>

<style lang="scss" scoped></style>
