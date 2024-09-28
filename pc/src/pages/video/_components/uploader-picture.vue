<template>
    <el-form-item prop="image" required>
        <template #label>
            <span class="font-bold text-tx-primary"> 上传参考图 </span>
        </template>
        <div class="flex-1 leading-snug">
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
                            src="@/assets/image/draw/uploader.png"
                            alt="文件上传"
                            class="w-8 mx-auto mb-2"
                        />
                        <div
                            class="el-upload__text text-[#798696] !text-[13px]"
                        >
                            拖拽文件到此处或者<em>点击上传</em>
                        </div>
                        <div class="el-upload__tip text-[#798696]">
                            {{ `支持图片格式：JPG/JPEG/PNG，低于${size}MB` }}
                        </div>
                    </div>
                </div>
            </el-upload>
        </div>
    </el-form-item>
</template>

<script lang="ts" setup>
import type {
    ElUpload,
    UploadRequestOptions,
    UploadUserFile
} from 'element-plus'

import { useVModels } from '@vueuse/core'
import { uploadFile } from '@/api/app'

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
        modelValue: '',
        type: 'image'
    }
)
const { modelValue: fileData } = useVModels(props, emit)

const fileList = ref<UploadUserFile[]>([])
const size = 10
const beforeUpload = (file: File) => {
    if (file.size > size * 1024 * 1024) {
        ElMessage.error(`文件大小不能超过${size}MB`)
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
        header: {}
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
