<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status class="h-full" :status="status">
        <view class="h-full flex flex-col">
            <view class="flex-1 min-h-0">
                <view class="px-[20rpx] bg-white" style="padding-bottom: 50px">

                    <!--      视频      -->
                    <view class="pt-[24rpx]" style="height: 720rpx;">
                        <video
                            class="w-full h-full rounded-[10rpx] overflow-hidden object-cover"
                            preload
                            autoplay
                            muted
                            loop
                            initial-time="0"
                            playsinline
                            :show-progress="false"
                            webkit-playsinline
                            x-webkit-airplay="allow"
                            x5-video-player-fullscreen="true"
                            x5-video-player-type="h5"
                            :src="videoData.video_url"
                        />
                    </view>

                    <!--      视频描述词      -->
                    <view class="mt-[30rpx]">
                        <view class="flex items-center justify-between">
                            <text class="text-lg font-medium">描述场景词</text>
                            <text class="text-primary text-base" @click="copy(videoData.prompt)">复制</text>
                        </view>
                        <view class="min-h-[150rpx] mt-[20rpx] p-[20rpx] bg-page rounded-lg">
                            <text
                                class="text-base leading-4"
                                style="letter-spacing: 1px"
                            >
                                {{ videoData.prompt }}
                            </text>
                        </view>
                    </view>

                    <!--      创作信息      -->
                    <view class="mt-[30rpx]" style="padding-bottom: 100rpx;">
                        <view class="text-lg font-medium">创作信息</view>
                        <view class="mt-[20rpx] p-[20rpx] bg-page rounded-lg text-main text-base">
                            <view class="flex items-center justify-between">
                                <text>创作者</text>
                                <text>{{ videoData.nickname || userStore?.userInfo?.nickname }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>生成时间</text>
                                <text>{{ videoData.create_time }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>视频类型</text>
                                <text>{{ videoData.type_desc }}</text>
                            </view>
                            <view class="flex items-center justify-between mt-[20rpx]">
                                <text>视频风格</text>
                                <text>{{ videoData.style_desc }}</text>
                            </view>
                        </view>
                    </view>

                    <view class="video-detail-footer flex justify-between items-center px-[60rpx]">
                        <view class="flex text-xs text-[#666]">
                            <view class="text-center" @click.stop="handlePraise">
                                <view class="praise">
                                    <view
                                        class="praise-animate"
                                        :style="{
                                            backgroundImage: `url(${domain}resource/image/api/default/praise02.png)`
                                        }"
                                        :class="[videoData.is_collect ? 'praise-entry':'praise-leave']"
                                    >
                                    </view>
                                </view>
                                <view class="text-[22rpx] text-content">收藏</view>
                            </view>

                            <view class="ml-7 text-center" @click.stop="handleDownload(videoData)">
                                <view class="mt-[2rpx] mb-[4rpx]">
                                    <u-icon name="download" size="42"></u-icon>
                                </view>
                                <view class="text-[22rpx] text-content">下载</view>
                            </view>
                        </view>
                        <u-button
                            type="primary"
                            size="default"
                            :customStyle="{
                                padding: '0 30rpx',
                                height: '82rpx',
                                width: '400rpx',
                                margin: '0'
                            }"
                            @click="handleDownload"
                        >
                            保存视频
                        </u-button>
                    </view>
                </view>
            </view>

            <!-- #ifdef H5 -->
            <!--    悬浮菜单    -->
            <floating-menu></floating-menu>
            <!-- #endif -->

            <u-popup v-model="showDownload" mode="center" border-radius="15" closeable>
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
        </view>
    </page-status>
</template>
<script setup lang="ts">
import {reactive, ref} from 'vue'
import {onLoad} from '@dcloudio/uni-app'
import {useRoute, useRouter} from 'uniapp-router-next'
import {useUserStore} from '@/stores/user'
import {getVideoDetail} from '@/api/video'
import {isWeixinClient} from '@/utils/client'
import {useCopy} from '@/hooks/useCopy'
import {downloadFile} from '@/utils/download'
import Video from "@/packages/pages/robot_square/_components/video.vue";
import {videoSquareCollect, getSquareVideoDetail} from "@/api/square";
import {PageStatusEnum} from "@/enums/appEnums";
import config from '@/config'
const domain = config.baseUrl

interface VideoRecordItem {
    status_desc: string
    style_desc: string
    type_desc: string
    id: number
    prompt: string
    type: 1 | 2 | 3 | 4
    tags: string
    image: string
    channel: string
    video_url: string
    style_id: string
    status: number
    is_collect?: number
    create_time: string
}

const {copy} = useCopy()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const status = ref(PageStatusEnum.LOADING)
const videoData = ref<VideoRecordItem>({
    status_desc: "生成成功",
    style_desc: "电影镜头",
    type_desc: "文生视频",
    id: 96,
    prompt: "",
    type: 1,
    tags: "电影镜头",
    image: "",
    nickname: '',
    channel: "openai_hk",
    video_url: "",
    style_id: "4",
    status: 2,
    create_time: ""
})
const collectData = reactive({
    id: ''
})
const showDownload = ref<boolean>(false)

const handlePraise = async () => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    try {
        await videoSquareCollect({
            records_id: collectData.id,
            status: videoData.value.is_collect ? 0 : 1
        })
        videoData.value.is_collect = videoData.value.is_collect ? 0 : 1
    } catch (e) {
        console.error(e)
        uni.$u.toast(JSON.stringify(e))
    }
}

const handleDownload = () => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    //#ifdef H5
    if (isWeixinClient()) {
        showDownload.value = true
    } else {
        downloadFile(videoData.value.video_url, '视频')
    }
    //#endif

    //#ifdef MP-WEIXIN
    showDownload.value = true
    //#endif
}

const copyLink = async () => {
    await copy(videoData.value.video_url)
    showDownload.value = false
}

const getDetail = async (id: number) => {
    try {
        if (route.query.collectId) {
            const data = await getSquareVideoDetail({
                id: id
            })
            videoData.value = data
        } else {
            const data = await getVideoDetail({
                id: id
            })
            videoData.value = data[0]
        }
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
}

onLoad(async () => {
    const id = route.query.id
    await getDetail(id as number)
    collectData.id = route.query.collectId as any
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>

<style lang="scss" scoped>
.video-detail-footer {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    border-radius: 20rpx 20rpx 0 0;
    height: calc(120rpx + env(safe-area-inset-bottom));
    padding: 0 60rpx env(safe-area-inset-bottom) 60rpx;
    background: #F6F6F6;
    box-shadow: 0 -3px 16px 0 rgba(219, 219, 244, 0.102);
}

.praise {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    position: relative;
    width: 42rpx;
    height: 42rpx;
    border-radius: 30px;
}

.praise-animate {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 54px;
    height: 54px;
    background-repeat: no-repeat;
    background-position: left;
    background-size: cover;
}

// 没点赞
.praise-leave {
    background-position: left;
}

// 点赞
.praise-entry {
    background-position: right;
    transition: background 1s steps(28);
}
</style>