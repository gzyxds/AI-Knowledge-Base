<template>
    <ElDialog v-model="showModel" width="900px">
        <div class="flex text-tx-primary">
            <div class="flex w-[510px] justify-center items-center">
                <ElImage
                    class="w-full rounded-[12px]"
                    :src="data.image"
                    :preview-src-list="[data.image]"
                    hide-on-click-modal
                />
            </div>

            <div class="w-[350px] ml-[20px] flex flex-col">
                <div class="bg-page p-[20px] rounded-[15px] flex-1 min-h-0">
                    <div class="mb-[15px]">
                        <div class="flex items-center mb-[15px]">
                            <div class="font-medium mr-auto">描述词</div>
                            <ElButton
                                link
                                type="primary"
                                @click="copy(data.prompt)"
                            >
                                复制
                            </ElButton>
                        </div>
                        <div
                            class="bg-body rounded-[10px] h-[110px] overflow-y-auto"
                        >
                            <div class="p-[10px]">{{ data.prompt }}</div>
                        </div>
                    </div>
                    <div class="mb-[15px]" v-if="data.negative_prompt">
                        <div class="flex items-center mb-[15px]">
                            <div class="font-medium mr-auto">反向词</div>
                            <ElButton
                                link
                                type="primary"
                                @click="copy(data.negative_prompt)"
                            >
                                复制
                            </ElButton>
                        </div>
                        <div
                            class="bg-body rounded-[10px] h-[110px] overflow-y-auto"
                        >
                            <div class="p-[10px]">
                                {{ data.negative_prompt }}
                            </div>
                        </div>
                    </div>
                    <div class="mb-[15px]">
                        <div class="flex items-center mb-[15px]">
                            <div class="font-medium mr-auto">创作信息</div>
                        </div>
                        <div class="bg-body rounded-[10px] p-[10px]">
                            <div class="flex mb-[15px]">
                                <div class="mr-auto flex-none">生成时间</div>
                                <div class="ml-[20px] text-right">
                                    {{ data.create_time }}
                                </div>
                            </div>
                            <div class="flex mb-[15px]">
                                <div class="mr-auto flex-none">绘画类型</div>
                                <div class="ml-[20px] text-right">
                                    {{
                                        data.draw_type === 'txt2img'
                                            ? '文生图'
                                            : '图生图'
                                    }}
                                </div>
                            </div>
                            <div class="flex mb-[15px]">
                                <div class="mr-auto flex-none">绘画模型</div>
                                <div class="ml-[20px] text-right">
                                    {{ data.engine }}
                                </div>
                            </div>
                            <div
                                class="flex mb-[15px]"
                                v-if="data.loras.length"
                            >
                                <div class="mr-auto flex-none">微调模型</div>
                                <div class="ml-[20px] text-right">
                                    {{ data.loras }}
                                </div>
                            </div>
                            <div class="flex mb-[15px]">
                                <div class="mr-auto flex-none">图片尺寸</div>
                                <div class="ml-[20px] text-right">
                                    {{ data.scale }}
                                </div>
                            </div>
                            <div class="flex mb-[15px]">
                                <div class="mr-auto flex-none">绘画步数</div>
                                <div class="ml-[20px] text-right">
                                    {{ complexParams.step }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-[20px]">
                    <div class="flex-1 mr-[20px]">
                        <ElButton
                            class="w-full"
                            type="primary"
                            @click="reDrawHandle"
                        >
                            画同款
                        </ElButton>
                    </div>
                    <div class="flex-1">
                        <ElButton
                            class="w-full"
                            plain
                            type="primary"
                            @click="downloadImgFile(data.image)"
                        >
                            下载图片
                        </ElButton>
                    </div>
                </div>
            </div>
        </div>
    </ElDialog>
</template>
<script setup lang="ts">
import { resetFormData } from '@/pages/draw/hooks/useDrawEffect'
import { useVModel } from '@vueuse/core'
import { downloadImgFile } from '@/utils/download'

const router = useRouter()
const { copy } = useCopy()
const props = withDefaults(
    defineProps<{
        show: boolean
        data: any
    }>(),
    {
        data: () => ({})
    }
)
const emit = defineEmits<{
    (event: 'update:show', value: any): void
}>()

const showModel = useVModel(props, 'show', emit)
const complexParams = computed(() => {
    if (props.data.complex_params) {
        return JSON.parse(props.data.complex_params)
    } else {
        return {}
    }
})

/**
 * 重绘
 */
const reDrawHandle = () => {
    const item = props.data
    const params: Record<string, any> = {
        draw_model: item.engine,
        image_mask: item.image_base,
        negative_prompt: item.negative_prompt,
        prompt: item.prompt,
        size: item.scale,
        draw_loras: item.loras
    }
    if (item.image_base) {
        params.draw_type = 'img2img'
    }
    resetFormData(params)
    showModel.value = false

    if (item.model === 'dalle3') {
        router.push('/draw/dalle')
    } else if (item.model === 'sd') {
        router.push('/draw/sd')
    } else {
        router.push('/draw/mj')
    }
}
</script>

<style scoped></style>