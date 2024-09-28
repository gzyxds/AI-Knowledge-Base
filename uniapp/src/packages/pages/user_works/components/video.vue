<template>
    <z-paging
        ref="pagingRef"
        v-model="dataList"
        :auto-clean-list-when-reload="false"
        :safe-area-inset-bottom="true"
        @query="queryList"
        :fixed="false"
        height="100%"
    >
        <view class="w-full h-full px-[20rpx]">
            <view
                v-for="item in dataList"
                class="mb-[20rpx] rounded-[12rpx] relative shadow-[0px_3px_16px_0px_#EBEEFD] bg-white"
                :key="item.id"
                @click="handleTouch(item.id)"
            >
                <view class="absolute right-0 bottom-[30rpx]" v-if="isBatch">
                    <u-checkbox
                        :model-value="selectId.includes(item.id)"
                        shape="circle"
                    >
                    </u-checkbox>
                </view>
                <view class="px-[30rpx]">
                    <view class="py-[30rpx] flex">
                        <view class="mr-[20rpx]">
                            <u-tag
                                :text="item.type_desc"
                                size="mini"
                                border-color="transparent"
                            />
                        </view>
                        <view class="ml-auto flex items-center">
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

                            <image
                                class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                                :src="IconDelete"
                                alt="删除"
                                @click="removeVideo(item.id)"
                            />
                        </view>
                    </view>

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
                    <view class="line-clamp-2 mt-[20rpx]">{{
                        item.prompt
                    }}</view>
                    <view class="flex items-center py-[30rpx]">
                        <view class="text-muted mr-auto">
                            时间：{{ item.create_time }}
                        </view>
                    </view>
                </view>
            </view>
        </view>
        <template #bottom>
            <view
                v-if="isBatch"
                class="h-[120rpx] flex items-center px-[20rpx] rounded-t-[25rpx] bg-white shadow-light"
            >
                <u-checkbox v-model="isSelectAll" shape="circle">
                    全选
                </u-checkbox>
                <view
                    >已选<text class="text-error">{{ selectId.length }}</text
                    >/{{ dataList.length }}</view
                >
                <view class="ml-auto">
                    <u-button
                        size="medium"
                        type="error"
                        :disabled="!selectId.length"
                        @click="removeVideo(selectId)"
                    >
                        删除
                    </u-button>
                </view>
            </view>
        </template>
    </z-paging>

    <u-popup
        v-model="actionState.show"
        mode="center"
        border-radius="15"
        closeable
    >
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
</template>

<script setup lang="ts">
import { computed, reactive, ref, shallowRef, watch, onUnmounted } from 'vue'
import { getVideoLists, deleteVideo } from '@/api/video'
import IconCopy from '@/static/images/common/icon_copy.png'
import IconDownload from '@/static/images/common/icon_download.png'
import IconDelete from '@/static/images/common/icon_delete.png'
import { useRouter } from 'uniapp-router-next'
import { isWeixinClient } from '@/utils/client'
import { useCopy } from '@/hooks/useCopy'
import { downloadFile } from '@/utils/download'

const props = defineProps<{
    isBatch: boolean
}>()
const router = useRouter()
const pagingRef = shallowRef()
const { copy } = useCopy()
const dataList = ref<any[]>([])
const selectId = ref<number[]>([])
const isSelectAll = computed({
    get() {
        return selectId.value.length === dataList.value.length
    },
    set() {
        if (selectId.value.length === dataList.value.length) {
            selectId.value = []
        } else {
            selectId.value = dataList.value.map((item) => item.id)
        }
    }
})

watch(
    () => props.isBatch,
    () => {
        selectId.value = []
    }
)

const handleTouch = (id: number) => {
    if (props.isBatch) {
        const index = selectId.value.findIndex((item) => item === id)
        if (index > -1) {
            selectId.value.splice(index, 1)
        } else {
            selectId.value.push(id)
        }
        return
    }
}

const removeVideo = async (id: number | number[]) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await deleteVideo({ id })
    selectId.value = []
    refresh()
}

const actionState = reactive({
    show: false,
    item: {} as any
})
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

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await getVideoLists({
            page_no: pageNo,
            page_size: pageSize,
            status: 2
        })

        pagingRef.value.complete(lists)
    } catch (error) {
        pagingRef.value.complete(false)
    } finally {
    }
}

const refresh = () => {
    pagingRef.value?.refresh()
}

onUnmounted(() => {
    clearTimeout(timer.value)
})
</script>
