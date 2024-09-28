<template>
    <view>
        <u-form-item
            label="启用形象"
            label-position="left"
            label-width="150"
            prop="is_digital"
        >
            <view class="flex-1">
                <view class="flex justify-end items-center">
                    <u-switch
                        v-model="formData.is_digital"
                        :active-value="1"
                        :inactive-value="0"
                        :size="40"
                    ></u-switch>
                    <view class="ml-[12rpx]">
                        {{ formData.is_digital == 1 ? '开启' : '关闭' }}
                    </view>
                </view>
            </view>
        </u-form-item>
        <!-- <u-form-item label="形象背景色" prop="digital_bg">
            <color-picker v-model="formData.digital_bg" />
        </u-form-item> -->
        <u-form-item
            label="选择形象"
            prop="digital_id"
            required
            v-if="formData.is_digital == 1"
        >
            <view class="flex-1 w-full flex flex-wrap mx-[-10rpx]">
                <view
                    class="w-[50%] p-[10rpx]"
                    v-for="item in digitalList"
                    :key="item.id"
                >
                    <view
                        class="rounded-[10rpx] h-[100rpx] flex items-center px-[30rpx] border border-solid border-light"
                        :class="{
                            '!bg-primary-light-7 !border-primary !text-primary':
                                item.id == formData.digital_id
                        }"
                        @click="formData.digital_id = item.id"
                    >
                        <u-avatar :size="64" :src="item.avatar"></u-avatar>
                        <view class="line-clamp-2 ml-[14rpx]">{{
                            item.name
                        }}</view>
                    </view>
                </view>
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { getDigitalList } from '@/api/digital'
import { ref } from 'vue'
const props = withDefaults(
    defineProps<{
        modelValue: Record<string, any>
    }>(),
    {}
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
}>()

const formData = useVModel(props, 'modelValue', emit)
const digitalList = ref<any[]>([])
const getAiModelList = async () => {
    const data = await getDigitalList({
        page_type: 0
    })
    digitalList.value = data.lists || []
}
getAiModelList()
</script>
