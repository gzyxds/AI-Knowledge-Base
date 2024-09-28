<template>
    <div
        v-if="appStore.config.switch.mj_status"
        class="h-full flex-1 flex p-4 gap-4 draw_layout"
    >
        <el-scrollbar class="rounded-[12px] pb-[72px] bg-body">
            <div class="bg-body w-[355px] p-4 flex flex-col gap-4 relative">
                <!-- 正向提示词 -->
                <prompt
                    v-model="formData.prompt"
                    :model="DrawModeEnum.MJ_GOAPI"
                />

                <!-- 反向提示词 -->
                <negative-prompt v-model="formData.negative_prompt" />

                <!-- 图生图初始蒙版 -->
                <uploader v-model="formData.image_mask" type="image" />

                <!-- 绘画尺寸 -->
                <mj-picture-size v-model="formData.size" />

                <!-- 绘画模型 -->
                <mj-model v-model="formData.draw_model" />

                <!-- 版本选择 -->
                <mj-version v-model="formData.version" />

                <!-- 风格选择 -->
                <mj-styles
                    v-if="formData.version == 5 && formData.draw_model === 'niji'"
                    v-model="formData.style"
                />

                <!-- 高级参数 -->
                <mj-options v-model="formData.complex_params" />
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
import { DrawModeEnum } from './enums/DrawEnum'
import { ComplexParams } from './types/draw'

// 公共组件
import Prompt from './components/common/prompt.vue'
import NegativePrompt from './components/common/negative-prompt.vue'
import Uploader from './components/common/uploader.vue'
import DrawResult from './components/common/draw-result.vue'
import CreateButton from './components/common/create-button.vue'

// MJ
import MjPictureSize from './components/mj/mj-picture-size.vue'
import MjModel from './components/mj/mj-model.vue'
import MjVersion from './components/mj/mj-version.vue'
import MjStyles from './components/mj/mj-styles.vue'
import MjOptions from './components/mj/mj-options.vue'

import DrawingEmpty from '@/assets/image/draw/empty-image.png'

definePageMeta({
    hasPanel: true,
    hiddenFooter: true
})

const appStore = useAppStore()

onMounted(() => {
    // 初始化成为 MJ 绘画模式
    resetFormData({
        draw_api: DrawModeEnum.MJ_GOAPI,
        draw_model: 'mj',
        action: 'generate',
        prompt: '',
        negative_prompt: '',
        size: '1:1',
        complex_params: {
            seed: '',
            iw: 1,
            q: 1,
            s: 100,
            c: 0
        } as ComplexParams
    })
    taskStatusParams.model = DrawModeEnum.MJ_GOAPI
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