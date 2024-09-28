<template>
    <u-popup
        v-model="show"
        mode="center"
        border-radius="14"
        duration="16"
        @close="close"
    >
        <view class="w-[650rpx]">
            <view class="p-[30rpx] text-center text-lg font-medium">分享至广场</view>
            <view class="h-[230rpx] p-[30rpx]">
                <view
                    class="flex items-center justify-between rounded-[12rpx] p-[20rpx] text-content bg-[#eeeeee]"
                    @click="showSelect = true"
                >
                    {{ formData.category_name ? formData.category_name : '全部' }}
                    <u-icon name="arrow-right"></u-icon>
                </view>
            </view>
            <view class="flex justify-center mt-[30rpx] pb-[20rpx]">
                <u-button
                    type="primary"
                    :customStyle="{
                        width: '220rpx'
                    }"
                    :loading="isLock"
                    @click="handleSubmit"
                >
                    确定
                </u-button>
            </view>
        </view>
    </u-popup>
    <u-select
        v-model="showSelect"
        :list="cateLists"
        valueName="id"
        labelName="name"
        :confirmColor="$theme.primaryColor"
        @confirm="handleChoiceCate"
    ></u-select>
</template>

<script lang="ts" setup>
import { ref, reactive } from 'vue'
import { useAppStore } from '@/stores/app'
import { getSquareCategory, shareVideo } from '@/api/task_reward'
import { useLockFn } from '@/hooks/useLockFn'

const emit = defineEmits(['success', 'close'])

const appStore = useAppStore()
const show = ref<boolean>(false)
const showSelect = ref<boolean>(false)
const cateLists = ref<{
    name: string,
    id: string
    image: string
}[]>([])
const formData = reactive<{
    category_id: string,
    records_id: string,
    style_name: string
}>({
    category_id: '',
    records_id: '',
    style_name: ''
})

const getData = async () => {
    try {
        const list = await getSquareCategory({
            type: 3,
            share: 1
        })
        list.unshift({ name: '全部', id: '' })
        cateLists.value = list
    } catch (error) {
        console.log('获取视频分类失败=>', error)
    }
}

//提交
const { lockFn: handleSubmit, isLock } = useLockFn(async () => {
    await shareVideo(formData)
    show.value = false
    emit('success', formData.records_id)
})

const handleChoiceCate = (e: any) => {
    formData.category_id = e[0].value
    formData.category_name = e[0].label
}

const open = (records_id: string) => {
    getData()
    show.value = true
    formData.records_id = records_id
}

const close = () => {
    emit('close')
}

defineExpose({ open })
</script>
