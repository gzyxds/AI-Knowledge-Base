<template>
    <div
        v-if="appStore.config.switch.dalle3_status"
        class="h-full flex-1 flex p-4 gap-4 draw_layout"
    >
        <el-scrollbar class="rounded-[12px] pb-[72px] bg-body">
            <div class="bg-body w-[355px] p-4 flex flex-col gap-4 relative">
                <!-- 正向提示词 -->
                <prompt
                    v-model="formData.prompt"
                    :model="DrawModeEnum.DALLE3"
                />

                <!-- 绘画尺寸 -->
                <dalle-picture-size v-model="formData.size" />

                <!-- 绘画风格 -->
                <dalle-style-picker v-model="formData.style" />

                <!-- 图片质量 -->
                <dalle-picture-quality v-model="formData.quality" />
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
import { useAppStore } from '@/stores/app'
import {
    pageLoading,
    checkOngoingTask,
    formData,
    resetFormData,
    taskStatusParams
} from './hooks/useDrawEffect'

// 公共组件
import Prompt from './components/common/prompt.vue'
import DrawResult from './components/common/draw-result.vue'
import CreateButton from './components/common/create-button.vue'

// DALLE
import DallePictureSize from './components/dalle/dalle-picture-size.vue'
import DalleStylePicker from './components/dalle/dalle-style-picker.vue'
import DallePictureQuality from './components/dalle/dalle-picture-quality.vue'

import DrawingEmpty from '@/assets/image/draw/empty-image.png'
import { DrawModeEnum } from '~/pages/draw/enums/DrawEnum'

definePageMeta({
    hasPanel: true,
    hiddenFooter: true
})

const appStore = useAppStore()

onMounted(() => {
    // 初始化成为 Dalle3 绘画模式
    resetFormData({
        draw_api: DrawModeEnum.DALLE3,
        draw_model: DrawModeEnum.DALLE3,
        action: 'generate',
        prompt: '',
        negative_prompt: '',
        size: '1024x1024'
    })
    taskStatusParams.model = DrawModeEnum.DALLE3
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