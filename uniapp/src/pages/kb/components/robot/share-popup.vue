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
                    {{ formData.cate_name ? formData.cate_name : '全部' }}
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
import { getAgentCategoryList, shareAgent } from '@/api/task_reward'
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
    cate_id: string,
    id: string,
    cate_name: string
}>({
    cate_id: '',
    id: '',
    cate_name: ''
})

const getData = async () => {
    try {
        const list = await getAgentCategoryList()
        list.unshift({ name: '全部', id: '' })
        cateLists.value = list
    } catch (error) {
        console.log('获取机器人分类失败=>', error)
    }
}

//提交
const { lockFn: handleSubmit, isLock } = useLockFn(async () => {
    await shareAgent(formData)
    show.value = false
    emit('success', formData.id)
})

const handleChoiceCate = (e: any) => {
    formData.cate_id = e[0].value
    formData.cate_name = e[0].label
}

const open = (id: string) => {
    getData()
    show.value = true
    formData.id = id
}

const close = () => {
    emit('close')
}

defineExpose({ open })
</script>
