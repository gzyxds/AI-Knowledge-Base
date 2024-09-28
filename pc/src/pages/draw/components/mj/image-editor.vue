<template>
    <div class="locality-draw-popup">
        <ElDialog
            v-model="visible"
            width="980px"
            class="!rounded-[12px]"
            :center="true"
            :draggable="true"
            :destroy-on-close="true"
            :close-on-click-modal="false"
            @close="clearState"
        >
            <template #header>
                <div class="w-full text-left">
                    <div class="text-base font-medium">MJ 局部重绘 设置</div>
                    <div class="text-xs text-tx-secondary">局部重绘</div>
                </div>
            </template>
            <div class="flex justify-center">
                <div class="w-[940px] h-[500px]">
                    <!--  图片区域  -->
                    <canvas
                        id="locality-canvas"
                        width="940"
                        height="500"
                        @mousedown="onMouseDown"
                        @mousemove="onMouseMove"
                        @mouseup="onMouseUp"
                        @mouseleave="onMouseUp"
                    ></canvas>
                </div>
            </div>
            <div class="dialog-footer flex items-center mt-6 mb-4 px-10 cursor-pointer">
                <el-tooltip effect="dark" content="矩形工具" placement="top">
                    <div
                        class="flex justify-center items-center w-[43px] h-[43px] bg-page rounded-full mr-2 hover:bg-primary hover:text-white"
                        :class="[currentTool === 'rect' ? 'bg-primary text-white' : '']"
                        @click="changeTool('RECT')"
                    >
                        <Icon name="local-icon-rect" size="24" />
                    </div>
                </el-tooltip>

                <el-tooltip effect="dark" content="套索工具" placement="top">
                    <div
                        class="flex justify-center items-center w-[43px] h-[43px] bg-page rounded-full mr-2 hover:bg-primary hover:text-white"
                        :class="[currentTool === 'lasso' ? 'bg-primary text-white' : '']"
                        @click="changeTool('LASSO')"
                    >
                        <Icon name="local-icon-lasso" size="24" />
                    </div>
                </el-tooltip>

                <el-tooltip effect="dark" content="返回上一步" placement="top">
                    <div
                        class="flex justify-center items-center w-[43px] h-[43px] bg-page rounded-full mr-2 hover:bg-primary hover:text-white"
                        @click="undo"
                    >
                        <Icon name="local-icon-back" size="24" />
                    </div>
                </el-tooltip>

                <div class="flex flex-1 items-center">
                    <div
                        class="flex h-full items-end flex-1 rounded-sm overflow-hidden"
                    >
                        <div
                            class="flex items-center w-full min-h-full px-[6px] bg-page rounded-full"
                        >
                            <ElInput
                                v-model="inputContent"
                                :input-style="{
                                    'border-radius': '50px',
                                    backgroundColor: 'var(--el-bg-color-page)'
                                }"
                                class="min-h-full py-[6px]"
                                placeholder="请输入重绘描述 [推荐英文]"
                                type="textarea"
                                :autosize="{
                                    maxRows: 3
                                }"
                                resize="none"
                            />
                            <el-button
                                :loading="isLoading"
                                type="primary"
                                :icon="Promotion"
                                :circle="true"
                                @click="handleSubmit"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </ElDialog>
    </div>
</template>

<script lang="ts" setup>
import { useImageEditor } from '../../hooks/useImageEditor'
import { ElButton, ElInput, ElDialog } from 'element-plus'
import { Promotion } from '@element-plus/icons-vue'
import type { DrawRecordItem } from '../../types/draw'

const props = defineProps<{
    drawFunc: (item: DrawRecordItem, type: string) => void
}>()

const {
    initCanvas,
    changeTool,

    currentTool,

    onMouseDown,
    onMouseMove,
    onMouseUp,

    captureCombinedSelections,
    undo,
    clearState
} = useImageEditor({
    async onData(result: string) {
        try {
            console.log('result', result)
            await props.drawFunc({
                ...drawRecordItem.value,
                image_mask: result,
                prompt: inputContent.value
            }, 'inpaint')
            emit('success')
            visible.value = false
        } finally {
            isLoading.value = false
        }
    }
})

const emit = defineEmits(['success'])

// 是否弹窗
const visible = ref<boolean>(false)
// 重绘输入内容
const inputContent = ref<string>('')
// 加载中
const isLoading = ref<boolean>(false)
// 绘画记录数据
const drawRecordItem = ref<any>()

const handleSubmit = () => {
    if (inputContent.value.trim() === '') {
        feedback.msgError('请输入重绘描述')
        return
    }
    isLoading.value = true
    captureCombinedSelections()
}

const open = async (draw: DrawRecordItem) => {
    visible.value = true
    drawRecordItem.value = draw
    await nextTick()
    initCanvas('locality-canvas', draw.image as string)
}

defineExpose({ open })
</script>

<style lang="scss" scoped>
:deep(.el-textarea__inner) {
    box-shadow: none;
    background-color: var(--el-bg-color-page);
}
</style>
