<script setup lang="ts">
import type { FormInstance, FormRules } from "element-plus";
import { smsSend } from '@/api/app'
import { forgotPassword } from '@/api/user'
import { SMSEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'
import { sendEmailCode } from '~/api/account'

interface ChangePwdType {
    email?: string,
    mobile?: string,
    password: string,
    code: string,
    password_confirm: string
}

const emits = defineEmits(['close'])
const props = defineProps<{
    mobile: {
        default: '',
        type: String
    },
    email: {
        default: '',
        type: String
    }
}>()
const popRef = shallowRef()

const userStore = useUserStore()
const { setLoginPopupType } = useUserStore()
const formRef = shallowRef<FormInstance>()
const verificationCodeRef = shallowRef()
const formRules: FormRules = {
    mobile: [
        {
            required: true,
            message: '请输入手机号码'
        }
    ],
    email: [
        {
            required: true,
            message: '请输入邮箱账号'
        },
        { type: 'email', message: '请输入正确的邮箱账号' }
    ],
    code: [
        {
            required: true,
            message: '请输入验证码',
            trigger: ['change', 'blur']
        }
    ],
    password: [
        {
            required: true,
            message: '请输入6-20位数字+字母或符号组合',
            trigger: ['change', 'blur']
        },
        {
            min: 6,
            max: 20,
            message: '密码长度应为6-20',
            trigger: ['change', 'blur']
        }
    ],
    password_confirm: [
        {
            validator(rule: any, value: any, callback: any) {
                if (value === '') {
                    callback(new Error('请再次输入密码'))
                } else if (value !== formData.password) {
                    callback(new Error('两次输入的密码不一致'))
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur']
        }
    ]
}
const formData = reactive<ChangePwdType>({
    email: '',
    mobile: '',
    password: '',
    code: '',
    password_confirm: ''
})

watch(() => props.mobile, (val) => {
    if (val) {
        formData.mobile = val
    }
}, { immediate: true })

watch(() => props.email, (val) => {
    if (val) {
        formData.email = val
    }
}, { immediate: true })

const sendCode = async () => {
    formData.mobile
        ? await sendSms()
        : await sendEmail()
    verificationCodeRef.value?.start()
}
const sendSms = async () => {
    await formRef.value?.validateField(['mobile'])
    await smsSend({
        scene: SMSEnum.FIND_PASSWORD,
        mobile: formData.mobile
    })
}
const sendEmail = async () => {
    await formRef.value?.validateField(['email'])
    await sendEmailCode({
        scene: SMSEnum.FIND_PASSWORD,
        email: formData.email
    })
}

const handleConfirm = async () => {
    await formRef.value?.validate()
    await forgotPassword({
        ...formData,
        scene: formData.mobile ? 1 : 2
    })
    popRef.value.close()
}
const { lockFn: handleConfirmLock, isLock } = useLockFn(handleConfirm)

const open = () => {
    popRef.value.open()
}


defineExpose({ open })
</script>

<template>
    <Popup
        ref="popRef"
        title="设置登录密码"
        :async="true"
        confirm-button-text="确认"
        @confirm="handleConfirmLock"
        @close="$emit('close')"
    >
        <div>
            <ElForm
                ref="formRef"
                size="large"
                :model="formData"
                :rules="formRules"
            >
                <ElFormItem
                    v-if="formData.mobile"
                    prop="mobile"
                >
                    <ElInput
                        v-model="formData.mobile"
                        placeholder="请输入手机号"
                        disabled
                    >
                        <template #prepend>
                            <ElSelect
                                model-value="+86"
                                style="width: 80px"
                                disabled
                            >
                                <ElOption label="+86" value="+86" />
                            </ElSelect>
                        </template>
                    </ElInput>
                </ElFormItem>
                <ElFormItem
                    v-if="formData.email"
                    prop="email"
                    disabled=""
                >
                    <ElInput v-model="formData.email" placeholder="请输入邮箱账号" />
                </ElFormItem>
                <ElFormItem prop="code">
                    <ElInput v-model="formData.code" placeholder="请输入验证码">
                        <template #suffix>
                            <div
                                class="flex justify-center leading-5 w-[90px] pl-2.5 border-l border-br"
                            >
                                <VerificationCode
                                    ref="verificationCodeRef"
                                    @click-get="sendCode"
                                />
                            </div>
                        </template>
                    </ElInput>
                </ElFormItem>
                <ElFormItem prop="password">
                    <ElInput
                        v-model="formData.password"
                        placeholder="请输入6-20位数字+字母或符号组合"
                        type="password"
                        show-password
                    />
                </ElFormItem>
                <ElFormItem prop="password_confirm">
                    <ElInput
                        v-model="formData.password_confirm"
                        placeholder="请再次输入密码"
                        type="password"
                        show-password
                    />
                </ElFormItem>
            </ElForm>
        </div>
    </Popup>
</template>