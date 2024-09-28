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
                >
                    <view class="py-[30rpx] flex">
                        <u-tag
                            v-if="item.status === 2"
                            :text="item.status_desc"
                            border-color="transparent"
                            type="success"
                        />
                        <u-tag
                            v-if="item.status === 3"
                            :text="item.status_desc"
                            border-color="transparent"
                            type="error"
                        />
                        <view class="ml-auto flex items-center">
                            <template v-if="item.status === 2">
                                <image
                                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                    :src="IconCopy"
                                    alt="复制"
                                    @click="copy(item.prompt)"
                                />
                                <image
                                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                    :src="IconDownload"
                                    alt="下载"
                                    @click="downloadVideo(item)"
                                />

                            </template>
                            <image
                                class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                :src="IconReload"
                                alt="重新生成"
                                @click="regenerate(item)"
                            />
                            <image
                                v-if="appStore.getSquareConfig.video_award.is_open"
                                class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                :src="IconShare"
                                alt="分享"
                                @click="shareVideo(item.id, item.is_share)"
                            />
                            <image
                                class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                :src="IconDelete"
                                alt="删除"
                                @click="removeVideo(item.id)"
                            />
                        </view>
                    </view>
                    <template v-if="item.status === 3">
                        <view
                            class="flex min-h-[472rpx] flex-col justify-center items-center"
                        >
                            <image
                                class="w-[300rpx] h-[300rpx] "
                                :src="ImageError"
                            />
                            <view class="text-xl font-medium ">
                                生成视频失败
                            </view>

                            <view v-if="item.fail_reason" class="text-muted mt-[26rpx]">
                                错误信息：{{ item.fail_reason }}
                            </view>
                        </view>
                    </template>
                    <template v-else-if="item.status === 2">
                        <view class="h-[472rpx]">
                            <video
                                class="w-full h-full rounded-[10rpx] overflow-hidden"
                                preload
                                playsinline
                                webkit-playsinline
                                x-webkit-airplay="allow"
                                x5-video-player-fullscreen="true"
                                x5-video-player-type="h5"
                                :src="item.video_url"
                            />
                        </view>

                    </template>
                    <view class="line-clamp-2 mt-[20rpx]">{{
                            item.prompt
                        }}
                    </view>
                    <view class="flex items-center py-[30rpx]">
                        <view class="text-muted mr-auto">
                            时间：{{ item.create_time }}
                        </view>
                        <view class="ml-[20rpx]">
                            <u-tag
                                :text="item.type_desc"
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

    <u-popup v-model="actionState.show" mode="center" border-radius="15" closeable>
        <view class="w-[650rpx] p-[40rpx]">
            <view class="text-lg font-bold text-center mb-[40rpx]">
                下载视频
            </view>
            <view class="mb-[40rpx]">
                当前环境不支持下载，请复制链接到浏览器打开下载
            </view>

            <u-button type="primary" shape="circle" @click="copyLink">
                复制链接
            </u-button>
        </view>
    </u-popup>

    <video-share
        v-if="showShare"
        ref="shareRef"
        @close="showShare = false"
        @success="(val) => sharedIds.push(val)"
    ></video-share>
</template>

<script setup lang="ts">
import {reactive, ref, shallowRef, watch, onUnmounted, nextTick} from 'vue'
import {getVideoLists, deleteVideo} from '@/api/video'
import {useRouter} from 'uniapp-router-next'
import {isWeixinClient} from '@/utils/client'
import {useCopy} from '@/hooks/useCopy'
import {downloadFile} from '@/utils/download'
import {useAppStore} from "@/stores/app";

import IconCopy from '@/static/images/common/icon_copy.png'
import IconDownload from '@/static/images/common/icon_download.png'
import IconReload from '@/static/images/common/icon_reload.png'
import IconShare from '@/static/images/common/icon_share.png'
import IconDelete from '@/static/images/common/icon_delete.png'
import ImageError from '@/packages/static/images/drawing/error.png'
import VideoShare from './_components/video-share.vue'

const router = useRouter()
const appStore = useAppStore()
const pagingRef = shallowRef()
const {copy} = useCopy()
const dataList = ref<any[]>([])
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
        type: 2
    },
    {
        name: '生成失败',
        type: 3
    }
]
const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])

const currentType = ref(-1)
const actionState = reactive({
    show: false,
    item: {} as any
})

const shareVideo = async (records_id: number, is_share?: number) => {
    if (sharedIds.value.includes(records_id) || is_share) {
        await uni.showModal({
            title: '温馨提示',
            content: '该视频已分享过了！'
        })
        return
    }
    showShare.value = true
    await nextTick()
    setTimeout(() => shareRef.value.open(records_id), 50)
}

const removeVideo = async (id: number) => {
    const {cancel} = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await deleteVideo({id})
    refresh()
}

const regenerate = async (item: any) => {
    const data = {
        type: item.type,
        prompt: item.prompt,
        scale: item.scale,
        image: item.image,
        style_id: item.style_id
    }

    const pages = getCurrentPages()
    if (pages.length > 1) {
        await router.navigateBack()
        uni.$emit('videoRegenerate', data)
    } else {
        await router.redirectTo({
            path: '/packages/pages/video/video',
            query: {data: JSON.stringify(data)}
        })
    }

}

const copyLink = async () => {
    await copy(actionState.item.video_url)
    actionState.show = false
}

const downloadVideo = (item: any) => {
    if (isWeixinClient()) {
        actionState.show = true
        actionState.item = item
    } else {
        downloadFile(item.video_url, '视频', 'video')
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
        const {lists} = await getVideoLists({
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
</script>
