<template>
    <u-popup
        v-model="showModel"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[80vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                用量设置
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
                            <u-form-item
                                label="限制每个用户总对话数"
                                prop="limit_today_chat"
                                required
                            >
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.limit_today_chat"
                                        placeholder="请输入限制数量"
                                        :border="true"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="限制每个用户每天总对话数"
                                prop="limit_total_chat"
                                required
                            >
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.limit_total_chat"
                                        placeholder="请输入限制数量"
                                        :border="true"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="超出将默认回复"
                                prop="limit_exceed"
                                required
                            >
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.limit_exceed"
                                        placeholder="请输入超出将默认回复内容"
                                        :border="true"
                                        type="textarea"
                                        :height="200"
                                    />
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
import { computed, shallowRef, watch, reactive } from 'vue'
const props = defineProps<{
    show: boolean
    data: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
    (event: 'confirm', value: any): void
}>()

const uFormRef = shallowRef()
const { show: showModel } = useVModels(props, emit)
const formData = reactive({
    limit_exceed: '',
    limit_today_chat: 0,
    limit_total_chat: 1
})

const formRules = {
    limit_exceed: [
        {
            required: true,
            message: '请输入超出将默认回复'
        }
    ],
    limit_today_chat: [
        {
            required: true,
            message: '请输入限制每个用户总对话数'
        }
    ],
    limit_total_chat: [
        {
            required: true,
            message: '请输入限制每个用户每天总对话数'
        }
    ]
}

const handleConfirm = () => {
    uFormRef.value.validate((valid: boolean) => {
        if (valid) {
            emit('confirm', cloneDeep(formData))
            showModel.value = false
        }
    })
}

watch(showModel, (value) => {
    if (value) {
        setTimeout(() => {
            uFormRef.value.setRules(formRules)
        })
    }
})

watch(
    () => props.data,
    (value) => {
        Object.assign(formData, value)
    }
)
</script>
