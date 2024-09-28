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
        <view class="flex flex-wrap px-[10rpx]">
            <view
                v-for="item in dataList"
                class="w-[50%] px-[10rpx] mb-[20rpx]"
                :key="item.id"
            >
                <view
                    class="h-[390rpx] relative rounded-[15rpx] overflow-hidden"
                    @click="handleTouch(item.id)"
                >
                    <view class="w-full h-full">
                        <u-image
                            :src="item.image_url"
                            width="100%"
                            height="100%"
                        />
                    </view>
                    <view class="flex flex-col h-full absolute left-0 right-0 top-0 bottom-0 z-[999]">
                        <view class="p-[20rpx] flex-1">
                            <view
                                class="bg-[rgba(0,0,0,0.5)] inline-flex text-white p-[10rpx] rounded-full text-xs items-center"
                            >
                                <image
                                    src="@/static/images/icon/icon_music.png"
                                    class="w-[24rpx] h-[24rpx]"
                                />
                                <text class="ml-[6rpx]">
                                    {{ item.duration }}
                                </text>
                            </view>
                        </view>
                        <view
                            class="bg-[rgba(0,0,0,0.3)] px-[20rpx] py-[25rpx] text-white"
                            style="backdrop-filter: blur(10px); border-radius: 0 0 15rpx 15rpx;"
                        >
                            <view class="line-clamp-1 font-bold">{{
                                item.title
                            }}</view>
                            <view class="flex text-xs mt-[15rpx] items-center">
                                <view class="mr-auto">{{
                                    item.create_time
                                }}</view>
                                <view @click.stop="showAction(item)">
                                    <u-icon name="more-dot-fill" :size="32" />
                                </view>
                            </view>
                        </view>
                    </view>
                    <view
                        class="h-full absolute inset-0 bg-white z-10 flex items-center justify-center flex-col"
                        v-if="item.status == 3 || item.status == 1"
                    >
                        <template v-if="item.status == 1">
                            <u-loading :size="50" mode="flower" />
                            <view class="text-content text-sm mt-[20rpx]">
                                歌曲生成中，请稍等！
                            </view>
                        </template>
                        <template v-else>
                            <u-icon :name="music_fail" :size="50" />
                            <view class="text-content text-sm mt-[20rpx]">
                                音乐生成失败
                            </view>
                            <view
                                class="absolute bottom-0 w-full px-[20rpx] py-[25rpx] flex items-center text-muted"
                            >
                                <view class="mr-auto text-xs">{{
                                    item.create_time
                                }}</view>
                                <view @click.stop="showAction(item)">
                                    <u-icon name="more-dot-fill" :size="32" />
                                </view>
                            </view>
                        </template>
                    </view>
                    <view class="absolute right-0 top-[20rpx]" v-if="isBatch">
                        <u-checkbox
                            :model-value="selectId.includes(item.id)"
                            shape="circle"
                        >
                        </u-checkbox>
                    </view>
                </view>
            </view>
        </view>
        <template #bottom>
            <view
                class="h-[120rpx] flex items-center px-[20rpx] rounded-t-[25rpx] bg-white shadow-light"
                v-if="isBatch"
            >
                <u-checkbox v-model="isSelectAll" shape="circle">
                    全选
                </u-checkbox>
                <view
                    >已选<text class="text-error">{{ selectId.length }}</text
                    >/{{ dataList.length }}首音乐</view
                >
                <view class="ml-auto">
                    <u-button
                        size="medium"
                        type="error"
                        :disabled="!selectId.length"
                        @click="removeMusic(selectId)"
                    >
                        删除
                    </u-button>
                </view>
            </view>
        </template>
    </z-paging>
    <u-action-sheet
        safe-area-inset-bottom
        :list="menuOptions"
        v-model="actionState.show"
        @click="menuAction"
    ></u-action-sheet>
    <u-popup v-model="showDownload" mode="center" border-radius="15" closeable>
        <view class="w-[650rpx] p-[40rpx]">
            <view class="text-lg font-bold text-center mb-[40rpx]">
                下载音乐
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
import { getMusicLists, deleteMusic } from '@/api/music'
import { downloadFile } from '@/utils/download'
import music_fail from '@/static/images/icon/icon_music_fail.png'
import { useRouter } from 'uniapp-router-next'
import { isWeixinClient } from '@/utils/client'
import { useCopy } from '@/hooks/useCopy'

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
const actionState = reactive({
    show: false,
    item: {} as any
})
const menuOptions = computed(() => {
    const options = [
        {
            value: 'delete',
            text: '删除',
            color: '#EA0000'
        }
    ] as any
    if (actionState.item.status == 2) {
        options.unshift({
            value: 'download',
            text: '下载'
        })
    }
    return options
})
const showAction = (item: any) => {
    actionState.show = true
    actionState.item = item
}
const removeMusic = async (id: number | number[]) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await deleteMusic({ id })
    selectId.value = []
    refresh()
}

const showDownload = ref()
const menuAction = async (index: number) => {
    const action = menuOptions.value[index].value
    const item = actionState.item
    switch (action) {
        case 'delete': {
            removeMusic(item.id)
            break
        }
        case 'download': {
            //#ifdef H5
            if (isWeixinClient()) {
                showDownload.value = true
            } else {
                downloadFile(item.audio_url, item.title)
            }
            //#endif

            //#ifdef MP-WEIXIN
            showDownload.value = true
            //#endif
        }
    }
}
const copyLink = async () => {
    await copy(actionState.item.audio_url)
    showDownload.value = false
}
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
    router.navigateTo({
        path: '/packages/pages/music_player/music_player',
        query: { id, mode: 'user_works' }
    })
}

const timer = shallowRef()

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await getMusicLists({
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
