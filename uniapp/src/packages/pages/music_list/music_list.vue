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
        <view class="flex flex-wrap px-[10rpx]">
            <view
                v-for="item in dataList"
                class="w-[50%] px-[10rpx] mb-[20rpx]"
                :key="item.id"
            >
                <view
                    class="h-[390rpx] relative rounded-[15rpx] overflow-hidden"
                    @click="toPlay(item.id)"
                >
                    <view class="w-full h-full">
                        <u-image
                            :src="item.image_url"
                            width="100%"
                            height="100%"
                        />
                    </view>
                    <view class="flex flex-col h-full absolute left-0 right-0 top-0 bottom-0 z-[999]">
                        <view class=" p-[20rpx] flex-1">
                            <view class="flex justify-between">
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

                                <view
                                    v-if="appStore.getSquareConfig.music_award.is_open"
                                    class="bg-[rgba(0,0,0,0.5)] inline-flex text-white w-[48rpx] h-[48rpx] rounded-full text-xs items-center justify-center"
                                    @click.stop="shareMusic(item.id, item.is_share)"
                                >
                                    <u-icon name="share"></u-icon>
                                </view>
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
                        class="h-full absolute left-0 right-0 top-0 bottom-0 z-[999] bg-white flex items-center justify-center flex-col"
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
                </view>
            </view>
        </view>
    </z-paging>

    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->

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

    <music-share
        v-if="showShare"
        ref="shareRef"
        @close="showShare = false"
        @success="(val) => sharedIds.push(val)"
    ></music-share>
</template>

<script setup lang="ts">
import {computed, reactive, ref, shallowRef, watch, onUnmounted, nextTick} from 'vue'
import { getMusicLists, deleteMusic } from '@/api/music'
import { downloadFile } from '@/utils/download'
import { useRouter } from 'uniapp-router-next'
import { isWeixinClient } from '@/utils/client'
import { useCopy } from '@/hooks/useCopy'
import { useAppStore } from '@/stores/app'

import music_fail from '@/static/images/icon/icon_music_fail.png'
import MusicShare from './_components/music-share.vue'

const router = useRouter()
const appStore = useAppStore()
const pagingRef = shallowRef()
const { copy } = useCopy()
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


const shareMusic = async (records_id: number, is_share?: number) => {
    if (sharedIds.value.includes(records_id) || is_share) {
        await uni.showModal({
            title: '温馨提示',
            content: '该音乐已分享过了！'
        })
        return
    }
    showShare.value = true
    await nextTick()
    setTimeout(() => shareRef.value.open(records_id), 50)
}

const showAction = (item: any) => {
    actionState.show = true
    actionState.item = item
}
const removeMusic = async (id: number) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await deleteMusic({ id })
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
const toPlay = (id: number) => {
    router.navigateTo({
        path: '/packages/pages/music_player/music_player',
        query: { id }
    })
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
        const { lists } = await getMusicLists({
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
