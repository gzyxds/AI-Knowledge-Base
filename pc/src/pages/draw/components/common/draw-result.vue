<template>
    <div class="bg-body flex-1 rounded-[12px] p-4 flex flex-col gap-4 relative">
        <div class="sticky top-0">
            <div class="border-b border-b-[#eff0f2] dark:border-[#333333] pb-4">
                绘图任务
            </div>
            <div class="mt-4" style="--el-border-radius-base: 12px">
                <el-segmented
                    class="task_type !bg-[transparent]"
                    v-model="taskStatus"
                    :options="taskStatusOptions"
                    x
                    @change="taskStatusChange"
                />
            </div>
        </div>
        <el-scrollbar class="draw_result flex-1" ref="resultScrollBar">
            <div v-if="pager.lists.length > 0">
                <div
                    class="grid grid-cols-2 xl:grid-cols-3 2xl:grid-cols-3 3xl:grid-cols-4 5xl:grid-cols-5 gap-4"
                >
                    <div
                        v-for="(item, index) in pager.lists"
                        :key="item.id"
                        class="rounded-[12px] p-4 flex flex-col gap-2 border border-[#eff0f2] dark:border-[#333333] min-w-[272px] flex-none"
                    >
                        <div class="flex justify-between relative">
                            <el-tag
                                :type="statusMap[item.status].type"
                                effect="light"
                            >
                                {{ statusMap[item.status].label }}
                            </el-tag>
                            <div
                                v-if="item.status !== 1 || item.status === 0"
                                class="flex items-center justify-center"
                                style="position: absolute; right: 0; top: -5px"
                            >
                                <el-tooltip
                                    v-if="item.status === 3"
                                    effect="dark"
                                    content="复制提示词"
                                    placement="bottom"
                                >
                                    <div @click="copy(item.prompt)">
                                        <div
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-[6px] box-content"
                                        >
                                            <img
                                                src="assets/image/draw/icon_copy.png"
                                                class="w-[16px] h-[16px] object-contain"
                                            />
                                        </div>
                                    </div>
                                </el-tooltip>
                                <template v-if="item.status === 3">
                                    <el-tooltip
                                        effect="dark"
                                        content="下载图片"
                                        placement="bottom"
                                    >
                                        <div
                                            @click="downloadImgFile(item.image)"
                                        >
                                            <div
                                                class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-[6px] box-content"
                                            >
                                                <img
                                                    src="assets/image/draw/icon_download.png"
                                                    class="w-[16px] h-[16px] object-contain"
                                                />
                                            </div>
                                        </div>
                                    </el-tooltip>
                                    <el-tooltip
                                        v-if="
                                            appStore.getSquareConfig.draw_award
                                                .is_open
                                        "
                                        effect="dark"
                                        content="分享至广场"
                                        placement="bottom"
                                    >
                                        <div @click="shareDraw(item)">
                                            <div
                                                class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-[6px] box-content"
                                            >
                                                <img
                                                    src="assets/image/draw/icon_share.png"
                                                    class="w-[16px] h-[16px] object-contain"
                                                />
                                            </div>
                                        </div>
                                    </el-tooltip>
                                    <el-tooltip
                                        v-if="
                                            item.engine === 'mj' &&
                                            item?.able_cut
                                        "
                                        effect="dark"
                                        content="一键切图"
                                        placement="bottom"
                                    >
                                        <div @click="handleSplit(item)">
                                            <div
                                                class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-[6px] box-content"
                                            >
                                                <img
                                                    src="assets/image/draw/icon_cutout.png"
                                                    class="w-[16px] h-[16px] object-contain"
                                                />
                                            </div>
                                        </div>
                                    </el-tooltip>
                                </template>
                                <el-tooltip
                                    effect="dark"
                                    content="重新生成"
                                    placement="bottom"
                                >
                                    <div @click="reDrawHandle(item)">
                                        <div
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-[6px] box-content"
                                        >
                                            <img
                                                src="assets/image/draw/icon_reload.png"
                                                class="w-[16px] h-[16px] object-contain"
                                            />
                                        </div>
                                    </div>
                                </el-tooltip>
                                <el-tooltip
                                    effect="dark"
                                    content="删除"
                                    placement="bottom"
                                >
                                    <div @click="deleteHandle(item.id)">
                                        <div
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-[6px] box-content"
                                        >
                                            <img
                                                src="assets/image/draw/icon_delete.png"
                                                class="w-[16px] h-[16px] object-contain"
                                            />
                                        </div>
                                    </div>
                                </el-tooltip>
                            </div>
                        </div>

                        <div
                            class="relative rounded-[12px] overflow-hidden flex-1"
                        >
                            <div
                                class="bg-[var(--el-bg-color-page)]"
                                v-loading="item.loading"
                            >
                                <div
                                    class="grid grid-cols-2 align-center justify-center"
                                    v-if="Array.isArray(item.image)"
                                >
                                    <div
                                        class="m-2 image__item relative"
                                        style="flex-basis: calc(50% - 10px)"
                                        v-for="(img, index) in item.image"
                                        :key="index"
                                    >
                                        <aspect-ratio
                                            :src="img"
                                            :ratio="[1, 1]"
                                            fit="cover"
                                        />
                                        <div
                                            class="image__item__icon cursor-default"
                                            @click="shareDraw(item, img)"
                                        >
                                            <Icon
                                                name="el-icon-Share"
                                                color="#ffffff"
                                                size="16"
                                            >
                                            </Icon>
                                        </div>
                                    </div>
                                </div>
                                <aspect-ratio
                                    v-else-if="item.status === 3"
                                    :thumbnail="item.thumbnail"
                                    :src="item.image"
                                    :ratio="[1, 1]"
                                />
                            </div>
                            <div
                                v-if="item.status === 2"
                                class="w-full pb-[100%]"
                            >
                                <div
                                    class="w-full h-full pb-9 px-4 flex flex-col justify-center items-center absolute left-0 top-0"
                                >
                                    <img
                                        class="w-1/2 mb-4"
                                        :src="drawError"
                                        alt="绘图失败"
                                    />
                                    <div>绘图失败</div>
                                    <div
                                        class="text-xs text-[#798696] dark:text-white line-clamp-3 w-full break-all"
                                    >
                                        错误信息：{{ item.fail_reason }}
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="item.status === 0 || item.status === 1"
                                class="draw_loading w-full pb-[100%]"
                                v-loading="true"
                                element-loading-svg="none"
                                element-loading-text="正在生成中..."
                            ></div>
                        </div>
                        <div class="w-full box-border">
                            <div class="line-clamp-1">
                                {{ item.prompt }}
                            </div>
                        </div>
                        <!--    MJ操作    -->
                        <div
                            class=""
                            v-if="item.status === 3 && item.engine === 'mj'"
                        >
                            <template
                                v-if="
                                    !item?.able_actions?.includes(
                                        'low_variation'
                                    ) && item?.able_actions?.length
                                "
                            >
                                <div class="flex flex-none">
                                    <span class="text-xs flex-none"
                                        >放大图片</span
                                    >
                                    <div class="flex flex-wrap gap-y-[10px]">
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'upscale1')
                                            "
                                        >
                                            左上
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'upscale2')
                                            "
                                        >
                                            右上
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'upscale3')
                                            "
                                        >
                                            左下
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'upscale4')
                                            "
                                        >
                                            右下
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-none mt-[15px]">
                                    <span class="text-xs flex-none"
                                        >变体图片</span
                                    >
                                    <div class="flex flex-wrap gap-y-[10px]">
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'variation1')
                                            "
                                        >
                                            左上
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'variation2')
                                            "
                                        >
                                            右上
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'variation3')
                                            "
                                        >
                                            左下
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'variation4')
                                            "
                                        >
                                            右下
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-else-if="item?.able_actions?.length">
                                <div>
                                    <span class="text-xs flex-none">调整</span>
                                    <div
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(item, 'high_variation')
                                        "
                                    >
                                        微调(强)
                                    </div>
                                    <div
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(item, 'low_variation')
                                        "
                                    >
                                        微调(弱)
                                    </div>
                                    <!--                                    <div class="opt-btn" @click="onImageEditorOpen(item)">局部重绘</div>-->
                                </div>
                                <div class="flex flex-none mt-[15px]">
                                    <span class="text-xs flex-none">变化</span>
                                    <div class="flex flex-wrap gap-y-[10px]">
                                        <template
                                            v-if="
                                                item?.able_actions?.includes(
                                                    'outpaint_1.5x'
                                                )
                                            "
                                        >
                                            <div
                                                class="opt-btn"
                                                @click="
                                                    handleMjEdit(
                                                        item,
                                                        'outpaint_1.5x'
                                                    )
                                                "
                                            >
                                                变焦1.5x
                                            </div>
                                            <div
                                                class="opt-btn"
                                                @click="
                                                    handleMjEdit(
                                                        item,
                                                        'outpaint_2x'
                                                    )
                                                "
                                            >
                                                变焦2x
                                            </div>
                                        </template>
                                        <template
                                            v-if="
                                                item?.able_actions?.includes(
                                                    'upscale_2x'
                                                )
                                            "
                                        >
                                            <div
                                                class="opt-btn"
                                                @click="
                                                    handleMjEdit(
                                                        item,
                                                        'upscale_2x'
                                                    )
                                                "
                                            >
                                                高清2x
                                            </div>
                                            <div
                                                class="opt-btn"
                                                @click="
                                                    handleMjEdit(
                                                        item,
                                                        'upscale_4x'
                                                    )
                                                "
                                            >
                                                高清4x
                                            </div>
                                        </template>
                                        <template
                                            v-if="
                                                item?.able_actions?.includes(
                                                    'upscale_subtle'
                                                )
                                            "
                                        >
                                            <div
                                                class="opt-btn"
                                                @click="
                                                    handleMjEdit(
                                                        item,
                                                        'upscale_subtle'
                                                    )
                                                "
                                            >
                                                弱变化
                                            </div>
                                            <div
                                                class="opt-btn"
                                                @click="
                                                    handleMjEdit(
                                                        item,
                                                        'upscale_creative'
                                                    )
                                                "
                                            >
                                                强变化
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <!--    第一次放大    -->
                                <div
                                    class="flex flex-none mt-[15px]"
                                    v-if="
                                        item?.able_actions?.includes('pan_down')
                                    "
                                >
                                    <span class="text-xs flex-none">拉伸</span>
                                    <div class="flex flex-wrap gap-y-[10px]">
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'pan_left')
                                            "
                                        >
                                            ⬅️
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'pan_right')
                                            "
                                        >
                                            ➡️
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'pan_up')
                                            "
                                        >
                                            ⬆️
                                        </div>
                                        <div
                                            class="opt-btn"
                                            @click="
                                                handleMjEdit(item, 'pan_down')
                                            "
                                        >
                                            ⬇️
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-[#8794A3]">
                                {{ item.create_time }}
                            </span>
                            <el-tag>
                                {{
                                    DrawResultTypeEnum[
                                        item.type as 1 | 2 | 3 | 4
                                    ]
                                }}
                            </el-tag>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="h-full flex items-center justify-center">
                <el-result>
                    <template #icon>
                        <el-image
                            class="w-[150px] dark:opacity-60"
                            :src="DrawingEmpty"
                        />
                    </template>
                    <template #title>
                        <div class="text-xl">当前任务是空的哦</div>
                    </template>
                    <template #sub-title>
                        <div class="text-info">
                            在左侧输入描述，创建你的作品吧!
                        </div>
                    </template>
                </el-result>
            </div>
        </el-scrollbar>
        <div class="w-full flex justify-end">
            <pagination v-model="pager" background @change="scrollTop" />
        </div>
    </div>

    <draw-share
        v-if="showShare"
        ref="shareRef"
        @close="showShare = false"
        @success="(val) => sharedIds.push(val)"
    ></draw-share>

    <image-editor
        v-if="showImageEditor"
        ref="imageEditorRef"
        :draw-func="handleMjEdit"
        @success="showImageEditor = false"
        @close="showImageEditor = false"
    >
    </image-editor>
</template>

<script lang="ts" setup>
import type { DrawRecordItem, DrawTypeOptions } from '../../types/draw'
import {
    pager,
    getLists,
    taskStatusChange,
    deleteHandle,
    resetFormData,
    scroll,
    pageLoading,
    createTask,
    formData
} from '../../hooks/useDrawEffect'
import { useImageSplit } from '../../hooks/useImageSplit'
import { copy } from '@/utils/util'
import { downloadImgFile } from '@/utils/download'
import { DrawResultTypeEnum } from '../../enums/DrawEnum'

import { ElScrollbar } from 'element-plus'
import { useAppStore } from '~/stores/app'

import DrawShare from './draw-share.vue'
import ImageEditor from '../mj/image-editor.vue'
import DrawingEmpty from '@/assets/image/draw/empty-image.png'
import drawError from '@/assets/image/draw/error.png'

const appStore = useAppStore()

const emit = defineEmits(['pageChange', 'taskStatusChange'])

const resultScrollBar = ref<InstanceType<typeof ElScrollbar>>()
const statusMap: Record<
    number,
    {
        label: string
        type: 'success' | 'warning' | 'danger'
    }
> = {
    0: {
        label: '生成中',
        type: 'warning'
    },
    1: {
        label: '生成中',
        type: 'warning'
    },
    2: {
        label: '生成失败',
        type: 'danger'
    },
    3: {
        label: '生成成功',
        type: 'success'
    }
}
const taskStatus = ref<number>(-1)
const taskStatusOptions: DrawTypeOptions[] | undefined = [
    {
        label: '全部',
        value: -1
    },
    {
        label: '完成',
        value: 3
    },
    {
        label: '进行中',
        value: 1
    },
    {
        label: '失败',
        value: 2
    }
]

const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])

const showImageEditor = ref<boolean>(false)
const imageEditorRef = shallowRef<any>(null)

watch(
    () => scroll.value,
    () => {
        resultScrollBar.value?.scrollTo(0, 0)
    }
)

/**
 * 分享到广场
 */
const shareDraw = async (drawing: DrawRecordItem, base64?: string) => {
    const params: any = {
        image: drawing.image,
        prompts: drawing.prompt,
        records_id: drawing.id
    }
    if (base64) {
        params.is_base64 = 1
        params.base64 = base64
    }
    if (sharedIds.value.includes(drawing.id) || drawing.is_share) {
        await feedback.confirm('该图片已分享过，是否确认重复分享？')
    }
    showShare.value = true
    await nextTick()
    shareRef.value.open(params)
}

/**
 * 切图
 * @param path 图片路径
 */
const { images, splitImage } = useImageSplit()
const handleSplit = async (item: DrawRecordItem) => {
    try {
        item.loading = true
        await splitImage(item.image as string)
        console.log(images.value)
        item.image = images.value
    } finally {
        item.loading = false
    }
}

/**
 * 滚动到顶部
 */
const scrollTop = async () => {
    pageLoading.value = true
    await getLists()
    resultScrollBar.value?.scrollTo(0, 0)
    pageLoading.value = false
}

/**
 * 重绘
 */
const reDrawHandle = (item: DrawRecordItem) => {
    const params: any = {
        draw_model: item.engine,
        image_mask: item.image_base,
        negative_prompt: item.negative_prompt,
        prompt: item.prompt,
        size: item.scale,
        draw_loras: item.loras,
        version: item.version
    }
    if (item.image_base) {
        params.draw_type = 'img2img'
    }
    resetFormData(params)
}

const onImageEditorOpen = async (item: DrawRecordItem) => {
    showImageEditor.value = true
    await nextTick()
    imageEditorRef.value.open(item)
}

/**
 *  MJ 操作
 */
const handleMjEdit = async (item: DrawRecordItem, action: string) => {
    const params: any = {
        action,
        draw_model: item.engine,
        image_mask: item.image_mask,
        negative_prompt: item.negative_prompt,
        prompt: item.prompt,
        size: item.scale,
        origin_task_id: item.task_id,
        complex_params: JSON.parse(item?.complex_params)
    }
    if (params.image_mask === undefined && item.image_base) {
        params.draw_type = 'img2img'
        params.image_mask = item.image_base
    }

    await createTask({
        ...formData.value,
        ...params
    })
}
</script>

<style lang="scss" scoped>
.task_type {
    :deep(.el-segmented__group) {
        gap: 10px;
        .el-segmented__item {
            padding: 0px 20px;
            height: 30px;
            @apply shadow-[0px_2px_6px_0px_#ebeefd] dark:shadow-[0px_2px_6px_0px_rgba(0,0,0,0.2)];
        }
    }
}
.draw_result {
    :deep(.el-loading-mask) {
        border-radius: 12px;
        z-index: 2 !important;
    }
    :deep(.el-scrollbar__view) {
        height: 100%;
    }

    .opt-btn:hover {
        color: var(--el-color-primary);
        background: var(--el-color-primary-light-9);
    }

    .opt-btn {
        flex: none;
        display: inline-block;
        transition: all 0.3s;
        cursor: pointer;
        font-size: 12px;
        margin-left: 7px;
        padding: 2px 12px;
        border-radius: 4px;
        color: #333333;
        background: #f2f3f6;
    }
}
.draw_loading {
    :deep(.el-loading-mask) {
        background-color: var(--el-bg-color-page) !important;
        .circular {
            display: none;
        }
    }
}
.image__item__icon {
    opacity: 0;
    opacity: 0;
    position: absolute;
    top: 4px;
    right: 4px;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background-color: rgba(0, 0, 0, 0.6);
}
.image__item:hover .image__item__icon {
    opacity: 1;
}
</style>
