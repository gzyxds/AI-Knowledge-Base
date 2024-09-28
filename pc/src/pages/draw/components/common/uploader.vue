<template>
    <div>
        <sidbar-item-title
            title="上传参考图"
            required
            tips="上传一张图片做为基底，用模型在其上面重新生成新的图片"
        />
        <el-upload
            v-model:file-list="fileList"
            class="uploader"
            drag
            :multiple="false"
            :show-file-list="false"
            :on-success="handleSuccess"
            :http-request="httpRequest"
            :before-upload="beforeUpload"
            :accept="getAccept"
        >
            <div>
                <div
                    class="flex justify-center items-center h-[150px] relative"
                    v-if="fileData"
                >
                    <el-image
                        class="!block h-[100%]"
                        :src="fileData"
                        fit="contain"
                    />
                    <Icon
                        class="!absolute right-0 top-0 z-10 drop-shadow"
                        name="el-icon-CircleCloseFilled"
                        color="#ffffff"
                        @click.stop="fileData = ''"
                    ></Icon>
                </div>
                <div class="uploader-container" v-else>
                    <img
                        src="../../../../assets/image/draw/uploader.png"
                        alt="文件上传"
                        class="w-8 mx-auto mb-2"
                    />
                    <div class="el-upload__text text-[#798696] !text-[13px]">
                        拖拽文件到此处或者<em>点击上传</em>
                    </div>
                    <div class="el-upload__tip text-[#798696]">
                        {{
                            parseInt(config.file_size) > 0
                                ? `支持图片格式：JPG/JPEG/PNG，低于${config.file_size}MB`
                                : `支持图片格式：JPG/JPEG/PNG`
                        }}
                    </div>
                </div>
            </div>
        </el-upload>
    </div>
</template>

<script lang="ts" setup>
import type {
    ElUpload,
    UploadRequestOptions,
    UploadUserFile
} from 'element-plus'

import { useVModels } from '@vueuse/core'
import { uploadFile } from '~/api/app'
import { config, checkUserLogin } from '../../hooks/useDrawEffect'

import sidbarItemTitle from './sidbar-item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: string
        type?: 'image' | 'video' | 'audio'
        files?: UploadUserFile[]
    }>(),
    {
        modelValue: ''
    }
)
const { modelValue: fileData } = useVModels(props, emit)

const fileList = ref<UploadUserFile[]>([])

const beforeUpload = (file: File) => {
    if (checkUserLogin()) return false
    if (
        parseInt(config.value.file_size) > 0 &&
        file.size > parseInt(config.value.file_size) * 1024 * 1024
    ) {
        ElMessage.error(`文件大小不能超过${config.value.file_size}MB`)
        return false
    }
    return true
}

const handleSuccess = (response: any) => {
    emit('update:modelValue', response.uri)
    fileData.value = response.uri
    fileList.value = [
        {
            name: response.name,
            url: response.uri
        }
    ]
}

const httpRequest = (options: UploadRequestOptions) => {
    return uploadFile(props.type as any, {
        file: options.file,
        name: 'file',
        header: {},
        data: {
            type: 'draw'
        }
    })
}

const getAccept = computed(() => {
    switch (props.type) {
        case 'image':
            return '.jpg,.png,.jpeg'
        case 'video':
            return '.wmv,.avi,.mpg,.mpeg,.3gp,.mov,.mp4,.flv,.rmvb,.mkv'
        case 'audio':
            return
        default:
            return '*'
    }
})
</script>

<style lang="scss" scoped>
.uploader {
    :deep(.el-upload) {
        .el-upload-dragger {
            background-color: var(--el-bg-color-page);
            padding: 0;
            border-radius: 12px;
            .uploader-container {
                padding: 26px 0;
            }
        }
    }
}
</style>
