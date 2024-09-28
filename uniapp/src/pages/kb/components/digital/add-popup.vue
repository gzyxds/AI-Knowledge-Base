<template>
    <u-popup
        v-model="show"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[70vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                新增形象
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
                            <u-form-item label="形象名称" prop="name" required>
                                <u-input
                                    v-model="formData.name"
                                    placeholder="请输入形象名称"
                                    :border="true"
                                />
                            </u-form-item>
                            <u-form-item
                                label="形象头像"
                                prop="avatar"
                                required
                            >
                                <view class="mx-[-10rpx] flex-1 min-w-0">
                                    <app-upload v-model="formData.avatar" />
                                </view>
                            </u-form-item>

                            <u-form-item label="形象封面" prop="image" required>
                                <view class="mx-[-10rpx] flex-1 min-w-0">
                                    <app-upload v-model="formData.image" />
                                </view>
                            </u-form-item>
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="flex p-[20rpx]">
                <view class="flex-1">
                    <u-button type="primary" @click="handelSave">保存</u-button>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { postDigital } from '@/api/digital'
import router from '@/router'
import { watch, computed, ref, shallowRef } from 'vue'

const props = withDefaults(
    defineProps<{
        modelValue: boolean
    }>(),
    {}
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
    (event: 'update'): void
}>()
const uFormRef = shallowRef()
const show = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

const formData = ref({
    image: '',
    name: '',
    avatar: ''
})
const rules = {
    name: [
        {
            required: true,
            message: '请输入形象名称'
        }
    ],
    image: [
        {
            required: true,
            message: '请选择形象封面'
        }
    ],
    avatar: [
        {
            required: true,
            message: '请选择形象头像'
        }
    ]
}

const handelSave = () => {
    uFormRef.value.validate(async (valid: boolean) => {
        if (valid) {
            const { id } = await postDigital(formData.value)
            emit('update')
            show.value = false
            router.navigateTo({
                path: '/packages/pages/digital_setting/digital_setting',
                query: {
                    id
                }
            })
        }
    })
}

watch(show, (value) => {
    if (value) {
        setTimeout(() => {
            uFormRef.value.setRules(rules)
        })
        formData.value = {
            image: '',
            name: '',
            avatar: ''
        }
    }
})
</script>
