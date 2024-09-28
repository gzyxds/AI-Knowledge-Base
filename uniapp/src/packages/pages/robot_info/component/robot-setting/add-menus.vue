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
                添加菜单
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
                            <u-form-item label="关键词" prop="keyword" required>
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.keyword"
                                        placeholder="请输入关键词"
                                        :border="true"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item label="回复内容" prop="content">
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.content"
                                        placeholder="请输入回复内容，10000个字以内"
                                        :border="true"
                                        type="textarea"
                                        :height="300"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item label="上传图片">
                                <view class="mx-[-10rpx] flex-1 min-w-0">
                                    <app-upload
                                        v-model="formData.images"
                                        :max-count="9"
                                        return-type="string-array"
                                        :disabled="false"
                                    />
                                    <view class="text-muted">
                                        最多支持上传 9 张图
                                    </view>
                                </view>
                            </u-form-item>
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="flex p-[20rpx]">
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
import { computed, shallowRef, watch } from 'vue'
const props = defineProps<{
    show: boolean
    // 'add' | 'edit' | 'view'
    type: string
    data: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
    (event: 'update:data', value: boolean): void
    (event: 'confirm', value: any): void
}>()

const uFormRef = shallowRef()
const { show: showModel, data: formData } = useVModels(props, emit)

const formRules = {
    keyword: [
        {
            required: true,
            message: '请输入关键词'
        }
    ]
}

const handleConfirm = () => {
    uFormRef.value.validate((valid: boolean) => {
        if (valid) {
            emit('confirm', cloneDeep(formData.value))
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
