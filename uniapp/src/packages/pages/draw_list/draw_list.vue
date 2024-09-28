<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <z-paging
        ref="pagingRef"
        v-model="dataList"
        :auto-clean-list-when-reload="false"
        :safe-area-inset-bottom="true"
        @query="queryList"
        :fixed="true"
    >
        <template #top>
            <view class="p-[20rpx] flex mx-[-10rpx]">
                <view
                    class="flex-1 px-[10rpx]"
                    v-for="item in categoryList"
                    :key="item.type"
                >
                    <view
                        class="bg-white py-[16rpx] rounded-[10rpx] shadow-light text-center"
                        :class="{
                            '!bg-primary text-white': item.type === currentType
                        }"
                        @click="currentType = item.type"
                    >
                        {{ item.name }}
                    </view>
                </view>
            </view>
        </template>
        <view class="px-[20rpx]">
            <view
                v-for="item in dataList"
                class="mb-[20rpx] rounded-[12rpx] shadow-[0px_3px_16px_0px_#EBEEFD] bg-white"
                :key="item.id"
            >
                <view
                    class="flex flex-col items-center justify-center h-[768rpx] px-[130rpx] text-center"
                    v-if="item.status === 1"
                >
                    <u-loading mode="flower" size="80"></u-loading>
                    <view class="text-xl font-medium my-[26rpx]">
                        正在生成中
                    </view>

                    <view class="text-muted">
                        预计花费30~60s时间，可离开后再回来查看效果喔～
                    </view>
                </view>
                <view
                    class="px-[30rpx]"
                    v-if="item.status === 2 || item.status === 3"
                    @click="toDetail(item)"
                >
                    <view class="py-[30rpx] flex">
                        <u-tag
                            :text="statusMap[item.status].label"
                            border-color="transparent"
                            :type="statusMap[item.status].type"
                        />
                        <view class="ml-auto flex items-center">
                            <template v-if="item.status === 3">
                                <image
                                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                    :src="IconCopy"
                                    alt="复制"
                                    @click.stop="copy(item.prompt)"
                                />
                                <image
                                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                    :src="IconDownload"
                                    alt="下载"
                                    @click.stop="downloadVideo(item)"
                                />
                                <image
                                    v-if="
                                        appStore.getSquareConfig.draw_award
                                            .is_open
                                    "
                                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                    :src="IconShare"
                                    alt="分享"
                                    @click.stop="shareDraw(item)"
                                />
                                <image
                                    v-if="
                                        item.engine === 'mj' && item?.able_cut
                                    "
                                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                    :src="IconCutout"
                                    alt="一键切图"
                                    @click.stop="handleSplit(item)"
                                />
                            </template>
                            <image
                                class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                :src="IconReload"
                                alt="重新生成"
                                @click.stop="regenerate(item)"
                            />
                            <image
                                class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                :src="IconDelete"
                                alt="删除"
                                @click.stop="removeVideo(item.id)"
                            />
                        </view>
                    </view>
                    <template v-if="item.status === 2">
                        <view
                            class="flex min-h-[472rpx] flex-col justify-center items-center"
                        >
                            <image
                                class="w-[300rpx] h-[300rpx]"
                                :src="ImageError"
                            />
                            <view class="text-xl font-medium"> 绘图失败 </view>

                            <view
                                v-if="item.fail_reason"
                                class="text-muted mt-[26rpx]"
                            >
                                错误信息：{{ item.fail_reason }}
                            </view>
                        </view>
                    </template>
                    <template v-else-if="item.status === 3">
                        <view class="preview-picture" @tap.stop>
                            <!--    单张图片    -->
                            <u-image
                                v-if="!Array.isArray(item.image)"
                                :src="item.thumbnail || item.image"
                                width="640rpx"
                                height="640rpx"
                                mode="aspectFit"
                                border-radius="0"
                                @tap="onPreview([item.image], 0)"
                            >
                                <template #loading>
                                    <view
                                        class="flex flex-col justify-center items-center w-[640rpx] h-[640rpx] bg-[#F7F9FD]"
                                    >
                                        <u-loading
                                            mode="circle"
                                            :color="$theme.primaryColor"
                                            size="40"
                                        ></u-loading>
                                        <view
                                            class="text-primary text-sm mt-[20rpx]"
                                        >
                                            加载中
                                        </view>
                                    </view>
                                </template>
                            </u-image>
                            <!--    多张图片    -->
                            <template v-else>
                                <view
                                    class="image__item relative inline-block"
                                    v-for="(citem, cindex) in item.image"
                                    :key="cindex"
                                >
                                    <u-image
                                        class="inline-block"
                                        width="280"
                                        mode="widthFix"
                                        :src="citem"
                                        @tap="onPreview(item.image, cindex)"
                                    >
                                        <template #loading>
                                            <view
                                                class="bg-[#F7F9FD]"
                                                style="
                                                    width: 280rpx;
                                                    height: 300rpx;
                                                "
                                            />
                                        </template>
                                    </u-image>
                                    <view
                                        class="image__item__icon"
                                        @click="shareDraw(item, citem)"
                                    >
                                        <u-icon
                                            name="share"
                                            color="#ffffff"
                                            size="34"
                                        ></u-icon>
                                    </view>
                                </view>
                            </template>
                        </view>
                    </template>
                    <view class="line-clamp-2 mt-[20rpx]">
                        {{ item.prompt }}
                    </view>

                    <view
                        class="mt-4"
                        v-if="item.status === 3 && item.engine == 'mj'"
                    >
                        <template
                            v-if="
                                !item?.able_actions?.includes(
                                    'low_variation'
                                ) && item?.able_actions?.length
                            "
                        >
                            <view class="flex flex-none min-w-[272px]">
                                <text class="text-xs">放大图片</text>
                                <text
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'upscale1')"
                                    >左上</text
                                >
                                <text
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'upscale2')"
                                    >右上</text
                                >
                                <text
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'upscale3')"
                                    >左下</text
                                >
                                <text
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'upscale4')"
                                    >右下</text
                                >
                            </view>
                            <view
                                class="flex flex-none min-w-[272px] mt-[15px]"
                            >
                                <text class="text-xs">变体图片</text>
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'variation1')"
                                    >左上</view
                                >
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'variation2')"
                                    >右上</view
                                >
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'variation3')"
                                    >左下</view
                                >
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'variation4')"
                                    >右下</view
                                >
                            </view>
                        </template>
                        <template v-else-if="item?.able_actions?.length">
                            <view>
                                <text>调整</text>
                                <view
                                    class="opt-btn"
                                    @click="
                                        handleMjEdit(item, 'high_variation')
                                    "
                                    >微调(强)</view
                                >
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'low_variation')"
                                    >微调(弱)</view
                                >
                                <!--                        <view class="opt-btn" @click="onLocalityReDrawing(item)">局部重绘</view>-->
                            </view>
                            <view
                                class="flex flex-none min-w-[272px] mt-[15px]"
                            >
                                <text>变化</text>
                                <template
                                    v-if="
                                        item?.able_actions?.includes(
                                            'outpaint_1.5x'
                                        )
                                    "
                                >
                                    <view
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(item, 'outpaint_1.5x')
                                        "
                                        >变焦1.5x</view
                                    >
                                    <view
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(item, 'outpaint_2x')
                                        "
                                        >变焦2x</view
                                    >
                                </template>
                                <template
                                    v-if="
                                        item?.able_actions?.includes(
                                            'upscale_2x'
                                        )
                                    "
                                >
                                    <view
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(item, 'upscale_2x')
                                        "
                                        >高清2x</view
                                    >
                                    <view
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(item, 'upscale_4x')
                                        "
                                        >高清4x</view
                                    >
                                </template>
                                <template
                                    v-if="
                                        item?.able_actions?.includes(
                                            'upscale_subtle'
                                        )
                                    "
                                >
                                    <view
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(item, 'upscale_subtle')
                                        "
                                        >弱变化</view
                                    >
                                    <view
                                        class="opt-btn"
                                        @click="
                                            handleMjEdit(
                                                item,
                                                'upscale_creative'
                                            )
                                        "
                                        >强变化</view
                                    >
                                </template>
                            </view>
                            <!--    第一次放大    -->
                            <view
                                class="flex flex-none min-w-[272px] mt-[15px]"
                                v-if="item?.able_actions?.includes('pan_left')"
                            >
                                <text>拉伸</text>
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'pan_left')"
                                    >⬅️</view
                                >
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'pan_right')"
                                >
                                    ➡️</view
                                >
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'pan_up')"
                                    >⬆️</view
                                >
                                <view
                                    class="opt-btn"
                                    @click="handleMjEdit(item, 'pan_down')"
                                    >⬇️</view
                                >
                            </view>
                        </template>
                    </view>

                    <view class="flex items-center py-[30rpx]">
                        <view class="text-muted mr-auto">
                            时间：{{ item.create_time }}
                        </view>
                        <view class="ml-[20rpx]">
                            <u-tag
                                :text="DrawResultTypeEnum[item.type as 1 | 2 | 3 | 4]"
                                size="mini"
                                border-color="transparent"
                            />
                        </view>
                    </view>
                </view>
            </view>
        </view>
    </z-paging>

    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->

    <u-popup
        v-model="actionState.show"
        mode="center"
        border-radius="15"
        closeable
    >
        <view class="w-[650rpx] p-[40rpx]">
            <view class="text-lg font-bold text-center mb-[40rpx]">
                下载图片
            </view>
            <view class="mb-[40rpx]">
                当前环境不支持下载，请复制链接到浏览器打开下载
            </view>

            <u-button type="primary" shape="circle" @click="copyLink">
                复制链接
            </u-button>
        </view>
    </u-popup>

    <draw-share
        v-show="showShare"
        ref="shareRef"
        @close="showShare = false"
        @success="(val) => sharedIds.push(val)"
    ></draw-share>
</template>

<script setup lang="ts">
import { reactive, ref, shallowRef, watch, onUnmounted, nextTick } from 'vue'
import { drawingRecord, drawingDelete, DrawRecordItem } from '@/api/draw'
import { useRoute, useRouter } from 'uniapp-router-next'
import { isWeixinClient } from '@/utils/client'
import { useCopy } from '@/hooks/useCopy'
import { downloadFile } from '@/utils/download'
import { useAppStore } from '@/stores/app'
import { onLoad } from '@dcloudio/uni-app'
import { useImageSplit } from './hooks/useImageSplit'

import IconCopy from '@/static/images/common/icon_copy.png'
import IconDownload from '@/static/images/common/icon_download.png'
import IconReload from '@/static/images/common/icon_reload.png'
import IconShare from '@/static/images/common/icon_share.png'
import IconCutout from '@/static/images/common/icon_cutout.png'
import IconDelete from '@/static/images/common/icon_delete.png'
import ImageError from '@/packages/static/images/drawing/error.png'
import DrawShare from './_components/draw-share.vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const DrawResultTypeEnum = {
    1: '文生图',
    2: '图生图',
    3: '选中放大',
    4: '选中变换'
}

const route = useRoute()
const router = useRouter()
const pagingRef = shallowRef()
const appStore = useAppStore()
const { copy } = useCopy()
const dataList = ref<DrawRecordItem[]>([])
const statusMap: Record<
    number,
    {
        label: string
        type: 'success' | 'warning' | 'error'
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
        type: 'error'
    },
    3: {
        label: '生成成功',
        type: 'success'
    }
}
const categoryList = [
    {
        name: '全部',
        type: -1
    },
    {
        name: '生成中',
        type: 1
    },
    {
        name: '生成成功',
        type: 3
    },
    {
        name: '生成失败',
        type: 2
    }
]
const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])

const currentModel = ref('')
const currentType = ref(-1)
const actionState = reactive({
    show: false,
    item: {} as any
})

const toDetail = (item: DrawRecordItem) => {
    if (item.status === 2) return
    router.navigateTo({
        path: '/packages/pages/draw_detail/draw_detail',
        query: {
            id: item.id
        }
    })
}

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
        const res = await uni.showModal({
            title: '温馨提示',
            content: '该图片已分享过，是否确认重复分享？'
        })
        if (res.cancel) {
            return
        }
    }
    showShare.value = true
    await nextTick()
    setTimeout(() => shareRef.value.open(params), 50)
}

const { images, splitImage } = useImageSplit()
const handleSplit = async (item: DrawRecordItem) => {
    if (Array.isArray(item.image)) {
        return
    }
    uni.showLoading({
        title: '图片拆分中...'
    })
    try {
        await splitImage(item.image as string)
        console.log(images.value)
        item.image = images.value
    } finally {
        uni.hideLoading()
    }
}

const onPreview = (urls: string[], index: number) => {
    uni.previewImage({
        urls: urls,
        current: index
    })
}

const removeVideo = async (id: number) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await drawingDelete({ ids: [id] })
    refresh()
}

const regenerate = async (item: any) => {
    const params: any = {
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

    const pages = getCurrentPages()
    if (pages.length > 1) {
        await router.navigateBack()
        uni.$emit('drawRegenerate', params)
    } else {
        await router.redirectTo({
            path: '/packages/pages/video/video',
            query: { data: JSON.stringify(params) }
        })
    }
}

/**
 *  MJ 操作
 */
const handleMjEdit = async (item: any, action: string) => {
    const params: any = {
        action,
        draw_model: item.engine,
        image_mask: item.image_base,
        negative_prompt: item.negative_prompt,
        prompt: item.prompt,
        size: item.scale,
        origin_task_id: item.task_id,
        complex_params: JSON.parse(item.complex_params)
    }
    if (item.image_base) {
        params.draw_type = 'img2img'
    }

    const pages = getCurrentPages()
    if (pages.length > 1) {
        await router.navigateBack()
        uni.$emit('drawRegenerate', params)
    } else {
        await router.redirectTo({
            path: '/packages/pages/video/video',
            query: { data: JSON.stringify(params) }
        })
    }
}

const copyLink = async () => {
    await copy(actionState.item.image)
    actionState.show = false
}

const downloadVideo = (item: any) => {
    if (isWeixinClient()) {
        actionState.show = true
        actionState.item = item
    } else {
        downloadFile(item.image, '图片', 'image')
    }
}

const timer = shallowRef()
const checkHasGenerating = () => {
    clearTimeout(timer.value)
    if ([-1, 1].includes(currentType.value)) {
        const hasGenerating = dataList.value.some((item) => item.status === 1)
        if (hasGenerating) {
            timer.value = setTimeout(() => {
                refresh()
            }, 6000)
        }
    }
}
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await drawingRecord({
            model: currentModel.value,
            page_no: pageNo,
            page_size: pageSize,
            status: currentType.value
        })

        pagingRef.value.complete(lists)
    } catch (error) {
        pagingRef.value.complete(false)
    } finally {
        setTimeout(() => {
            checkHasGenerating()
        }, 100)
    }
}

const refresh = () => {
    pagingRef.value?.refresh()
}

watch(currentType, () => {
    refresh()
})

onUnmounted(() => {
    clearTimeout(timer.value)
})

onLoad(() => {
    const query = route.query
    const model = query.model as string
    if (model) {
        currentModel.value = model
    }
})
</script>

<style lang="scss" scoped>
.preview-picture {
    overflow: hidden;
    margin: 0 auto;
    width: 100%;
    min-height: 640rpx;
    display: flex;
    flex-wrap: wrap;
    align-content: center;
    // #ifndef MP
    justify-content: center;
    // #endif
    .image__item {
        // #ifdef MP
        margin: 10rpx 16rpx;
        // #endif
        // #ifndef MP
        margin: 10rpx;
        // #endif
        &__icon {
            position: absolute;
            top: 10rpx;
            right: 10rpx;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 64rpx;
            height: 64rpx;
            border-radius: 8rpx;
            background-color: rgba(0, 0, 0, 0.6);
        }
    }
}
.opt-btn {
    display: inline-block;
    transition: all 0.3s;
    font-size: 26rpx;
    margin-left: 14rpx;
    padding: 8rpx 24rpx;
    text-align: center;
    border-radius: 8rpx;
    color: #333333;
    background: #f2f3f6;
}
</style>
