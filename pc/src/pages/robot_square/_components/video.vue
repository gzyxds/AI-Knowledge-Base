<template>
    <div class="flex-1 min-h-0 mx-[16px] relative">
        <swiper
            :slidesPerView="'auto'"
            :spaceBetween="16"
            class="category-lists"
            style="padding: 10px 0"
            @swiper="onSwiper"
        >
            <swiper-slide
                v-for="(item, index) in categoryList"
                :key="item.id"
                style="width: auto; margin-right: 12px"
            >
                <div
                    v-if="Object.keys(item).includes('name')"
                    class="category-item bg-white"
                    :class="{
                        'is-active': currentIndex === index
                    }"
                    @click="selectCategory(index)"
                >
                    {{ item.name }}
                </div>
            </swiper-slide>
        </swiper>
        <div class="flex-1 min-h-[70vh] overflow-hidden mx-auto">
            <div
                class="model-lists mb-[10px] mx-[0px]"
                v-infinite-scroll="getLists"
                infinite-scroll-distance="50"
                :infinite-scroll-delay="200"
                :infinite-scroll-distance="500"
                :infinite-scroll-disabled="!pageInfo.more"
            >
                <template v-if="pageInfo.lists.length">
                    <div
                        class="grid grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 4xl:grid-cols-6 8xl:grid-cols-7 gap-4"
                    >
                        <div
                            v-for="(item, index) in pageInfo.lists"
                            :key="item.id"
                            class="flex flex-col min-h-0 bg-body rounded-[12px] cursor-pointer"
                        >
                            <!--      视频      -->
                            <div class="relative flex-1">
                                <div
                                    class="bg-[var(--el-bg-color-page)] rounded-t-[12px] overflow-hidden"
                                >
                                    <aspect-ratio
                                        :src="item.video_url"
                                        type="video"
                                        :ratio="[4, 3]"
                                    />
                                </div>
                            </div>
                            <!--      生成场景描述      -->
                            <div class="w-full h-full p-[15px] box-border">
                                <div class="line-clamp-2">
                                    {{ item.prompt }}
                                </div>
                            </div>
                            <div
                                class="flex justify-between px-[15px] pb-[20px]"
                            >
                                <!--      用户信息      -->
                                <div
                                    class="flex items-center"
                                    v-if="item?.user_info?.image"
                                >
                                    <ElAvatar
                                        :size="28"
                                        :src="item?.user_info?.image"
                                    />
                                    <p
                                        class="text-[#BBBBBB] ml-[6px] w-[100px] truncate"
                                    >
                                        {{ item.user_info.name }}
                                    </p>
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
                                        content="下载视频"
                                        placement="bottom"
                                    >
                                        <div
                                            @click.stop="
                                                downloadVideo(
                                                    item.video_url,
                                                    '视频'
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
                </template>

                <div
                    v-if="pageInfo.loading"
                    class="flex justify-center items-center mt-[50px]"
                >
                    <el-icon size="25" class="is-loading">
                        <Loading />
                    </el-icon>
                    <span class="mt-[4px] ml-[10px] text-[#999999]"
                        >加载中...</span
                    >
                </div>

                <div
                    class="flex flex-col justify-center items-center w-full h-[60vh]"
                    v-show="!pageInfo.lists.length && !pageInfo.loading"
                >
                    <el-image class="w-[200px] h-[200px]" :src="EmptyLayer" />
                    <div class="text-tx-regular mb-4">当前选择暂无视频～</div>
                    <ElButton type="primary" @click="resetPage">
                        点击刷新
                    </ElButton>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import 'swiper/css'
import { Swiper as SwiperInstance } from 'swiper'
import { Loading } from '@element-plus/icons-vue'

import {
    videoSquareCollect,
    getVideoSquare,
    type SquareQuery
} from '@/api/square'
import { getSquareCategory } from '~/api/task_reward'
import { useUserStore } from '~/stores/user'
import { watchDebounced } from '@vueuse/core'
import { ShareSquareEnum } from '~/enums/appEnums'

import EmptyLayer from 'assets/image/empty_con.png'
import feedback from '~/utils/feedback'

const props = defineProps<{ keyword: string }>()

const router = useRouter()
const userStore = useUserStore()

const queryParams = reactive<SquareQuery>({
    page_no: 0,
    page_size: 20,
    keyword: '',
    category_id: ''
})

const pageInfo = reactive<{
    more: boolean
    count: number
    loading: boolean
    lists: any[]
}>({
    more: true,
    count: 0,
    loading: false,
    lists: [] as any[]
})

const currentIndex = ref<number>(0)
// 分类列表
const { data: categoryList } = await useAsyncData(
    () =>
        getSquareCategory({
            type: ShareSquareEnum.VIDEO
        }),
    {
        default() {
            return []
        },
        transform(data: any) {
            return [
                {
                    id: '',
                    name: '全部'
                }
            ].concat(data)
        },
        lazy: true
    }
)
// 默认全部视频分类列表
await useAsyncData(() => getLists(), { lazy: true })

// 获取视频列表
const getLists = async () => {
    if (pageInfo.loading) return
    if (pageInfo.more) {
        queryParams.page_no += 1
    } else {
        return
    }
    pageInfo.loading = true
    try {
        const data = await getVideoSquare(queryParams)
        const { lists, page_no, page_size, count } = data
        if (page_no * page_size > count) {
            pageInfo.more = false
        }
        if (page_no == 1) {
            pageInfo.lists = lists
        } else {
            pageInfo.lists = [...pageInfo.lists, ...lists]
        }
    } finally {
        setTimeout(() => (pageInfo.loading = false), 200)
    }
}

const resetPage = () => {
    queryParams.page_no = 0
    pageInfo.more = true
    getLists()
}

/**
 * 收藏 / 取消收藏
 * **/
const handlePraise = async (val: any) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin(true)
        return
    }
    await videoSquareCollect({
        records_id: val.id,
        status: val.is_collect ? 0 : 1
    })
    // 分类ID是0说明是当前在喜欢页
    if (queryParams.category_id === 0) {
        resetPage()
    } else {
        val.is_collect = val.is_collect ? 0 : 1
    }
}

/**
 * 下载视频
 * **/
const downloadVideo = async (url: string, name: string) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin(true)
        return
    }
    try {
        const res = await $request.get(
            { url, responseType: 'blob', baseURL: '' },
            { isReturnDefaultResponse: true, apiPrefix: '' }
        )
        console.log(res)
        const blob = new Blob([res._data], {
            type: res.headers.get('Content-Type')
        })
        const link = window.URL.createObjectURL(blob)
        download(link, name)
    } catch (error) {
        feedback.msgError('文件下载失败')
    }
}

const swiperInstance = shallowRef<SwiperInstance>()
const onSwiper = (swiper: SwiperInstance) => {
    swiperInstance.value = swiper
    console.log(swiper)
}

const selectCategory = (index: any) => {
    currentIndex.value = index
    queryParams.category_id = categoryList.value[index]?.id
    resetPage()
}

selectCategory(0)

watchDebounced(
    () => props.keyword,
    (value) => {
        queryParams.keyword = value
        resetPage()
    },
    {
        debounce: 500
    }
)
</script>

<style lang="scss" scoped>
.category-lists {
    .category-item {
        display: flex;
        align-items: center;
        justify-content: center;

        white-space: nowrap;
        cursor: pointer;
        padding: 4px 12px;
        box-shadow: 0 2px 4px 0 #1e1e1f0a;
        border-radius: 12px;
        height: 32px;
        font-size: 12px;
        font-weight: 600;
        color: #4b4a58;
        background-color: #fff;
        @apply dark:bg-[#333] dark:text-white;

        &.is-active {
            @apply text-white bg-primary;
        }

        &.is-active:hover {
            @apply text-white bg-primary;
        }
    }

    .category-item:hover {
        @apply dark:text-[#4B4A58];
        background-color: #e6e6e9;
    }
}

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