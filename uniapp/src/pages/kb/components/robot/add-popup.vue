<template>
    <u-popup v-model="show" closeable border-radius="16" mode="bottom">
        <view class="h-[70vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                新增智能体
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
                                label="智能体图标"
                                prop="image"
                                required
                            >
                                <view class="mx-[-10rpx] flex-1 min-w-0">
                                    <app-upload v-model="formData.image" />
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="智能体名称"
                                prop="name"
                                required
                            >
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.name"
                                        placeholder="给你的智能体取个响亮的名字"
                                        :border="true"
                                    />
                                </view>
                            </u-form-item>
                            <u-form-item label="智能体简介" prop="intro">
                                <view class="flex-1">
                                    <u-input
                                        v-model="formData.intro"
                                        type="textarea"
                                        :height="200"
                                        placeholder="请简单描述下给你的智能体"
                                        :border="true"
                                    />
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
import { postRobot } from '@/api/robot'
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
    intro: ''
})
const rules = {
    image: [
        {
            required: true,
            message: '请选择智能体图标',
            trigger: ['change']
        }
    ],
    name: [
        {
            required: true,
            message: '请输入智能体名称',
            trigger: ['change']
        }
    ]
}

const handelSave = () => {
    uFormRef.value.validate(async (valid: boolean) => {
        if (valid) {
            const { id } = await postRobot(formData.value)
            emit('update')

            setTimeout(() => {
                show.value = false
                router.navigateTo({
                    path: '/packages/pages/robot_info/robot_info',
                    query: {
                        id
                    }
                })
            }, 1000)
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
            intro: ''
        }
    }
})
</script>
