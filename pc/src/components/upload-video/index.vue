<template>
    <div v-if="isClient">
        <div class="flex">
            <ElUpload
                v-loading="loading"
                element-loading-text="上传中..."
                ref="uploadRef"
                class="avatar-uploader flex"
                :show-file-list="false"
                :limit="1"
                :on-change="handleChange"
                :auto-upload="false"
                accept=".wmv,.avi,.mpg,.mpeg,.3gp,.mov,.mp4,.flv,.rmvb,.mkv"
            >
                <slot>
                    <div
                        v-if="!value"
                        class="el-upload flex-col bg-fill-lighter"
                    >
                        <Icon name="el-icon-Plus" :size="20" />
                        <div class="text-tx-secondary mt-[2px]">添加视频</div>
                    </div>
                    <div v-if="!!value" class="imgContiner relative">
                        <div
                            class="border border-solid border-br-light rounded-[6px] relative cursor-pointer"
                            :style="{
                                width: size,
                                height: size
                            }"
                        >
                            <video
                                class="rounded-lg w-full h-full"
                                :src="value"
                            />
                            <div
                                class="z-[10px] absolute left-1/2 top-1/2 translate-x-[-50%] translate-y-[-50%] rounded-full w-5 h-5 flex justify-center items-center bg-[rgba(0,0,0,0.3)]"
                                @click.stop="visible = true"
                            >
                                <icon
                                    name="el-icon-CaretRight"
                                    :size="18"
                                    color="#fff"
                                />
                            </div>
                        </div>

                        <div
                            v-if="canClose"
                            class="icon absolute top-[-10px] right-[-10px] text-tx-secondary"
                            @click.stop="value = ''"
                        >
                            <Icon size="20" name="el-icon-CircleCloseFilled" />
                        </div>
                    </div>
                </slot>
            </ElUpload>
        </div>
        <el-dialog v-model="visible" width="740px" title="视频预览">
            <video-player
                ref="playerRef"
                :src="value"
                width="100%"
                height="450px"
            />
        </el-dialog>
    </div>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { uploadFile } from '@/api/app'

const emits = defineEmits(['change', 'update:modelValue'])

const { getImageUrl } = useAppStore()

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    excludeDomain: {
        type: Boolean,
        default: false
    },
    canClose: {
        type: Boolean,
        default: true
    },
    size: {
        type: String,
        default: '100px'
    }
})
const playerRef = shallowRef()
const visible = ref(false)
const isClient = ref(false)

watch(visible, (value) => {
    if (value) {
        nextTick(() => {
            playerRef.value?.play()
        })
    } else {
        nextTick(() => {
            playerRef.value?.pause()
        })
    }
})

const loading = ref(false)

const value = computed({
    get() {
        return props.excludeDomain
            ? getImageUrl(props.modelValue)
            : props.modelValue
    },
    set(value) {
        emits('update:modelValue', value)
    }
})
//上传组件ref
const uploadRef = shallowRef()

const handleChange = async ({ raw }: any) => {
    try {
        loading.value = true
        const data = await uploadFile('video', { file: raw })
        loading.value = false
        value.value = props.excludeDomain ? data.url : data.uri
        emits('change', data.uri)
        uploadRef.value?.clearFiles()
    } catch (error) {
        loading.value = false
        uploadRef.value?.clearFiles()
    }
}

onMounted(() => {
    isClient.value = true
})
</script>

<style lang="scss" scoped>
.avatar-uploader .el-upload {
    width: v-bind(size);
    height: v-bind(size);
    border: 1px dashed #dddddd;
    border-radius: 6px;
    cursor: pointer;
    // position: relative;
    // overflow: hidden;
    // transition: var(--el-transition-duration-fast);
}

.avatar-uploader .el-upload:hover {
    border-color: var(--el-color-primary);
}

.el-icon.avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    text-align: center;
}

.imgContiner {
    .icon {
        opacity: 0;
        transition: all 0.2s linear;
    }

    &:hover {
        .icon {
            opacity: 1;
        }
    }
}
</style>