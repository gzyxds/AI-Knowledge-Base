<template>
<!--    4xl:w-[2000px] mx-auto-->
    <div class="music-player-container h-full p-[16px] ">
        <div
            class="music-player flex flex-col min-h-0 h-full bg-body rounded-xl"
        >
            <div class="flex flex-1 min-h-0 h-full">
                <!--    Left Panel    -->
                <div class="flex flex-col flex-1 h-full min-h-0 p-[16px]">
                    <div
                        class="flex items-center cursor-pointer"
                        @click="$router.back()"
                    >
                        <div
                            class="flex w-[30px] bg-body p-[5px] text-bold rounded-[50%] text-primary shadow-light"
                        >
                            <Icon name="el-icon-Back" :size="18" />
                        </div>
                        <div class="text-xl flex-1 min-w-0 ml-[10px]">
                            音乐广场
                        </div>
                    </div>

                    <div
                        class="flex flex-col flex-1 h-full min-h-0"
                        v-loading="pending"
                        element-loading-text="加载中..."
                        element-loading-background="rgba(0, 0, 0, 0)"
                    >
                        <div class="flex items-center" style="margin-top: 30px">
                            <div
                                class="w-[200px] h-[200px] flex items-center justify-center flex-none relative"
                                @click="selectMusic(musicDetail)"
                            >
                                <!--      音乐封面      -->
                                <el-image
                                    v-if="musicDetail?.image_url"
                                    :src="musicDetail?.image_url"
                                    class="w-full h-full rounded-[15px]"
                                />
                                <div v-else class="text-tx-secondary">
                                    <Icon name="local-icon-music1" :size="45" />
                                </div>
                                <div
                                    v-if="
                                        currentId == musicDetail.id && playing
                                    "
                                    class="absolute inset-0 flex items-center justify-center text-white"
                                >
                                    <Icon name="local-icon-pause1" :size="20" />
                                </div>
                                <div
                                    v-if="
                                        currentId == musicDetail.id && !playing
                                    "
                                    class="absolute inset-0 flex items-center justify-center text-white"
                                >
                                    <Icon name="local-icon-play" :size="20" />
                                </div>
                            </div>
                            <div class="ml-[15px] py-[8px]">
                                <div class="text-xl font-medium">
                                    {{ musicDetail.title }}
                                </div>
                                <div
                                    class="mt-[20px] text-base text-tx-placeholder"
                                >
                                    {{ musicDetail.style_desc }}
                                </div>
                                <div
                                    class="flex items-center"
                                    style="margin-top: 30px"
                                >
                                    <div class="flex items-center w-[150px]">
                                        <Icon
                                            class="cursor-pointer p-1"
                                            name="el-icon-Timer"
                                            size="30"
                                            color="#556477"
                                        />
                                        <span class="ml-1 text-tx-primary">{{
                                            musicDetail.duration
                                        }}</span>
                                    </div>

                                    <el-tooltip
                                        effect="dark"
                                        content="收藏 / 取消收藏"
                                        placement="bottom"
                                    >
                                        <div
                                            class="image-praise relative dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                            @click.stop="handlePraise(musicDetail)"
                                        >
                                            <div
                                                class="praise-animate"
                                                :class="
                                                    musicDetail.is_collect
                                                        ? 'praise-entry'
                                                        : 'praise-leave'
                                                "
                                            />
                                        </div>
                                    </el-tooltip>
                                    <el-tooltip
                                        effect="dark"
                                        content="下载音乐"
                                        placement="bottom"
                                    >
                                        <div>
                                            <Icon
                                                class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                                name="el-icon-Download"
                                                size="24"
                                                color="#556477"
                                            />
                                        </div>
                                    </el-tooltip>
                                </div>

                                <div class="mt-[20px]">
                                    <div
                                        class="flex items-center"
                                        v-if="musicDetail?.user_info?.image"
                                    >
                                        <ElAvatar
                                            :size="40"
                                            :src="musicDetail?.user_info?.image"
                                        />
                                        <p
                                            class="text-[#BBBBBB] ml-[6px] w-[200px] truncate"
                                        >
                                            {{ musicDetail.user_info.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <el-scrollbar style="margin-top: 30px">
                            <div class="whitespace-pre">
                                {{ musicDetail.lyric }}
                            </div>
                        </el-scrollbar>
                    </div>
                </div>
                <!--    Right Panel    -->
                <div class="flex flex-col h-full min-h-0 w-[600px] p-[16px]">
                    <div class="text-xl font-medium">热门推荐</div>

                    <el-scrollbar style="margin-top: 20px">
                        <div
                            v-for="item in recommendList"
                            class="flex bg-page rounded-[15px] cursor-pointer hover:bg-[#EEF2FF] mb-[20px] p-[20px]"
                            :id="`music-item-${item.id}`"
                            @click="selectRecommendMusic(item)"
                        >
                            <div
                                class="w-[100px] h-[100px] flex items-center justify-center flex-none relative"
                            >
                                <!--      音乐封面      -->
                                <el-image
                                    v-if="item.image_url"
                                    :src="item.image_url"
                                    class="w-full h-full rounded-[15px]"
                                />
                                <div v-else class="text-tx-secondary">
                                    <Icon name="local-icon-music1" :size="45" />
                                </div>
                                <div
                                    v-if="currentId == item.id && playing"
                                    class="absolute inset-0 flex items-center justify-center text-white"
                                >
                                    <Icon name="local-icon-pause1" :size="20" />
                                </div>
                                <div
                                    v-if="currentId == item.id && !playing"
                                    class="absolute inset-0 flex items-center justify-center text-white"
                                >
                                    <Icon name="local-icon-play" :size="20" />
                                </div>
                            </div>
                            <!--      音乐信息      -->
                            <div class="flex-1 ml-[20px]">
                                <div
                                    class="text-[16px] font-bold"
                                    :class="{
                                        '!text-primary': currentId === item.id
                                    }"
                                >
                                    {{ item.title }}
                                </div>
                                <div
                                    v-if="item.style_name"
                                    class="mt-[12px] text-tx-secondary"
                                >
                                    {{ item.style_name }}
                                </div>
                                <div class="flex justify-between mt-[12px]">
                                    <!--      用户信息      -->

                                    <!--      音乐时长      -->
                                    <div class="mt-[4px] text-tx-secondary">
                                        {{ item.duration }}
                                    </div>

                                    <!--      曹作兰      -->
                                    <div class="flex items-center">
                                        <el-tooltip
                                            effect="dark"
                                            content="收藏 / 取消收藏"
                                            placement="bottom"
                                        >
                                            <div
                                                class="image-praise relative dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                                @click.stop="handlePraise(item)"
                                            >
                                                <div
                                                    class="praise-animate"
                                                    :class="
                                                        item.is_collect
                                                            ? 'praise-entry'
                                                            : 'praise-leave'
                                                    "
                                                />
                                            </div>
                                        </el-tooltip>
                                        <el-tooltip
                                            effect="dark"
                                            content="下载音乐"
                                            placement="bottom"
                                        >
                                            <div
                                                @click.stop="
                                                    downloadMusic(
                                                        item.audio_url,
                                                        item.title
                                                    )
                                                "
                                            >
                                                <Icon
                                                    class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                                    name="el-icon-Download"
                                                    size="24"
                                                    color="#556477"
                                                />
                                            </div>
                                        </el-tooltip>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </el-scrollbar>
                </div>
            </div>

            <div class="px-[16px] pb-[16px]">
                <MusicPlayer ref="musicPlayerRef" class="bg-page rounded-lg" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { getMusicRecommendList } from '~/api/music'
import { getMusicDetail } from '~/api/square'
import { musicSquareCollect } from '~/api/square'
import { useMusicPlay } from '~/components/music/useMusicPlay'
import { useUserStore } from '~/stores/user'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const selectDetailId = ref<number>(0)

const { playing, currentId, setCurrentId, togglePlay, setMusic } =
    useMusicPlay()

const {
    data: musicDetail,
    pending,
    refresh: refreshMusicDetail
} = await useAsyncData(
    () =>
        getMusicDetail({
            id:
                selectDetailId.value ||
                (route.query.id as number | unknown as number)
        }),
    {
        default() {
            return {
                audio_url: '',
                create_time: '',
                duration: '',
                image_large_url: '',
                image_url: '',
                is_collect: 0,
                id: -1,
                lyric: '',
                prompt: '',
                style_desc: '',
                tags: '',
                title: '',
                video_url: ''
            }
        },
        transform: (data: any) => {
            return data
        },
        lazy: true,
        immediate: false
    }
)
const { data: recommendList, refresh: refreshRecommendList } =
    await useAsyncData(() => getMusicRecommendList(), {
        default() {
            return []
        },
        lazy: true,
        immediate: false
    })

/**
 * 选择推荐的音乐
 * **/
const selectRecommendMusic = async (item: any) => {
    if (item.square_id === currentId.value) return
    selectDetailId.value = item.square_id
    await refreshMusicDetail()
    await refreshRecommendList()
    const filter = recommendList.value.filter(
        (item: any) => item.square_id === selectDetailId.value
    )
    setMusic(recommendList.value)
    selectMusic(filter[0])
    console.log(musicDetail.value)
    router.replace({
        path: '',
        query: {
            id: musicDetail.value.id
        }
    })
}

/**
 * 播放/选择音乐
 * **/
const selectMusic = (item: any) => {
    if (item.square_id == currentId.value) {
        togglePlay()
        console.log('播放')
        return
    }
    setCurrentId(item.id)
}

/**
 * 收藏 / 取消收藏
 * **/
const handlePraise = async (val: any) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin(true)
        return
    }
    await musicSquareCollect({
        records_id: val?.square_id || route.query.id,
        status: val.is_collect ? 0 : 1
    })
    val.is_collect = val.is_collect ? 0 : 1
}

/**
 * 下载音乐
 * **/
const downloadMusic = async (url: string, name: string) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin(true)
        return
    }
    try {
        const res = await $request.get(
            { url, responseType: 'blob', baseURL: '' },
            { isReturnDefaultResponse: true, apiPrefix: '' }
        )
        console.log('下载音乐', res)
        const blob = new Blob([res._data], {
            type: res.headers.get('Content-Type')
        })
        const link = window.URL.createObjectURL(blob)
        download(link, name)
    } catch (error) {
        feedback.msgError('文件下载失败')
    }
}

/**
 * 初始化
 * **/
onMounted(async () => {
    await refreshMusicDetail()
    await refreshRecommendList()
    const filter = recommendList.value.filter(
        (item: any) => item.square_id === route.query.id
    )
    if (filter.length === 0) {
        recommendList.value.unshift(musicDetail.value)
        setMusic(recommendList.value)
    } else {
        setMusic(recommendList.value)
    }
    selectMusic(musicDetail.value)
})

definePageMeta({ hiddenFooter: true })
</script>

<style lang="scss" scoped>
.image-praise {
    position: relative;
    width: 24px;
    height: 24px;
    margin-right: 20px;

    .praise-animate {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 54px;
        height: 54px;
        background: url('~/assets/image/praise02.png') no-repeat;
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
}
</style>