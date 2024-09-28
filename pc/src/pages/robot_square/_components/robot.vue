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
                style="width: auto; margin-right: 12px;"
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
                class="model-lists mb-[10px] mx-[0px] "
                ref="robotRef"
                v-infinite-scroll="load"
                infinite-scroll-distance="50"
            >
                <template v-if="pageInfo.lists.length">
                    <Waterfall
                        ref="waterFull"
                        :delay="100"
                        :list="pageInfo.lists"
                        :width="315"
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
                                class="card-item cursor-pointer  bg-white dark:bg-[#1d2025]"
                                @click="openRobot(item)"
                            >
                                <div class="flex items-center">
                                    <ElAvatar class="flex-none" :src="item.image" :size="64"/>
                                    <div class="flex-1 min-w-0 ml-[15px]">
                                        <div class="line-clamp-1 text-xl font-medium">{{ item.name }}</div>
                                        <div class="line-clamp-1 text-tx-secondary text-xs mt-[5px]">{{ item.author }}</div>
                                    </div>
                                </div>
                                <div class="mt-[13px] text-tx-secondary line-clamp-2 h-[40px] text-sm">{{ item.intro }}</div>
                                <div class="mt-[30px] entry-btn">开始对话</div>
                            </div>
                            <!--                        <el-skeleton class="flex flex-wrap" :loading="pageInfo.loading" animated v-if="pageInfo.lists.length">-->
                            <!--                            <template #template>-->
                            <!--                                <div-->
                            <!--                                    class="card-item cursor-pointer w-[315px] h-[236px] bg-white dark:bg-[#1d2025]"-->
                            <!--                                    v-for="item in pageInfo.lists.length" :key="item"-->
                            <!--                                >-->
                            <!--                                    <div class="flex items-center">-->
                            <!--                                        <el-skeleton-item variant="circle" style="width: 64px; height: 64px"/>-->
                            <!--                                        <div class="flex flex-col flex-1 min-w-0 ml-[15px]">-->
                            <!--                                            <el-skeleton-item variant="h3" style="width: 50%"/>-->
                            <!--                                            <el-skeleton-item variant="text" style="margin-top: 5px; width: 80%"/>-->
                            <!--                                        </div>-->
                            <!--                                    </div>-->
                            <!--                                    <el-skeleton-item variant="text" style="margin-top: 13px; height: 40px"/>-->
                            <!--                                    <el-skeleton-item variant="button"-->
                            <!--                                                      style="margin-top: 30px; width: 100%; height: 35px; border-radius: 30px;"/>-->
                            <!--                                </div>-->
                            <!--                            </template>-->
                            <!--                            <template #default>-->
                            <!--                                -->
                            <!--                            </template>-->
                            <!--                        </el-skeleton>-->
                        </template>
                    </Waterfall>
                </template>

                <div
                    v-if="pageInfo.loading"
                    class="flex justify-center items-center mt-[50px]"
                >
                    <el-icon size="25" class="is-loading"><Loading/></el-icon>
                    <span class="mt-[4px] ml-[10px] text-[#999999]">加载中...</span>
                </div>

                <div class="flex flex-col justify-center items-center w-full h-[60vh]"
                     v-show="!pageInfo.lists.length && !pageInfo.loading">
                    <el-image class="w-[200px] h-[200px]" :src="EmptyLayer"/>
                    <div class="text-tx-regular mb-4">暂无机器人</div>
                    <ElButton type="primary" @click="resetPage"> 点击刷新</ElButton>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import 'swiper/css'
import { Swiper as SwiperInstance } from 'swiper'
import {Loading} from '@element-plus/icons-vue'

import { getRobotCategory, getRobotSquare } from '@/api/robot'
import { putRobotRecord } from '~/api/robot'
import { useUserStore } from '~/stores/user'
import { watchDebounced } from '@vueuse/core'
import EmptyLayer from "assets/image/empty_con.png";

const props = defineProps<{ keyword: string }>()

const router = useRouter()
const userStore = useUserStore()
const robotRef = shallowRef()
const queryParams = reactive<{
    keyword: string,
    cid: number
}>({
    keyword: '',
    cid: 0
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
    pageNo: number,
    count: number,
    loading: boolean,
    pageSize: number,
    lists: any[]
}>({
    pageNo: 1,
    count: 0,
    loading: true,
    pageSize: 15,
    lists: [] as any[]
})

const currentIndex = ref<number>(0)
// 分类列表
const { data: categoryList } = await useAsyncData(
    () => getRobotCategory(),
    {
        default() {
            return []
        },
        transform(data: any) {
            return [
                {
                    id: 0,
                    name: '全部'
                }
            ].concat(data)
        },
        lazy: true
    }
)
// 默认全部机器人列表
await useAsyncData(() => getLists(), { lazy: true })

const getLists = async () => {
    pageInfo.loading = true
    try {
        const data = await getRobotSquare({
            ...queryParams,
            page_no: pageInfo.pageNo,
            page_size: pageInfo.pageSize
        })
        if (pageInfo.pageNo === 1) {
            pageInfo.lists = []
        }
        pageInfo.count = data.count
        pageInfo.lists.push(...data.lists)
    } finally {
        setTimeout(() => pageInfo.loading = false, 200)
    }
}


const load = () => {
    if (!userStore.isLogin) return
    if (pageInfo.count >= pageInfo.pageNo * pageInfo.pageSize) {
        pageInfo.pageNo++
        getLists()
    }
}

const resetPage = () => {
    pageInfo.pageNo = 1
    getLists()
}

const swiperInstance = shallowRef<SwiperInstance>()
const onSwiper = (swiper: SwiperInstance) => {
    swiperInstance.value = swiper
    console.log(swiper)
}

const selectCategory = (index: any) => {
    currentIndex.value = index
    queryParams.cid = categoryList.value[index]?.id
    resetPage()
}

selectCategory(0)

const openRobot = async (item: any) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin()
        return
    }
    const { id } = await putRobotRecord({
        id: item.id
    })
    router.push({
        path: '/robot_square/chat',
        query: {
            id
        }
    })
}

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
        color: #4B4A58;
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

.card-item {
    //width: 315px;
    //height: 236px;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid rgba(67, 111, 246, 0.3);
    //margin: 10px;

    .entry-btn {
        box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.2);
        padding: 7px 20px;
        border-radius: 12px;
        text-align: center;
        @apply text-white bg-primary;
    }
}
</style>