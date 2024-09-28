<template>
    <div class="flex-1 min-h-0 mx-[16px] relative">
        <swiper
            :slidesPerView="'auto'"
            :spaceBetween="16"
            class="category-lists"
            @swiper="onSwiper"
            style="padding: 10px 0"
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
                    <Waterfall
                        ref="waterFull"
                        :delay="100"
                        :list="pageInfo.lists"
                        :width="305"
                        :gutter="20"
                        :animationDelay="0"
                        :animationDuration="0"
                        backgroundColor="none"
                        animationPrefix="none"
                        animated="none"
                        animationEffect="none"
                        :breakpoints="breakpoints"
                    >
                        <template #item="{ item }">
                            <div
                                class="image-payload h-full w-full relative text-sm"
                            >
                                <div class="image-bg">
                                    <image-cover
                                        :thumbnail="item.thumbnail"
                                        :image="item?.image || item?.image_url"
                                        @refresh="waterFull?.renderer()"
                                        @on-click="
                                            (val) => (previewLists = val)
                                        "
                                    ></image-cover>
                                </div>
                                <!--  点赞  -->
                                <div
                                    class="image-praise relative"
                                    @click="handlePraise(item)"
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
                                <!--  海报  -->
                                <el-tooltip
                                    effect="dark"
                                    content="生成海报"
                                    placement="top"
                                >
                                    <div
                                        class="image-poster relative"
                                        @click.stop="openPoster(item)"
                                    >
                                        <div class="text-center leading-[38px]">
                                            <Icon
                                                size="16px"
                                                color="#ffffff"
                                                name="el-icon-Picture"
                                            />
                                        </div>
                                    </div>
                                </el-tooltip>
                                <div class="image-content">
                                    <p
                                        class="text-white line-clamp-2"
                                        @click="copy(item.prompt)"
                                    >
                                        {{
                                            item?.prompts_cn ||
                                            item?.original_prompts.prompt
                                        }}
                                    </p>
                                    <div class="flex justify-between mt-[10px]">
                                        <div class="flex items-center">
                                            <template
                                                v-if="item?.user_info?.image"
                                            >
                                                <ElAvatar
                                                    :size="28"
                                                    :src="
                                                        item?.user_info?.image
                                                    "
                                                />
                                                <p
                                                    class="text-[#BBBBBB] ml-[6px] w-[80px] truncate"
                                                >
                                                    {{ item.user_info.name }}
                                                </p>
                                            </template>
                                        </div>
                                        <div
                                            class="flex items-center"
                                            @click="copy(item.prompts)"
                                        >
                                            <Icon name="el-icon-Copy"></Icon>
                                            <p class="text-white ml-[6px]">
                                                复制
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Waterfall>
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
                    <div class="text-tx-regular mb-4">当前选择暂无绘画～</div>
                    <ElButton type="primary" @click="resetPage">
                        点击刷新</ElButton
                    >
                </div>
            </div>

            <poster-pop ref="posterPopupRef"></poster-pop>
            <el-image-viewer
                v-if="previewLists.length"
                :url-list="previewLists"
                :hide-on-click-modal="true"
                @close="previewLists = []"
            />
        </div>
    </div>
</template>
<script setup lang="ts">
import 'swiper/css'
import { Swiper as SwiperInstance } from 'swiper'
import { Loading } from '@element-plus/icons-vue'

import {
    drawSquareCollect,
    getDrawSquare,
    type SquareQuery
} from '@/api/square'
import { getSquareCategory } from '~/api/task_reward'
import { useUserStore } from '~/stores/user'
import { watchDebounced } from '@vueuse/core'
import { ShareSquareEnum } from '~/enums/appEnums'

import EmptyLayer from 'assets/image/empty_con.png'
import ImageCover from '~/components/image-cover/index.vue'
import PosterPop from './posterPop.vue'

const props = defineProps<{ keyword: string }>()

const router = useRouter()
const userStore = useUserStore()
const waterFull = shallowRef<any>(null)
const posterPopupRef = shallowRef<any>(null)

const queryParams = reactive<SquareQuery>({
    page_no: 0,
    page_size: 20,
    keyword: '',
    category_id: ''
})

const breakpoints: Record<number, { rowPerView: number }> = {
    4000: { rowPerView: 7 },
    2000: { rowPerView: 6 },
    1800: { rowPerView: 5 },
    1600: { rowPerView: 5 },
    1440: { rowPerView: 4 },
    1360: { rowPerView: 4 },
    1280: { rowPerView: 4 },
    1024: { rowPerView: 3 }
}

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
// 当前选项卡的索引
const currentIndex = ref<number>(0)
// 图片预览列表
const previewLists = ref<string[]>([])

// 分类列表
const { data: categoryList } = await useAsyncData(
    () =>
        getSquareCategory({
            type: ShareSquareEnum.DRAW
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
// 默认全部绘画分类列表
await useAsyncData(() => getLists(), { lazy: true })

// 获取绘画列表
const getLists = async () => {
    if (pageInfo.loading) return
    if (pageInfo.more) {
        queryParams.page_no += 1
    } else {
        return
    }
    pageInfo.loading = true
    try {
        const data = await getDrawSquare(queryParams)
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
    await drawSquareCollect({
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
 * 打开海报
 * **/
const openPoster = (value: any) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin(true)
        return
    }
    posterPopupRef.value.open(value)
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

.image-payload:hover {
    .image-bg {
        //transform: scale(1.1);
    }

    .image-content {
        opacity: 1;
        bottom: 0;
    }

    .image-praise {
        opacity: 1;
    }

    .image-poster {
        opacity: 1;
    }
}

.image-payload {
    cursor: pointer;
    overflow: hidden;
    border-radius: 12px;

    .image-bg {
        user-select: none;
        //pointer-events: none;
        //transition: all 1s;
    }

    .image-content {
        transition: all 0.5s;
        opacity: 0;
        position: absolute;
        left: 0;
        right: 0;
        bottom: -10px;
        margin: 10px;
        padding: 10px;
        border-radius: 12px;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .image-poster {
        transition: opacity 0.5s;
        background-color: rgba($color: #000000, $alpha: 0.5);
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 32px;
        opacity: 0;
    }

    .image-praise {
        transition: opacity 0.5s;
        background-color: rgba($color: #000000, $alpha: 0.5);
        position: absolute;
        top: 10px;
        left: 10px;
        width: 32px;
        height: 32px;
        border-radius: 32px;
        opacity: 0;

        .praise-animate {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 54px;
            height: 54px;
            background: url('~/assets/image/praise.png') no-repeat;
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
}
</style>