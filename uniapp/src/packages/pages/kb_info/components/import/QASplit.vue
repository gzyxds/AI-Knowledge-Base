<template>
    <view>
        <view class="mt-2">
            <app-file-picker @importFile="fileInput">
                <view
                    class="text-center py-[100rpx] rounded-lg border border-dashed border-info bg-[#FCFCFC] relative"
                >
                    <view>选择文件</view>
                    <view>支持 .txt,.docx ,.pdf ,.md文件</view>
                    <view
                        v-if="isLock"
                        class="absolute inset-0 flex justify-center items-center bg-[rgba(0,0,0,0.4)]"
                    >
                        <u-loading :size="50" :color="$theme.primaryColor" />
                    </view>
                </view>
            </app-file-picker>
        </view>
        <template v-if="data.length > 0">
            <view class="mt-2">
                <view class="py-[20rpx] font-bold"
                    >文件（{{ data.length }}）</view
                >
                <view class="px-[10rpx] py-[15rpx]">
                    <div
                        v-for="(item, index) in data"
                        :key="index"
                        class="fileItem flex items-center p-2 rounded-lg mt-1 hover:cursor-pointer bg-page transition duration-300"
                        :class="{
                            'bg-page border border-solid border-primary rounded-lg':
                                showIndex == index
                        }"
                        @click="selectShow(index)"
                    >
                        <div class="ml-2">
                            {{ item.name }}
                        </div>
                        <div
                            class="ml-auto flex items-center"
                            @click.stop="delFile(index)"
                        >
                            <u-icon
                                name="trash-fill"
                                color="#2979ff"
                                size="28"
                            ></u-icon>
                        </div>
                    </div>
                </view>
            </view>
            <!-- <view
                class="my-[40rpx] comsume py-[15rpx] text-center text-[#F7A40A]"
            >
                预计消耗：0.033积分
            </view> -->
            <view>
                <view class="py-[20rpx] font-bold"
                    >结果预览（{{ data[showIndex]?.data.length }}组）</view
                >
                <view class="px-[10rpx] pb-[20rpx]">
                    <div
                        class="bg-page rounded p-[10px] mt-2"
                        v-for="(item, index) in data[showIndex]?.data"
                        :key="index"
                    >
                        <view class="flex items-center">
                            <span class="bg-white px-4 rounded">
                                #{{ index + 1 }}
                            </span>
                            <span class="mx-2 text-[#000] line-clamp-1 flex-1">
                                {{ data[showIndex].name }}
                            </span>
                            <div
                                class="p-[6rpx] text-content"
                                @click.stop="handleDelete(index)"
                            >
                                <u-icon name="trash-fill" size="28"></u-icon>
                            </div>
                        </view>
                        <div class="mt-2 whitespace-pre-line">
                            {{ item.q }}
                        </div>
                    </div>
                </view>
            </view>
        </template>
    </view>
</template>

<script setup lang="ts">
import { type IDataItem, isSameFile } from './hook'
import {
    readDocContent,
    readPdfContent,
    readTxtContent
} from '@/utils/fileReader'
import { ref, reactive } from 'vue'
import { useVModel } from '@vueuse/core'
import { useKB } from '../../useKb'
import { useLockFn } from '@/hooks/useLockFn'
import { splitText2ChunksArray } from '@/utils/textSplitter'
const { KBInfo } = useKB()

const props = defineProps<{
    modelValue: IDataItem[]
}>()
const emit = defineEmits(['update:modelValue'])
const data = useVModel(props, 'modelValue', emit)

const fileList = ref<File[]>([])
const fileAccept = ['.txt', '.docx', '.pdf', '.md']
const accept = fileAccept.join(', ')

//预览
const showIndex = ref(-1)

const fileToData = async (file: any) => {
    try {
        // loading.value = true
        await isSameFile(file, fileList.value)
        const content = await parseFile(file)
        if (!content) {
            throw '解析结果为空，已自动忽略'
        }

        data.value.push({
            name: file.name,
            path: '',
            data: []
        })
        //@ts-ignore
        file.data = content

        fileList.value.push(file)
        selectShow(fileList.value.length - 1)
        reSplit()
    } catch (error: any) {
        console.log(error)

        // feedback.msgError(error)
    } finally {
        // loading.value = false
        // uploadRef.value?.clearFiles()
    }
}
const { lockFn: fileInput, isLock } = useLockFn(async (fileData: any) => {
    const promise = fileData.tempFiles.map(async (item: any) => {
        await fileToData(item.file)
    })
    await Promise.all(promise)
})

const reSplit = () => {
    data.value.forEach((item: any) => {
        item.data = []
        const index = fileList.value.findIndex(
            (fileItem) => fileItem.name == item.name
        )

        const contentList = splitText2ChunksArray({
            text: fileList.value[index].data,
            chunkLen: KBInfo.value.qa_length
        })

        contentList.forEach((contentListItem) => {
            item.data.push({ q: contentListItem, a: '' })
        })
    })
}

const handleDelete = async (index: any) => {
    data.value[showIndex.value].data.splice(index, 1)
}

const parseFile = async (file: File) => {
    const suffix = file.name.substring(file.name.lastIndexOf('.') + 1)
    let res = ''
    switch (suffix) {
        case 'md':
        case 'txt':
            res = await readTxtContent(file)
            break
        case 'pdf':
            res = await readPdfContent(file)
            break
        case 'doc':
        case 'docx':
            res = await readDocContent(file)
            break
        default:
            res = await readTxtContent(file)
            break
    }
    return res
}

//选择预览文件
const selectShow = (index: number) => {
    showIndex.value = index
}

//删除文件
const delFile = (index: number) => {
    data.value.splice(index, 1)
    fileList.value.splice(index, 1)
}
</script>

<style scoped lang="scss">
.comsume {
    background: linear-gradient(
        90deg,
        #fff0e000 0%,
        #fff0e0 49.32%,
        #fff0e000 100%
    );
}
</style>
