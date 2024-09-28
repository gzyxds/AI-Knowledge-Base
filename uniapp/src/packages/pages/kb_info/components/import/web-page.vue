<template>
    <view class="web-page">
        <view class="py-[20rpx]">
            <view class="flex-1">
                <!-- <el-input
                            v-model="url"
                            :placeholder="`请输入要解析的网页链接，添加多个请按回车键分隔`"
                            type="textarea"
                            resize="none"
                            :rows="6"
                        /> -->
                <u-input
                    v-model="url"
                    type="textarea"
                    :maxlength="-1"
                    border
                    :height="200"
                    :auto-height="false"
                    placeholder="请输入要解析的网页链接，添加多个请按回车键分隔"
                >
                </u-input>
            </view>
            <view class="inline-flex mt-[20rpx]">
                <u-button
                    type="primary"
                    :loading="isLock"
                    @click="parseUrl"
                    :customStyle="{
                        width: '180rpx'
                    }"
                >
                    解析
                </u-button>
            </view>
            <view>
                <view
                    v-for="(item, index) in formData"
                    :key="index"
                    class="mt-[20rpx]"
                >
                    <view class="mb-[10rpx] font-medium text-lg">
                        #{{ index + 1 }}
                        {{ item.name }}

                        <u-icon
                            class="text-muted ml-[20rpx]"
                            name="trash"
                            @click="handleDelete(item)"
                        />
                    </view>
                    <template v-for="(data, index) in item.data" :key="index">
                        <u-input
                            v-model="data.q"
                            type="textarea"
                            :maxlength="-1"
                            border
                            :height="300"
                            :auto-height="false"
                            placeholder="文件内容，空内容会自动省略"
                        >
                        </u-input>
                    </template>
                </view>
            </view>
        </view>
    </view>
</template>
<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import type { IDataItem } from './hook'
import { webHtmlCapture } from '@/api/kb'
import { ref } from 'vue'
import { useLockFn } from '@/hooks/useLockFn'

const props = defineProps<{
    modelValue: IDataItem[]
}>()

const emit = defineEmits<{
    (event: 'update:modelValue', value: IDataItem): void
}>()
const formData = useVModel(props, 'modelValue', emit)
const url = ref('')

const handleDelete = async (item: IDataItem) => {
    const index = formData.value.indexOf(item)
    if (index !== -1) {
        formData.value.splice(index, 1)
    }
}

const { lockFn: parseUrl, isLock } = useLockFn(async () => {
    if (!url.value) return uni.$u.toast('请输入网页链接')
    const data = await webHtmlCapture({
        url: url.value.split('\n').filter(Boolean)
    })
    formData.value = [
        ...data.map((item: any) => ({
            data: [
                {
                    a: '',
                    q: item.content
                }
            ],
            path: '',
            name: item.url
        })),
        ...formData.value
    ]
    url.value = ''
})
</script>

<style scoped lang="scss"></style>
