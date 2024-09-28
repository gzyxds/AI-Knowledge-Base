<template>
    <u-popup
        v-model="showModel"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[60vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                {{ mode == 'add' ? '创建' : '编辑' }}链接
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="p-[20rpx]">
                        <u-form
                            :model="formData"
                            ref="uFormRef"
                            label-position="top"
                            :border-bottom="false"
                        >
                            <u-form-item label="链接名称" prop="name" required>
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.name"
                                        placeholder="请输入链接名称"
                                        :border="true"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item label="访问密码" prop="password">
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.password"
                                        placeholder="请输入访问密码"
                                        :border="true"
                                        type="password"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="对话模式"
                                prop="chat_type"
                                v-if="type === 1 && mode == 'add'"
                            >
                                <view>
                                    <u-radio-group v-model="formData.chat_type">
                                        <u-radio :name="1"> 文本对话 </u-radio>
                                        <u-radio :name="2"> 形象对话 </u-radio>
                                    </u-radio-group>
                                    <view class="text-muted">
                                        若关闭或没有配置形象选择后，默认展示文本
                                    </view>
                                </view>
                            </u-form-item>
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="flex p-[20rpx]">
                <view class="flex-1 mr-[20rpx]">
                    <u-button @click="showModel = false"> 取消 </u-button>
                </view>
                <view class="flex-1">
                    <u-button type="primary" @click="handleConfirm">
                        保存
                    </u-button>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { cloneDeep } from 'lodash'
import { useVModels } from '@vueuse/core'
import { computed, shallowRef, watch, reactive, ref } from 'vue'
const props = defineProps<{
    mode: string
    data?: any
    show: boolean
    type: number | string
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
    (event: 'confirm', value: any): void
}>()

const uFormRef = shallowRef()
const { show: showModel } = useVModels(props, emit)
const formData = ref({
    name: '',
    password: '',
    chat_type: 1
})

const formRules = {
    name: [
        {
            required: true,
            message: '请输入链接名称'
        }
    ]
}

const handleConfirm = () => {
    uFormRef.value.validate((valid: boolean) => {
        if (valid) {
            emit('confirm', {value: cloneDeep(formData.value), type: props.mode})
            showModel.value = false
        }
    })
}

watch(showModel, (value) => {
    if (value) {
        setTimeout(() => {
            uFormRef.value.setRules(formRules)
        })
        if (props.mode == 'add') {
            formData.value = {
                name: '',
                password: '',
                chat_type: 1
            }
        } else {
            formData.value = {
                id: props.data?.id,
                name: props.data?.name,
                password: props.data?.secret
            } as any
        }
    }
})
</script>
