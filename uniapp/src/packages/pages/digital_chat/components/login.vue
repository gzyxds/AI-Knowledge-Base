<template>
    <view>
        <u-modal
            ref="uModalRef"
            v-model="showModel"
            :async-close="true"
            @confirm="handleConfirm"
            title="输入密码"
        >
            <view class="p-[40rpx]">
                <u-form :model="formData" ref="uFormRef" :border-bottom="false">
                    <u-form-item label="密码" prop="password" required>
                        <u-input
                            v-model="formData.password"
                            placeholder="请输入密码"
                            :border="true"
                        />
                    </u-form-item>
                </u-form>
            </view>
        </u-modal>
    </view>
</template>
<script lang="ts" setup>
import { reactive, shallowReactive, shallowRef, watch } from 'vue'
import { useVModel } from '@vueuse/core'
const props = defineProps<{
    show: boolean
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
    (event: 'confirm', value: any): void
}>()
const uFormRef = shallowRef()
const uModalRef = shallowRef()
const showModel = useVModel(props, 'show', emit)
const formData = reactive({
    password: ''
})
watch(showModel, (value) => {
    if (value) {
        setTimeout(() => {
            uFormRef.value.setRules(formRules)
        })
    }
})
const formRules = shallowReactive({
    password: [
        {
            required: true,
            message: '请输入密码'
        }
    ]
})

const handleConfirm = async () => {
    uModalRef.value?.clearLoading()
    uFormRef.value.validate((valid: boolean) => {
        if (valid) {
            emit('confirm', formData)
            showModel.value = false
        }
    })
}
</script>
