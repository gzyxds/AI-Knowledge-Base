<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col bg-white" v-if="musicList.length">
        <view class="dot flex py-[20rpx] justify-center">
            <view
                class="w-[15rpx] h-[15rpx] rounded-[50%] bg-[#D8D8D8] mx-[8rpx]"
                v-for="(item, index) in swiperList"
                :key="item"
                :class="{
                    '!bg-[#767676]': swiperIndex === index
                }"
            ></view>
        </view>
        <view class="flex-1 min-h-0">
            <swiper
                class="h-full"
                :current="swiperIndex"
                @change="swiperChange"
            >
                <swiper-item v-for="item in swiperList" :key="item">
                    <template v-if="item === 'default'">
                        <view class="h-full flex flex-col text-center">
                            <view class="flex items-center flex-col">
                                <image
                                    :src="currentMusic.image_url"
                                    class="w-[550rpx] h-[450rpx] rounded-[50rpx]"
                                    mode="aspectFill"
                                />
                                <view class="mt-[40rpx] px-[20rpx]">
                                    <view class="text-[50rpx]">{{
                                            currentMusic.title
                                        }}
                                    </view>
                                    <view
                                        v-if="currentMusic.style_desc"
                                        class="text-muted py-[20rpx]"
                                    >
                                        风格：{{ currentMusic.style_desc }}
                                    </view>
                                </view>
                            </view>
                            <view class="flex-1 min-h-0">
                                <scroll-view class="h-full" scroll-y>
                                    <view class="p-[20rpx] text-center">
                                        <text
                                            class="text-lg text-content leading-loose"
                                        >
                                            {{ currentMusic.lyric }}
                                        </text>
                                    </view>
                                </scroll-view>
                            </view>
                        </view>
                    </template>
                    <template v-else-if="item === 'lyric'">
                        <scroll-view class="h-full" scroll-y>
                            <view class="p-[20rpx] text-center">
                                <view>
                                    <view class="text-[50rpx]">{{
                                            currentMusic.title
                                        }}
                                    </view>
                                    <view
                                        v-if="currentMusic.style_desc"
                                        class="text-muted py-[20rpx]"
                                    >
                                        风格：{{ currentMusic.style_desc }}
                                    </view>
                                </view>
                                <text
                                    class="text-lg text-content leading-loose"
                                >
                                    {{ currentMusic.lyric }}
                                </text>
                            </view>
                        </scroll-view>
                    </template>
                </swiper-item>
            </swiper>
        </view>
        <view class="pb-[40rpx] px-[20rpx]">
            <view class="flex items-center">
                <view class="w-[100rpx] text-content text-right">
                    {{ transTime(currentTime) }}
                </view>
                <view class="flex-1">
                    <uv-slider
                        :disabled="!duration"
                        :model-value="currentTime"
                        :block-size="12"
                        :block-color="$theme.primaryColor"
                        :max="duration || 100"
                        :activeColor="$theme.primaryColor"
                        @input="changeTime"
                    ></uv-slider>
                </view>
                <view class="w-[100rpx] text-content">
                    {{ duration ? transTime(duration) : currentMusic.duration }}
                </view>
            </view>
            <view class="flex items-center">
                <view class="w-[150rpx] flex justify-center items-center" @click="handleDownload(currentMusic)">
                    <u-icon name="download" :size="48" color="#999999"/>
                </view>
                <view class="flex-1 flex items-center justify-center">
                    <view @click="prevOrNext(-1)" class="p-[20rpx]">
                        <image
                            src="@/static/images/icon/icon_prev.png"
                            class="w-[34rpx] h-[34rpx]"
                            :class="{
                                'opacity-70': currentIndex === 0
                            }"
                        />
                    </view>
                    <view
                        class="w-[128rpx] h-[128rpx] text-white flex justify-center items-center rounded-[50%] mx-[60rpx] play-brn"
                        @click="togglePlay"
                    >
                        <u-icon v-if="isPlaying" name="pause" :size="40"/>
                        <u-icon v-else name="play-right-fill" :size="40"/>
                    </view>
                    <view @click="prevOrNext(1)" class="p-[20rpx]">
                        <image
                            :class="{
                                'opacity-70':
                                    currentIndex === musicList.length - 1
                            }"
                            src="@/static/images/icon/icon_next.png"
                            class="w-[34rpx] h-[34rpx]"
                        />
                    </view>
                </view>
                <view class="w-[150rpx] flex justify-end px-[25rpx]">
                    <view
                        class="w-[50rpx] h-[50rpx] border-2 border-solid border-muted rounded-[50%] text-muted font-bold flex items-center justify-center"
                        @click="swiperIndex = 1"
                    >
                        词
                    </view>
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
    </view>
</template>

<script setup lang="ts">
import {getMusicLists} from '@/api/music'
import {getMusicDetail, getMusicRecommendLists} from '@/api/square'
import {computed, ref, onMounted, watch} from 'vue'
import {useRoute, useRouter} from 'uniapp-router-next'
import {useMusicPlay} from './useMusicPlay'
import {downloadFile} from '@/utils/download'
import {isWeixinClient} from '@/utils/client'
import {useCopy} from '@/hooks/useCopy'
import {onUnload} from "@dcloudio/uni-app";
import {useUserStore} from "@/stores/user";

const {copy} = useCopy()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const musicList = ref([])

const swiperList = ['default', 'lyric']
const swiperIndex = ref(0)
const idToIndex = new Map()
const currentIndex = ref(-1)
const showDownload = ref<boolean>(false)
const audioUrl = ref<string>('')

const {play, duration, currentTime, seek, isPlaying, pause, setUrl} =
    useMusicPlay()

const togglePlay = () => {
    if (!isPlaying.value) {
        play()
    } else {
        pause()
    }
}

const prevOrNext = (num: number) => {
    const index = currentIndex.value
    let newIndex = index + num
    if (newIndex <= 0) {
        newIndex = 0
    }
    if (newIndex >= musicList.value.length - 1) {
        newIndex = musicList.value.length - 1
    }
    currentIndex.value = newIndex
}
const getData = async () => {
    // 如果有 id 就属于广场进入，请求广场列表，否则则是自己生成的音乐列表
    if (route.query.mode == 'square') {
        const detail = await getMusicDetail({
            id: route.query.id as string
        })
        detail.square_id = route.query.id
        const list = await getMusicRecommendLists()

        const index = list.findIndex(
            (item: any) => item.id == route.query.id
        )
        if (index === -1) {
            list.unshift(detail)
            musicList.value = list
        } else {
            musicList.value = list
        }
        musicList.value.forEach((item: any, index) => {
            idToIndex.set(item.square_id, index)
            console.log('没有找到音乐', idToIndex)
        })
    } else {
        const data = await getMusicLists({
            page_type: 0,
            status: 2
        })
        musicList.value = data.lists
        idToIndex.clear()
        musicList.value.forEach((item: any, index) => {
            idToIndex.set(item.id, index)
        })
    }
}

const handleDownload = (item: any) => {
    if (!userStore.isLogin) router.navigateTo('/pages/login/login')
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
    audioUrl.value = item.audio_url
}

const copyLink = async () => {
    await copy(audioUrl.value)
    showDownload.value = false
}

const changeTime = (value: number) => {
    seek(value)
}

const transTime = (time: number) => {
    let minute: string | number = parseInt(String(time / 60))
    let sec = Math.round(time % 60) + ''
    const isM0 = ':'
    if (minute == 0) {
        minute = '00'
    } else if (minute < 10) {
        minute = '0' + minute
    }
    if (sec.length == 1) {
        sec = '0' + sec
    }
    return minute + isM0 + sec
}

const swiperChange = (e: any) => {
    swiperIndex.value = e.detail.current
}
const currentMusic = computed<any>(
    () => musicList.value[currentIndex.value] || {}
)

watch(currentMusic, (value) => {
    if (value.audio_url) {
        setUrl(value.audio_url)
        play()
    }
})
onMounted(async () => {
    await getData()
    const index = idToIndex.get(route.query.id)

    if (index !== -1) currentIndex.value = index
})
onUnload(() => {
    pause()
    musicList.value = []
})
</script>
<style lang="scss">
page {
    height: 100%;
    overflow: hidden;
}

.play-brn {
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
}
</style>
