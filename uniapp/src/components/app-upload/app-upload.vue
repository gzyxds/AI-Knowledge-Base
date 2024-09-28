<template>
    <uv-upload
        v-bind="props"
        :useBeforeRead="true"
        :fileList="filesLists"
        :maxCount="count"
        @afterRead="afterRead"
        @delete="deletePic"
    >
        <slot />
        111
    </uv-upload>
</template>

<script setup lang="ts">
import request from '@/utils/request'
import { computed, ref, watch } from 'vue'
import { filterFile } from './utils'
import { isObject, isString, isArray } from 'lodash-es'
type FileValueItem = string | { url: string }
const props = withDefaults(
    defineProps<{
        modelValue: FileValueItem | FileValueItem[]
        /**
         * file只支持H5（只有微信小程序才支持把accept配置为all、media）
         */
        accept?: 'all' | 'media' | 'image' | 'file' | 'video'
        /**
         * 图片或视频拾取模式，当accept为image类型时设置capture可选额外camera可以直接调起摄像头
         */
        capture?: string[] | string
        /**
         * 当accept为video时生效，是否压缩视频，默认为true
         */
        compressed?: boolean
        /**
         * 当accept为video时生效，可选值为back或front
         */
        camera?: string
        /**
         * 当accept为video时生效，拍摄视频最长拍摄时间，单位秒
         */
        maxDuration?: number
        /**
         * 上传区域的图标，只能内置图标
         */
        uploadIcon?: string
        previewFullVideo?: boolean
        previewFullImage?: boolean
        maxCount?: number
        maxUploadCount?: number
        disabled?: boolean
        /**
         * 预览上传的图片时的裁剪模式，和image组件mode属性一致
         */
        imageMode: string
        width?: number | string
        height?: number | string
        header?: Record<string, any>
        data?: Record<string, any>
        multiple?: boolean
        deletable?: boolean
        returnType: 'object' | 'string' | 'object-array' | 'string-array'
        /**
         * @example 'png,jpeg', ['png'], '*'(相当于[])
         */
        extname?: string | string[]
        previewImage?: boolean
    }>(),
    {
        accept: 'image',
        capture: () => ['album', 'camera'],
        compressed: true,
        camera: 'back',
        maxDuration: 120,
        uploadIcon: 'camera-fill',
        previewFullImage: true,
        previewFullVideo: true,
        maxCount: 1,
        disabled: false,
        imageMode: 'aspectFill',
        multiple: true,
        width: '180rpx',
        height: '180rpx',
        header: () => ({}),
        data: () => ({}),
        deletable: true,
        maxUploadCount: 9,
        returnType: 'string',
        previewImage: true
    }
)

const emit = defineEmits<{
    (event: 'update:modelValue', modelValue: string | string[]): void
}>()

const filesLists = ref<any[]>([])
const deletePic = (event: any) => {
    filesLists.value.splice(event.index, 1)
    updateValue()
}

const count = computed(() => {
    if (props.returnType == 'object' || props.returnType == 'string') {
        return 1
    }
    return props.maxCount
})
const fileExtname = computed(() => {
    let extname = props.extname
    if (!extname) {
        switch (props.accept) {
            case 'image': {
                extname = 'jpg,png,gif,jpeg'
                break
            }
            case 'video': {
                extname = 'wmv,avi,mpg,mpeg,3gp,mov,mp4,flv,rmvb,mkv'
                break
            }
            default: {
                extname = '*'
            }
        }
    }
    if (isString(extname)) {
        if (extname == '*') {
            return []
        } else {
            return extname.split(',')
        }
    } else if (Array.isArray(extname)) {
        return extname
    } else {
        return []
    }
})
const afterRead = async ({ file }: any) => {
    let index = 0
    let count = 0
    const files = filterFile(file, fileExtname.value)
    const len = files.length
    files.forEach((item) => {
        filesLists.value.push({
            ...item,
            status: 'uploading',
            message: '上传中'
        })
    })
    const run = async () => {
        const cur = index++
        const fileItem = files[cur]

        const current = filesLists.value.find(
            (item) => item.url === fileItem.url
        )
        try {
            const url = await upload(fileItem.url)
            current.status = 'success'
            current.url = url
        } catch (error) {
            current.status = 'failed'
            current.message = '上传失败'
        }

        count++
        if (count === len) {
            console.log(filesLists.value)
            updateValue()
            return
        }
        if (index < len) {
            run()
        }
    }
    for (let i = 0; i < Math.min(len, props.maxUploadCount); i++) {
        run()
    }
}

const upload = async (path: string) => {
    let type = props.accept
    if (type == 'all') {
        type = 'file'
    }

    const data = await request.uploadFile({
        url: `/upload/${type}`,
        filePath: path,
        name: 'file',
        header: props.header,
        formData: props.data
    })
    return data.uri
}

const updateValue = () => {
    let value: any = ''
    switch (props.returnType) {
        case 'string': {
            const [item] = filesLists.value
            if (item?.status === 'success') {
                value = item.url
            } else {
                value = ''
            }
            break
        }
        case 'object': {
            const [item] = filesLists.value
            if (item?.status === 'success') {
                value = {
                    url: item.url,
                    name: item.name
                }
            } else {
                value = {}
            }
            break
        }
        case 'object-array': {
            const data = filesLists.value.filter(
                (item) => item.status === 'success'
            )
            value = data.map((item) => ({
                url: item.url,
                name: item.name
            }))
            break
        }
        case 'string-array': {
            const data = filesLists.value.filter(
                (item) => item.status === 'success'
            )
            value = data.map((item) => item.url)
            break
        }
    }
    emit('update:modelValue', value)
}

const setValueItem = (item: any) => {
    if (!item.url) return
    const isInFiles = filesLists.value.some((file) => file.url == item.url)
    console.log(filesLists.value)
    if (!isInFiles) {
        item.status = 'success'
        filesLists.value.push({ ...item })
    }
}

watch(
    () => props.modelValue,
    (newVal) => {
        if (isString(newVal)) {
            if (!newVal) {
                filesLists.value = []
                return
            }
            filesLists.value = [{ url: newVal }]
        } else if (isArray(newVal)) {
            if (!newVal.length) {
                filesLists.value = []
                return
            }
            newVal.forEach((item: any) => {
                if (isString(item)) {
                    setValueItem({ url: item })
                } else {
                    setValueItem(item)
                }
            })
        } else if (isObject(newVal)) {
            if (!newVal.url) {
                filesLists.value = []
                return
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
