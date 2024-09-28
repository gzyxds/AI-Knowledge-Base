<template>
    <view>
        <uni-file-picker
            ref="filePickerRef"
            v-model="fileList"
            :limit="limit"
            file-mediatype="all"
            mode="list"
            :del-icon="true"
            :list-styles="{ display: 'none' }"
            :fileExtname="fileExtname"
            :disabled="disabled"
            @retry="retry"
            @select="selectFile"
            @delete="deleteFile"
        >
            <slot />
        </uni-file-picker>
    </view>
</template>

<script lang="ts" setup>
import { uploadFile } from '@/api/app'
import { ref, shallowRef, watch } from 'vue'
const props = withDefaults(
    defineProps<{
        modelValue?: any[]
        fileExtname?: string[] | string
        data?: Record<string, any>
        header?: Record<string, any>
        limit?: number
        disabled?: boolean
    }>(),
    {
        modelValue: () => [],
        fileExtname: () => [],
        limit: 9,
        disabled: false,
        data: () => ({}),
        header: () => ({})
    }
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: any[]): void
}>()
const filePickerRef = shallowRef()
const fileList = ref<any[]>([])
const isCanSetFileList = ref(true)
const selectFile = async (e: any) => {
    uni.showLoading({
        title: '正在上传中。。。'
    })
    isCanSetFileList.value = false
    const { tempFilePaths } = e
    const task = tempFilePaths.map(async (path: string) => {
        await upload(path)
    })
    await Promise.all(task)
    uni.hideLoading()
    isCanSetFileList.value = true
}

const retry = async (e: any) => {
    uni.showLoading({
        title: '正在上传中，请稍后。。。'
    })
    isCanSetFileList.value = false
    const [{ item }] = e
    const path = item?.path
    const currentFile = filePickerRef.value?.filesList?.find(
        (item: any) => item.path == path
    )
    currentFile.progress = 0
    currentFile.status = 'ready'
    await upload(path)
    uni.hideLoading()
    isCanSetFileList.value = true
}

const upload = async (path: string) => {
    const currentFile = filePickerRef.value?.filesList?.find(
        (item: any) => item.path == path
    )
    try {
        const { uri, name } = await uploadFile(
            {
                filePath: path,
                formData: props.data,
                header: props.header
            },
            (progress) => {
                if (currentFile) {
                    currentFile.progress = progress
                }
            }
        )
        currentFile.status = 'success'
        currentFile.url = uri
        emit('update:modelValue', [
            ...props.modelValue.slice(),
            { url: uri, name }
        ])
    } catch (error) {
        if (currentFile) {
            currentFile.progress = 0
            currentFile.status = 'error'
        }
    }
}

const deleteFile = (e: any) => {
    isCanSetFileList.value = false
    const { tempFilePath } = e
    const deleteIdx = props.modelValue.findIndex(
        (item) => item.url == tempFilePath
    )
    if (deleteIdx > -1) {
        const result = props.modelValue.slice()
        result.splice(deleteIdx, 1)
        emit('update:modelValue', result)
    }
    setTimeout(() => {
        isCanSetFileList.value = true
    })
}
watch(
    () => props.modelValue,
    (value) => {
        if (isCanSetFileList.value) {
            fileList.value = value
        }
    },
    {
        immediate: true
    }
)
</script>
<style lang="scss" scoped></style>
