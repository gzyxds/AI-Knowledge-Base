<template>
    <div
        v-if="appStore.config.switch.sd_status"
        class="h-full flex-1 flex p-4 gap-4 draw_layout"
    >
        <el-scrollbar class="rounded-[12px] pb-[72px] bg-body">
            <div class="bg-body w-[355px] p-4 flex flex-col gap-4 relative">
                <!-- 绘图功能 -->
                <draw-api v-model="formData.draw_api" />

                <!-- 绘图类型 -->
                <draw-type v-model="formData.draw_type" />

                <!-- 图生图初始蒙版 -->
                <uploader
                    v-model="formData.image_mask"
                    type="image"
                    v-if="formData.draw_type === DrawTypeEnum.img2img"
                />

                <!-- 重绘强度 -->
                <sd-denoising-strength
                    v-if="formData.draw_type === DrawTypeEnum.img2img"
                    v-model="formData.denoising_strength"
                />

                <!-- 正向提示词 -->
                <prompt v-model="formData.prompt" :model="DrawModeEnum.SD" />

                <!-- 反向提示词 -->
                <negative-prompt v-model="formData.negative_prompt" />

                <!-- 绘画尺寸 -->
                <sd-picture-size v-model="formData.size" />

                <!-- 主要模型选择 -->
                <sd-model v-model="formData.draw_model" />

                <!-- 微调模型选择（lora） -->
                <sd-lora v-model="formData.draw_loras" />

                <!-- 高级参数 -->
                <sd-options v-model="formData.complex_params" />
            </div>

            <!-- 生成按钮 -->
            <create-button />
        </el-scrollbar>
        <draw-result
            v-loading="pageLoading"
            element-loading-text="正在加载数据..."
        />
    </div>
    <div
        v-else
        class="h-full flex-1 flex p-4 gap-4 draw_layout justify-center items-center"
    >
        <el-result>
            <template #icon>
                <el-image
                    class="w-[100px] dark:opacity-60"
                    :src="DrawingEmpty"
                />
            </template>
            <template #title>
                <div class="text-info">绘画功能暂未开启</div>
            </template>
        </el-result>
    </div>
</template>

<script lang="ts" setup>
import { DrawModeEnum, DrawTypeEnum } from './enums/DrawEnum'
import { useAppStore } from '@/stores/app'
import {
    pageLoading,
    checkOngoingTask,
    formData,
    resetFormData,
    taskStatusParams
} from './hooks/useDrawEffect'

// 公共组件
import DrawType from './components/common/draw-type.vue'
import Prompt from './components/common/prompt.vue'
import Uploader from './components/common/uploader.vue'
import NegativePrompt from './components/common/negative-prompt.vue'
import DrawResult from './components/common/draw-result.vue'
import DrawApi from './components/common/draw-api.vue'
import CreateButton from './components/common/create-button.vue'

// SD
import SdPictureSize from './components/sd/sd-picture-size.vue'
import SdModel from './components/sd/sd-model.vue'
import SdLora from './components/sd/sd-lora.vue'
import SdOptions from './components/sd/sd-options.vue'
import SdDenoisingStrength from './components/sd/sd-denoising-strength.vue'

import DrawingEmpty from '@/assets/image/draw/empty-image.png'

definePageMeta({
    hasPanel: true,
    hiddenFooter: true
})

const appStore = useAppStore()

onMounted(() => {
    // 初始化成为 SD 绘画模式
    resetFormData({
        draw_api: DrawModeEnum.SD,
        action: 'generate',
        prompt: '',
        negative_prompt: '',
        size: '512x512'
    })
    taskStatusParams.model = DrawModeEnum.SD
    checkOngoingTask()
})
</script>

<style lang="scss" scoped>
.draw_layout {
    :deep(.el-loading-mask) {
        border-radius: 12px;
        background-color: var(--el-bg-color);
        z-index: 2000;
    }
}
</style>