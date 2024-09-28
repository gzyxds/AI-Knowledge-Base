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
                创建API
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
                            <u-form-item label="接口名称" prop="name" required>
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.name"
                                        placeholder="请输入接口名称"
                                        :border="true"
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
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
    (event: 'confirm', value: any): void
}>()

const uFormRef = shallowRef()
const { show: showModel } = useVModels(props, emit)
const formData = reactive({
    name: ''
})

const formRules = {
    name: [
        {
            required: true,
            message: '请输入接口名称'
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
</script>
