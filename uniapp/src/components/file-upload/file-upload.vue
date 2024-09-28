<template>
    <view
        class="file-upload"
        :class="{
            'file-upload--line': limitLength === 1
        }"
    >
        <view class="file-picker">
            <view @click="choose">
                <slot>
                    <view class="file-button">
                        <u-icon
                            name="arrow-upward"
                            :size="32"
                            v-if="fileType === 'file'"
                        />
                        <u-icon name="camera" :size="32" v-else />
                        <view class="ml-[10rpx]">
                            {{ getBtnText }}
                        </view>
                    </view>
                </slot>
            </view>
        </view>

        <view class="file-list" v-if="showFilesLists">
            <view
                class="file-item relative"
                v-for="item in filesLists"
                :key="item.url"
            >
                <view class="text-primary mr-[10rpx] flex">
                    <u-icon
                        v-if="fileType == 'file'"
                        :size="32"
                        name="file-text"
                    />
                    <image
                        v-if="fileType == 'image'"
                        :src="item.path || item.url"
                        class="w-[32rpx] h-[32rpx]"
                    />
                    <image
                        v-if="fileType == 'video'"
                        :src="item.path || item.url"
                        class="w-[32rpx] h-[32rpx]"
                    />
                </view>
                <view class="flex-1 min-w-0 mr-[20rpx]">
                    <view class="line-clamp-1">
                        {{ item.name }}
                    </view>
                </view>
                <view
                    v-if="!disabled"
                    @click.stop="removeFile(item.url!)"
                    class="relative flex items-center z-10"
                >
                    <u-icon name="close" :size="28" />
                </view>
                <view
                    class="absolute bottom-[0px] w-full left-0 flex"
                    v-if="
                        showProgress 
                        && item.progress! >= 0 
                        && item.progress 
                        && item.status !== 'success'
                    "
                >
                    <u-line-progress
                        class="w-full h-auto"
                        height="6"
                        :show-percent="false"
                        :active-color="$theme.primaryColor"
                        :percent="item.progress"
                    ></u-line-progress>
                </view>
                <view
                    v-if="item.status === 'error'"
                    class="absolute inset-0 bg-[rgba(0,0,0,0.4)] flex items-center justify-center text-white"
                >
                    <view @click="retry(item)"> 点击重试 </view>
                </view>
            </view>
        </view>
    </view>
</template>
<script lang="ts" setup>
import { PropType, computed, ref, watch } from 'vue'
import {
    ChooseResult,
    FileData,
    chooseFile,
    getFileName,
    getFilesByExtname,
    normalizeFileData
} from './choose-file'
import { uploadFileByType } from '@/api/app'
const props = defineProps({
    modelValue: {
        type: [Array, Object],
        default() {
            return []
        }
    },
    limit: {
        type: Number,
        default: 10
    },
    disabled: {
        type: Boolean,
        default: false
    },
    // 文件类型
    fileType: {
        type: String as PropType<'image' | 'video' | 'file'>,
        default: 'all'
    },
    returnType: {
        type: String as PropType<'object' | 'array'>,
        default: 'array'
    },
    // 文件类型筛选
    fileExtname: {
        type: Array as PropType<string[]>,
        default() {
            return []
        }
    },
    data: {
        type: Object,
        default() {
            return {}
        }
    },
    header: {
        type: Object,
        default() {
            return {}
        }
    },
    sizeType: {
        type: Array as PropType<string[]>,
        default() {
            return ['original', 'compressed']
        }
    },
    sourceType: {
        type: Array as PropType<string[]>,
        default() {
            return ['album', 'camera']
        }
    },
    showProgress: {
        type: Boolean,
        default: true
    },
    //最大上传量
    maxCount: {
        type: Number,
        default: 2
    },
    showFilesLists: {
        type: Boolean,
        default: true
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()
const filesLists = ref<Partial<FileData>[]>([])
const limitLength = computed(() => {
    if (props.returnType === 'object') {
        return 1
    }
    if (!props.limit) {
        return 1
    }

    return props.limit
})

const choose = async () => {
    if (props.disabled) return
    if (
        filesLists.value.length >= Number(limitLength.value) &&
        props.returnType === 'array' &&
        limitLength.value > 1
    ) {
        uni.showToast({
            title: `您最多选择 ${limitLength.value} 个文件`,
            icon: 'none'
        })
        return
    }
    const filesResult = await chooseFile({
        type: props.fileType,
        compressed: false,
        sizeType: props.sizeType,
        sourceType: props.sourceType,
        extension: props.fileExtname.length ? props.fileExtname : undefined,
        count: limitLength.value - filesLists.value.length
    })
    chooseFileCallback(filesResult)
}

const chooseFileCallback = async (filesResult: ChooseResult) => {
    const isOne = Number(limitLength.value) === 1
    if (isOne) {
        filesLists.value = []
    }
    const { files } = getFilesByExtname(filesResult, props.fileExtname)
    const currentData = []
    for (let i = 0; i < files.length; i++) {
        if (limitLength.value - filesLists.value.length <= 0) break
        const filedata = normalizeFileData(files[i])
        filesLists.value.push(filedata)
        currentData.push(filedata)
    }
    await upload(currentData)
    emitUpdateValue()
}

const emitUpdateValue = () => {
    let value: any = {}
    if (props.returnType === 'object') {
        const [item] = filesLists.value
        if (item.status === 'success') {
            value = {
                url: item.url,
                name: item.name
            }
        }
    } else {
        const data = filesLists.value.filter(
            (item) => item.status === 'success'
        )
        value = data.map((item) => ({
            url: item.url,
            name: item.name
        }))
    }
    emit('update:modelValue', value)
}

//上传，并处理并发问题
const upload = (files: FileData[]): Promise<void> => {
    const len = files.length
    let index = 0
    let count = 0
    return new Promise((resolve) => {
        const run = async () => {
            const cur = index++
            const fileItem = files[cur]
            const currentIndex = filesLists.value.findIndex(
                (item) => item.path === fileItem.path
            )
            try {
                const { uri }: any = await uploadFileByType(
                    props.fileType,
                    {
                        filePath: fileItem.url,
                        formData: props.data,
                        header: props.header
                    },
                    (progress) => {
                        filesLists.value[currentIndex].progress = progress
                    }
                )

                filesLists.value[currentIndex].status = 'success'
                filesLists.value[currentIndex].url = uri
            } catch (error) {
                filesLists.value[currentIndex].errMsg = error as string
                filesLists.value[currentIndex].status = 'error'
            }
            count++
            if (count === len) {
                resolve()
                return
            }
            if (index < len) {
                run()
            }
        }
        for (let i = 0; i < Math.min(len, props.maxCount); i++) {
            run()
        }
    })
}

const removeFile = (url: string) => {
    const index = filesLists.value.findIndex((item) => item.url === url)
    if (index > -1) {
        filesLists.value.splice(index, 1)
        emitUpdateValue()
    }
}

const retry = async (item: any) => {
    item.status = 'ready'
    item.progress = 0
    await upload([{ ...item }])
}

const getBtnText = computed(() => {
    switch (props.fileType) {
        case 'image':
            return '上传图片'
        case 'video':
            return '上传视频'
        default:
            return '上传文件'
    }
})
const setValueItem = (item: any) => {
    if (!item.url) return
    const isInFiles = filesLists.value.some((file) => file.url == item.url)
    if (!isInFiles) {
        if (!item.name) {
            item.name = getFileName(item.url)
        }
        item.status = 'success'
        filesLists.value.push({ ...item })
    }
}
watch(
    () => props.modelValue,
    (newVal) => {
        if (Array.isArray(newVal)) {
            newVal.forEach((item: any) => {
                setValueItem(item)
            })
        } else {
            if (!newVal.url) {
                filesLists.value = []
            }
            setValueItem(newVal)
        }
    },
    {
        immediate: true
    }
)

const clear = () => {
    filesLists.value = []
}

defineExpose({
    clear
})
</script>

<style lang="scss">
.file-upload {
    // display: flex;
    &.file-upload--line {
        display: flex;
        align-items: center;
        .file-list {
            .file-item {
                margin-top: 0;
            }
        }
    }
    .file-picker {
        display: flex;
        margin-right: 20rpx;
        flex: none;
        .file-button {
            display: flex;
            align-items: center;
            padding: 15rpx 20rpx;
            border-radius: 10rpx;
            background: #fff;
            box-shadow: 0 0 10px #e6e9ed;
            @apply text-primary text-xs;
        }
    }
    .file-list {
        flex: 1;
        min-width: 0;
        .file-item {
            padding: 15rpx 20rpx;
            border-radius: 10rpx;
            background: #f7f7f7;
            font-size: 26rpx;
            display: flex;
            align-items: center;
            margin-top: 15rpx;
            overflow: hidden;
        }
    }
}
</style>
