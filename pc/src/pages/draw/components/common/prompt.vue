<template>
    <div>
        <sidbar-item-title
            title="描述词"
            required
            tips="输入你希望AI绘制的内容，无需完整的句子，只需要关键的提示词即可，但必须为英文。例如：1dog,black hair"
        />

        <div
            v-loading="translateLoading"
            element-loading-text="正在翻译"
            class="bg-[var(--el-bg-color-page)] rounded-[12px] prompt_container"
        >
            <el-input
                v-model="value"
                :rows="4"
                :input-style="{
                    boxShadow: 'unset',
                    backgroundColor: 'transparent'
                }"
                resize="none"
                type="textarea"
                placeholder="请输入正向提示词"
                @focus="checkUserLogin"
            />
            <div class="flex items-center p-3 pb-2">
                <prompt-selector v-model="value">
                    <template #trigger>
                        <div
                            class="flex items-center cursor-pointer text-[#6F7E8E] text-sm hover:text-primary"
                        >
                            <img
                                class="w-[13px] h-[13px] align-middle"
                                src="@/assets/image/draw/dict.png"
                            />
                            <span class="ml-[4px]">描述词推荐</span>
                        </div>
                    </template>
                </prompt-selector>
                <div
                    v-if="config.translate_switch == 1"
                    class="flex items-center cursor-pointer text-[#6F7E8E] text-sm ml-2 hover:text-primary"
                    @click="translatePrompt"
                >
                    <Icon name="el-icon-Refresh"></Icon>
                    <span class="ml-[4px]">翻译成英文</span>
                </div>
                <div
                    class="flex items-center cursor-pointer text-primary text-sm ml-auto"
                    @click="clearPrompt"
                >
                    <Icon name="el-icon-Delete"></Icon>
                    <span class="ml-[4px]">清空</span>
                </div>
            </div>
        </div>
        <div class="flex items-center text-sm text-[#798696] mt-2">
            <div class="flex items-center gap-1 flex-nowrap">
                <span class="whitespace-nowrap">随便试试：</span>
            </div>
            <div
                class="flex-[1_0_0] truncate text-[#333333] dark:text-white cursor-pointer hover:text-primary"
                @click="
                    value =
                        exampleList[exampleIndex]?.prompt_en || defaultPrompt
                "
            >
                {{ exampleList[exampleIndex]?.prompt || defaultPrompt }}
            </div>
            <el-tooltip effect="dark" content="刷新" placement="bottom">
                <div @click="randomInput()">
                    <Icon
                        class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-[12px] p-1 box-content"
                        name="el-icon-RefreshRight"
                        size="18"
                        color="#556477"
                    />
                </div>
            </el-tooltip>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import { translate, drawingExample } from '@/api/draw'
import sidbarItemTitle from './../common/sidbar-item-title.vue'
import PromptSelector from './prompt-selector.vue'
import { checkUserLogin, config } from './../../hooks/useDrawEffect'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue: string
        model: string
    }>(),
    {
        modelValue: '',
        model: ''
    }
)

const { modelValue: value } = useVModels(props, emit)
const translateLoading = ref<boolean>(false)
const defaultPrompt = ref<string>('')
const exampleIndex = ref<number>(0)
const exampleList = ref<
    {
        id: number
        prompt: string
        prompt_en: string
        sort: number
        status: number
    }[]
>([])

const clearPrompt = () => {
    if (checkUserLogin()) return
    value.value = ''
}

const randomInput = () => {
    if (exampleList.value.length > 0) {
        if (exampleIndex.value < exampleList.value.length - 1) {
            exampleIndex.value++
        } else {
            exampleIndex.value = 0
        }
    }
}

const translatePrompt = () => {
    if (checkUserLogin()) return
    if (config.value.translate_switch != 1) return
    if (!value.value) {
        return ElMessage.warning('请输入描述词')
    }
    translateLoading.value = true
    translate({
        prompt: value.value,
        model: props.model
    })
        .then((res: { content: string; result: string }) => {
            value.value = res.result
        })
        .finally(() => {
            translateLoading.value = false
        })
}

onMounted(async () => {
    await nextTick()
    exampleList.value = await drawingExample({
        model: props.model
    })
})
</script>

<style lang="scss" scoped>
.prompt_container {
    :deep(.el-loading-spinner .circular) {
        height: 24px;
        width: 24px;
    }
    :deep(.el-loading-text) {
        font-size: 12px;
    }
    :deep(.el-loading-mask) {
        border-radius: 6px !important;
        background-color: var(--el-bg-color-page) !important;
        z-index: 2000;
    }
}
</style>
