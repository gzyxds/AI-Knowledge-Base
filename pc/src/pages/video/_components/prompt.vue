<template>
    <el-form-item prop="prompt" required>
        <template #label>
            <span class="font-bold text-tx-primary">
                {{ currentConfig.label }}
            </span>
        </template>
        <div class="flex-1" v-loading="isLock" element-loading-text="正在翻译">
            <LTextarea
                v-model="value"
                :placeholder="currentConfig.placeholder"
                :contentStyle="{
                    height: '120px'
                }"
            >
                <template #length-suffix>
                    <div class="flex p-[10px]">
                        <div class="flex-1 flex items-center">
                            <div
                                class="flex items-center cursor-pointer text-[#6F7E8E] text-sm mr-2 hover:text-primary"
                                v-if="
                                    isText2Video &&
                                    config.status &&
                                    config.data?.length
                                "
                                @click="useExample"
                            >
                                <Icon name="el-icon-Refresh"></Icon>
                                <span class="ml-[4px]">试试示例</span>
                            </div>
                            <div
                                v-if="showTranslate"
                                class="flex items-center cursor-pointer text-[#6F7E8E] text-sm hover:text-primary"
                                @click="translatePrompt"
                            >
                                <Icon name="el-icon-Switch"></Icon>
                                <span class="ml-[4px]">翻译成英文</span>
                            </div>
                        </div>
                        <ElButton
                            link
                            size="small"
                            type="primary"
                            @click="value = ''"
                        >
                            <template #icon>
                                <Icon name="el-icon-Delete" :size="12" />
                            </template>
                            清空
                        </ElButton>
                    </div>
                </template>
            </LTextarea>
        </div>
    </el-form-item>
</template>
<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import { translate } from '@/api/video'
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue: string
        config: Record<string, any>
        type: number
        showTranslate: boolean
    }>(),
    {
        modelValue: '',
        config: () => ({}),
        showTranslate: false,
        type: 1
    }
)

const { modelValue: value } = useVModels(props, emit)
const isText2Video = computed(() => props.type === 1)

const configMap = {
    '1': {
        label: '视频场景',
        placeholder: '在此描述你的视频场景，包含内容主体+动作/场景'
    },
    '2': {
        label: '描述词',
        placeholder: '描述视频中需要变化的内容即可～'
    }
}

const currentConfig = computed<any>(() => configMap[props.type as never] || {})
const currentIndex = ref(-1)
const useExample = () => {
    const length = props.config.data?.length
    if (length) {
        let index = Math.round(Math.random() * (length - 1))
        if (currentIndex.value === index) {
            if (index < length - 1) {
                index++
            } else {
                index--
            }
        }
        if (index < 0) {
            index = 0
        }
        currentIndex.value = index
        const content = props.config.data[index]
        if (content) value.value = content
    }
}

const { lockFn: translatePrompt, isLock } = useLockFn(async () => {
    if (!value.value) {
        return ElMessage.error('请输入描述词')
    }

    const data = await translate({
        prompt: value.value
    })
    value.value = data.result
})
</script>
