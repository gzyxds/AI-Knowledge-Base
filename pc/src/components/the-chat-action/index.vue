<template>
    <div class="chat-action">
        <div
            class="flex items-center pt-[10px] justify-center"
            v-if="showPause || showContinue"
        >
            <ElButton plain @click="emit('pause')" v-if="loading">
                <template #icon>
                    <Icon name="el-icon-VideoPause" />
                </template>
                停止
            </ElButton>
            <ElButton
                v-else-if="showContinue"
                plain
                @click="emit('continue')"
            >
                <template #icon>
                    <Icon name="el-icon-VideoPlay" />
                </template>
                继续
            </ElButton>
        </div>

        <div class="p-[10px]">
            <div class="flex mb-[10px]" v-if="filePlugin.url">
                <DelWrap @close="filePlugin.url = ''">
                    <div
                        class="flex bg-page p-[10px] rounded-[12px] items-center max-w-[400px]"
                    >
                        <ElImage
                            :src="filePlugin.url"
                            :preview-src-list="[filePlugin.url]"
                            :hide-on-click-modal="true"
                            class="w-[35px] h-[35px] flex-none"
                        />
                        <span
                            class="line-clamp-2 ml-[10px] flex-1 min-w-0"
                            :style="{
                                'word-break': 'break-word'
                            }"
                        >
                            {{ filePlugin.name }}
                        </span>
                    </div>
                </DelWrap>
            </div>

            <div class="mb-[10px] flex flex-wrap items-center">
                <slot name="btn" />
                <div class="mr-[10px]" v-if="showFileUpload">
                    <el-upload
                        ref="uploadRef"
                        :show-file-list="false"
                        :accept="'.jpg,.png,.jpeg'"
                        :multiple="false"
                        :on-success="handleSuccess"
                        :http-request="httpRequest"
                    >
                        <template #trigger>
                            <el-button plain class="!rounded-[8px]" :loading="fileUploadLoading">
                                <template #icon>
                                    <Icon name="el-icon-Upload" />
                                </template>
                                上传图片
                            </el-button>
                        </template>
                    </el-upload>
                </div>
                <ElButton
                    v-if="showClear"
                    :disabled="loading"
                    class="!rounded-[8px]"
                    plain
                    @click="emit('clear')"
                >
                    <template #icon>
                        <Icon name="el-icon-Delete" />
                    </template>
                    清空
                </ElButton>
                <ElButton
                    :color="btnColor"
                    style="
                        --el-button-disabled-text-color: var(
                            --el-button-text-color
                        );
                    "
                    :disabled="loading"
                    @click="emit('enter', item.keyword)"
                    v-for="(item, index) in menus"
                    :key="index"
                >
                    {{ item.keyword }}
                </ElButton>
            </div>
            <div
                ref="textContainerRef"
                class="chat-input relative text-container flex items-center"
            >
                <ElInput
                    ref="textareaRef"
                    v-model="userInput"
                    :autosize="autosize"
                    type="textarea"
                    :placeholder="getPlaceholderByClient"
                    resize="none"
                    @compositionstart="isInputChinese = true"
                    @compositionend="isInputChinese = false"
                    @keydown="handleInputEnter"
                    @blur="handleBlur"
                    @focus="handleFocus"
                >
                </ElInput>

                <div class="py-[3px] mr-[-7px] input-suffix">
                    <ElButton
                        :disabled="!userInput || loading"
                        type="primary"
                        :style="{
                            height: '40px'
                        }"
                        @click="emit('enter', userInput)"
                    >
                        <Icon name="el-icon-Promotion" :size="20" />
                        发送
                    </ElButton>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { useElementSize, useVModel } from '@vueuse/core'
import type { InputInstance, UploadRequestOptions } from 'element-plus'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '~/stores/app'
import { uploadFile } from '@/api/app'

const props = withDefaults(
    defineProps<{
        placeholder?: string
        loading?: boolean
        showManual?: boolean
        showPause?: boolean
        showClear?: boolean
        showContinue?: boolean
        showFileUpload?: boolean
        menus: any[]
        btnColor: string
        filePlugin?: Record<string, any>
    }>(),
    {
        placeholder: '请输入问题',
        loading: false,
        showManual: false,
        menus: () => [],
        btnColor: '#fff',
        showPause: true,
        showClear: true,
        showContinue: false,
        showFileUpload: false,
        filePlugin: () => ({})
    }
)
const emit = defineEmits<{
    (event: 'clear'): void
    (event: 'pause'): void
    (event: 'continue'): void
    (event: 'blur'): void
    (event: 'focus'): void
    (event: 'enter', value: string): void
    (event: 'update:filePlugin', value: any): void
}>()

const appStore = useAppStore()
const userInput = ref('')
const textContainerRef = shallowRef<HTMLDivElement>()
const textareaRef = shallowRef<InputInstance>()
const isFocus = ref(false)
const handleBlur = () => {
    isFocus.value = false
    emit('blur')
}
const handleFocus = () => {
    isFocus.value = true
    emit('focus')
}
const isInputChinese = ref(false)
const getPlaceholderByClient = computed(() => {
    return `${props.placeholder} ${
        appStore.isMobile ? '' : '（Shift + Enter）= 换行'
    }`
})

const filePlugin = useVModel(props, 'filePlugin', emit)
const fileUploadLoading = ref(false)
const handleSuccess = (response: any) => {
    filePlugin.value.url = response.uri
    filePlugin.value.name = response.name
}

const httpRequest = async (options: UploadRequestOptions) => {
    fileUploadLoading.value = true
    try {
        const data = await uploadFile('image', {
            file: options.file,
            name: 'file',
            header: {}
        })
        return data
    } finally {
        fileUploadLoading.value = false
    }
}
const getBtnColor = (disabled: boolean) => {
    return disabled
        ? 'var(--el-text-color-disabled)'
        : 'var(--el-color-primary)'
}
const handleInputEnter = (e: any) => {
    if (e.shiftKey && e.keyCode === 13) {
        return
    }
    if (isInputChinese.value) return
    if (e.keyCode === 13) {
        // textareaRef.value?.blur()
        emit('enter', userInput.value)
        return e.preventDefault()
    }
}

const handleManual = () => {
    emit('enter', '转人工')
}
const autosize = computed(() => {
    return appStore.isMobile
        ? {
              maxRows: 4,
              minRows: 1
          }
        : {
              maxRows: 6,
              minRows: 1
          }
})
const handleCommand = async (command: any) => {
    emit(command)
}

const isTextAreaExpands = ref(false)
const { height } = useElementSize(textContainerRef)
let initHeight = 0
watchEffect(() => {
    if (initHeight === 0) {
        initHeight = height.value
    }
    if (height.value > initHeight) {
        isTextAreaExpands.value = true
    }

    if (userInput.value === '' && height.value > initHeight) {
        isTextAreaExpands.value = false
    }
})

const setInputValue = (value = '') => {
    userInput.value = value
}

defineExpose({
    setInputValue,
    focus: () => textareaRef.value?.focus(),
    blur: () => textareaRef.value?.blur()
})
</script>

<style lang="scss" scoped>
.chat-action {
    margin: 0 auto;
    box-sizing: content-box;
    width: 100%;
    .chat-input {
        :deep() {
            .el-textarea__inner {
                --el-input-border-radius: 12px;
                --el-input-hover-border-color: var(--el-color-primary);
                --el-input-border-color: var(--el-color-primary);
                min-height: 46px !important;
                line-height: 24px;
                padding: 10px 12px;
                padding-right: 100px;
            }
        }
        .input-suffix {
            position: absolute;
            right: 10px;
            bottom: 6px;
            width: 80px;
            height: 40px;
        }
    }
    // .action-btn {
    //   position: absolute;
    //   z-index: 3;
    //   right: 14px;
    //   bottom: 8px;
    //   @apply flex items-center;
    // }
    // .text-container {
    //   .el-textarea {
    //     overflow: visible;
    //     background-color: var(--el-input-bg-color, var(--el-fill-color-blank));
    //     box-shadow: 0 0 0 1px var(--el-input-border-color, var(--el-border-color))
    //       inset;
    //     border-radius: var(
    //       --el-input-border-radius,
    //       var(--el-border-radius-base)
    //     );
    //   }
    //   :deep(.el-textarea__inner) {
    //     transition: margin-bottom 0.6s ease;
    //     padding: 10px 12px;
    //     @apply sm:pr-[120px];
    //     box-shadow: none;
    //     background-color: transparent;
    //   }
    //   &--expands {
    //     :deep(.el-textarea__inner) {
    //       padding-right: 12px;
    //       padding-bottom: 0;
    //       margin-bottom: 40px;
    //     }
    //   }
    //   &--focus {
    //     .el-textarea {
    //       outline: 0;
    //       box-shadow: 0 0 0 1px var(--el-input-focus-border-color) inset;
    //     }
    //   }
    // }
}
</style>
