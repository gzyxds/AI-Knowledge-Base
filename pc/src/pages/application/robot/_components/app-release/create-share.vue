<template>
    <div>
        <popup
            ref="popupRef"
            :title="`${type == 'add' ? '创建' : '编辑'}链接`"
            :async="true"
            width="550px"
            @confirm="handleConfirm"
            @close="formRef?.resetFields()"
        >
            <el-form
                ref="formRef"
                :model="formData"
                :rules="formRules"
                label-width="84px"
            >
                <el-form-item label="名称" prop="name">
                    <el-input
                        v-model="formData.name"
                        placeholder="记录名称，仅用于展示"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input
                        v-model="formData.password"
                        type="password"
                        placeholder="不设置密码，可直接访问"
                        show-password
                    />
                </el-form-item>
                <el-form-item
                    label="对话模式"
                    prop="chat_type"
                    required
                    v-if="isShowChatType && type === 'add'"
                >
                    <div>
                        <el-radio-group v-model="formData.chat_type">
                            <el-radio :label="1">文本对话</el-radio>
                            <el-radio :label="2">形象对话</el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            若关闭或没有配置形象选择后，默认展示文本
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { cloneDeep } from 'lodash-es'
const emit = defineEmits<{
    (event: 'confirm', value: any, type: string): void
}>()
const props = defineProps<{
    isShowChatType: boolean | number
}>()
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = ref({
    name: '',
    password: '',
    chat_type: 1
})

const type = ref('add')
const formRules = shallowReactive<FormRules>({
    name: [
        {
            required: true,
            message: '请输入分享名称'
        }
    ]
})
const open = (data?: any) => {
    if (data) {
        type.value = 'edit'
        formData.value = cloneDeep(data)
    } else {
        type.value = 'add'
        formData.value = {
            name: '',
            password: '',
            chat_type: 1
        }
    }
    popupRef.value?.open()
}
const close = () => {
    popupRef.value?.close()
}
const handleConfirm = async () => {
    await formRef.value?.validate()
    emit('confirm', formData.value, type.value)
}

defineExpose({
    open,
    close
})
</script>
