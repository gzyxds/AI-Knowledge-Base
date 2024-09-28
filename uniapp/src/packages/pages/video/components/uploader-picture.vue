<template>
    <u-form-item prop="image" label="上传参考图" required>
        <view class="flex-1 leading-snug">
            <uni-file-picker
                ref="filePickerRef"
                limit="1"
                fileMediatype="image"
                @select="selectFile"
                mode="list"
                :list-styles="{ display: 'none' }"
                :auto-upload="false"
            >
                <view
                    class="bg-white rounded-[7rpx] h-[300rpx] border-light border border-dashed relative overflow-hidden"
                >
                    <view
                        class="flex justify-center items-center relative h-full"
                        v-if="fileData"
                    >
                        <image
                            class="w-full h-[100%]"
                            :src="files.url || fileData"
                            mode="aspectFit"
                        />
                        <view
                            class="!absolute right-0 top-0 z-10 text-muted"
                            @click.stop="fileData = ''"
                        >
                            <u-icon
                                name="close-circle-fill"
                                :size="50"
                            ></u-icon>
                        </view>
                    </view>
                    <view
                        class="uploader-container flex justify-center items-center flex-col h-full"
                        v-else
                    >
                        <img
                            src="@/packages/static/images/image-uploader.png"
                            alt="文件上传"
                            class="w-[80rpx] h-[80rpx] mx-auto mb-2"
                        />
                        <view class="text-[#798696] !text-[13px]">
                            <text class="text-primary">点击上传</text>
                        </view>
                        <view class="text-[#798696] !text-[13px] mt-[10rpx]">
                            {{ `支持图片格式：JPG/JPEG/PNG，低于${size}MB` }}
                        </view>
                    </view>
                    <view
                        class="absolute top-0 left-0 w-full h-full bg-[rgba(0,0,0,0.5)] flex flex-col items-center justify-center"
                        v-if="isLock"
                    >
                        <view>
                            <u-loading size="40" mode="circle" />
                        </view>
                        <text class="text-white mt-[10rpx]"> 上传中... </text>
                    </view>
                </view>
            </uni-file-picker>
        </view>
    </u-form-item>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import { uploadFileByType } from '@/api/app'
import { computed, reactive, ref, shallowRef } from 'vue'
import { useLockFn } from '@/hooks/useLockFn'
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: string
        type?: 'image' | 'video' | 'audio'
    }>(),
    {
        modelValue: '',
        type: 'image'
    }
)
const { modelValue: fileData } = useVModels(props, emit)
const filePickerRef = shallowRef()
const files = ref<any>({})
const size = 10

const { lockFn: selectFile, isLock } = useLockFn(async (e: any) => {
    filePickerRef.value?.clearFiles()
    const [file] = e.tempFiles
    if (!file) return uni.$u.toast(`文件不存在`)
    if (file.size > size * 1024 * 1024) {
        uni.$u.toast(`文件大小不能超过${size}MB`)
        return
    }
    try {
        const data = await uploadFileByType(props.type as any, {
            filePath: file.url,
            name: 'file'
        })
        fileData.value = data.uri
        files.value = file
    } catch (error) {
        uni.$u.toast(error)
    }
})
</script>

<style lang="scss" scoped>
.uploader {
}
</style>
