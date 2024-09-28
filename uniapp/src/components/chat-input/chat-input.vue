<template>
    <view class="send-area">
        <view :class="[safeAreaInsetBottom ? 'safe-area-inset-bottom' : '']">
            <view class="float-btn">
                <view
                    v-if="showStop !== undefined ? showStop : loading"
                    class="px-[20rpx] py-[10rpx] text-xs flex items-center"
                    @click="emit('pause')"
                >
                    <u-icon name="pause-circle" class="mr-[8rpx]" size="36" />
                    停止
                </view>
                <view
                    v-if="!loading && showContinue"
                    class="px-[20rpx] py-[10rpx] text-xs flex items-center"
                    @click="emit('continue')"
                >
                    <u-icon name="play-circle" class="mr-[8rpx]" size="36" />
                    继续
                </view>
            </view>
            <view class="flex mb-[20rpx]" v-if="filePlugin.url">
                <view
                    class="flex bg-page p-[20rpx] rounded-[16rpx] items-center"
                >
                    <image
                        :src="filePlugin.url"
                        class="w-[60rpx] h-[60rpx] flex-none"
                    />
                    <text
                        class="line-clamp-2 ml-[20rpx] flex-1 min-w-0"
                        :style="{
                            'word-break': 'break-word'
                        }"
                    >
                        {{ filePlugin.name }}
                    </text>
                    <view class="pl-[20rpx] flex">
                        <u-icon
                            name="close-circle"
                            :size="38"
                            @click="filePlugin.url = ''"
                        />
                    </view>
                </view>
            </view>
            <view class="mb-[20rpx] flex items-center">
                <view class="flex-1 min-w-0 mr-[20rpx]">
                    <slot name="actions" />
                </view>
                <view class="mr-[20rpx]">
                    <uni-file-picker
                        v-if="showFileUpload"
                        ref="filePickerRef"
                        :limit="1"
                        fileMediatype="image"
                        @select="httpRequest"
                        mode="list"
                        :list-styles="{ display: 'none' }"
                        :auto-upload="false"
                    >
                        <view
                            class="text-base text-primary flex items-center bg-[#ECF6FF] py-[12rpx] px-[24rpx] rounded-full"
                        >
                            <u-loading
                                v-if="fileUploadLoading"
                                mode="flower"
                                class="mr-[4rpx]"
                                size="28"
                            />
                            <u-icon
                                v-else
                                name="arrow-upward"
                                class="mr-[4rpx]"
                                size="28"
                            />
                            上传图片
                        </view>
                    </uni-file-picker>
                </view>
                <view
                    class="text-base text-primary flex text-content items-center bg-[#ECF6FF] py-[12rpx] px-[24rpx] rounded-full"
                    @click="emit('clear')"
                >
                    <u-icon name="trash" class="mr-[4rpx]" size="28" />
                    清空
                </view>
            </view>
            <view
                class="send-area__content border-[#DCDFE6] border border-solid rounded-[22px]"
            >
                <view class="flex-1 min-w-0 relative">
                    <u-input
                        type="textarea"
                        v-model="userInput"
                        :placeholder="placeholder"
                        maxlength="-1"
                        :height="30"
                        :auto-height="true"
                        confirm-type="send"
                        :adjust-position="false"
                        :fixed="false"
                        :custom-style="{
                            background: 'white',
                            maxHeight: '174rpx'
                        }"
                        adjust-keyboard-to="bottom"
                        @focus="emit('focus')"
                    />
                </view>
                <view class="ml-[20rpx] h-[65rpx]">
                    <view>
                        <!--                    v-if="userInput"-->
                        <image
                            class="w-[100rpx] h-[65rpx]"
                            src="@/static/images/icon/dialogue_send.png"
                            @click.stop="emit('send', userInput)"
                        >
                        </image>
                        <!--                    loading-->
                    </view>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { uploadFileByType } from '@/api/app'
import { computed, ref, shallowRef } from 'vue'
import { useVModel } from '@vueuse/core'
const props = withDefaults(
    defineProps<{
        modelValue: string
        placeholder?: string
        loading?: boolean
        showManual?: boolean
        showStop?: boolean
        showContinue?: boolean
        btnColor?: string
        safeAreaInsetBottom?: boolean
        filePlugin?: Record<string, any>
        showFileUpload?: boolean
    }>(),
    {
        placeholder: '请输入问题',
        loading: false,
        showManual: false,
        showContinue: false,
        btnColor: '#fff',
        safeAreaInsetBottom: false,
        filePlugin: () => ({}),
        showFileUpload: false
    }
)
const emit = defineEmits<{
    (event: 'clear'): void
    (event: 'pause'): void
    (event: 'continue'): void
    (event: 'focus'): void
    (event: 'update:modelValue', value: string): void
    (event: 'update:filePlugin', value: any): void
    (event: 'send', value: string): void
}>()

const userInput = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

const filePickerRef = shallowRef()
const filePlugin = useVModel(props, 'filePlugin', emit)
const fileUploadLoading = ref(false)

const httpRequest = async (e: any) => {
    console.log(e)
    fileUploadLoading.value = true
    filePickerRef.value?.clearFiles()
    const [file] = e.tempFiles
    try {
        const data = await uploadFileByType('image', {
            filePath: file.url,
            name: 'file',
            header: {}
        })
        filePlugin.value.url = data.uri
        filePlugin.value.name = data.name
    } finally {
        fileUploadLoading.value = false
    }
}
</script>

<style lang="scss" scoped>
.send-area {
    position: relative;
    padding: 20rpx 24rpx;
    background-color: #fff;
    .float-btn {
        position: absolute;
        left: 50%;
        top: -10rpx;
        transform: translate(-50%, -100%);
        z-index: 100;
        border: 1px solid;
        border-radius: 20rpx;
        @apply bg-white border-light;
    }
    &__content {
        padding: 10rpx 20rpx;
        position: relative;
        display: flex;
        align-items: center;

        :deep() {
            .u-input__textarea {
                --line-height: 40rpx;
                --line-num: 4;
                height: auto;
                min-height: var(--line-height) !important;
                max-height: calc(var(--line-height) * var(--line-num));
                font-size: 28rpx;
                box-sizing: border-box;
                padding: 0;
                line-height: var(--line-height);
                .uni-textarea-textarea {
                    max-height: calc(var(--line-height) * var(--line-num));
                    overflow-y: auto !important;
                }
            }
        }
        .send-btn {
            width: 100%;
            position: absolute;
            right: 0rpx;
            bottom: 10rpx;
            z-index: 99;
            padding: 0 20rpx;
        }
    }
}
</style>
