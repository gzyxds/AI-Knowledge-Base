<template>
    <Popup ref="popRef" :title="userStore.userInfo.mobile ? '点击更改' : '立即绑定'" async
           confirm-button-text="确认更改" @confirm="submit" @close="$emit('close')">
        <div>
            <ElForm ref="formRef" size="large" :model="formData" :rules="formRules">
                <ElFormItem prop="mobile">
                    <ElInput v-model="formData.mobile" placeholder="请输入手机号">
                        <template #prepend>
                            <ElSelect model-value="+86" style="width: 80px">
                                <ElOption label="+86" value="+86"/>
                            </ElSelect>
                        </template>
                    </ElInput>
                </ElFormItem>

                <ElFormItem prop="code">
                    <ElInput v-model="formData.code" placeholder="请输入验证码">
                        <template #suffix>
                            <div class="flex justify-center leading-5 w-[90px] pl-2.5 border-l border-br">
                                <VerificationCode ref="verificationCodeRef" @click-get="sendSms"/>
                            </div>
                        </template>
                    </ElInput>
                </ElFormItem>
            </ElForm>
        </div>
    </Popup>
</template>

<script setup lang="ts">
import {smsSend} from '@/api/app'
import {userBindMobile} from '@/api/user'
import {SMSEnum} from '@/enums/appEnums'
import type {FormInstance} from "element-plus";
import {useUserStore} from "~/stores/user";

const emits = defineEmits(['close'])
const popRef = shallowRef()
const userStore = useUserStore()

const formData = reactive({
    mobile: '',
    code: ''
})

const verificationCodeRef = shallowRef()

const formRef = shallowRef<FormInstance>()

const open = () => {
    popRef.value.open()
}

const formRules = {
    mobile: [
        {
            required: true,
            message: '请输入手机号'
        }
    ],
    code: [
        {
            required: true,
            message: '请输入验证码'
        }
    ]
}

const sendSms = async () => {
    await formRef.value?.validateField(['mobile'])
    await smsSend({
        scene: SMSEnum.BIND_MOBILE,
        mobile: formData.mobile
    })

    verificationCodeRef.value?.start()
}

//提交
const submit = async () => {
    await userBindMobile({type: 'bind', ...formData})
    popRef.value.close()
}

defineExpose({open})

</script>