<template>
    <div>
        <NuxtLayout name="default">
            <div class="h-full flex flex-col create 4xl:w-[2000px] mx-auto">
                <header
                    class="creation-header flex flex-col justify-center items-center px-[16px] m-[16px] rounded-[12px] overflow-hidden"
                    :style="{
                        'background-image': `url(${appStore.getImageUrl(
                            pages[0]?.prop?.banner_bg
                        )})`
                    }"
                >
                    <div
                        class="font-medium 2xl:text-[50px] xl:text-[40px] lg:text-[36px] text-[36px]"
                        :class="getTitleColor(pages[0]?.prop?.title_color)"
                    >
                        {{ pages[0]?.prop?.title }}
                    </div>

                    <div
                        class="2xl:max-w-[880px] xl:max-w-[780px] lg:max-w-[680px] max-w-[680px] search w-full mt-4"
                    >
                        <el-input
                            size="large"
                            class="2xl:h-[54px] xl:h-[48px] lg:h-[44px] rounded-[7px]"
                            style="--el-border-color: transparent"
                            v-model="searchKeyword"
                            :prefix-icon="Search"
                            placeholder="请输入关键词搜索"
                        >
                        </el-input>
                    </div>
                </header>
                <div class="flex-1 min-h-0 mx-[16px]">
                    <div class="h-full flex flex-col">
                       <swiper
                           :slidesPerView="'auto'"
                           :spaceBetween="16"
                           class="category-lists"
                           @swiper="onSwiper"
                           style="padding: 10px 0; margin-left: 0"
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
                        <div class="flex-1 min-h-0">

                            <Waterfall
                                v-if="creantionList.length"
                                ref="waterFull"
                                :delay="100"
                                :list="creantionList"
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
                                    <NuxtLink
                                        :to="{
                                            path: '/creation/produce',
                                            query: {
                                                cateId: currentCategoryId,
                                                modelId: item.id
                                            }
                                        }"
                                        class="h-full"
                                    >
                                        <el-card
                                            class="!border-none h-full rounded-[12px] relative cardItem shadow-light"
                                            shadow="never"
                                            style="border-radius: 12px"
                                            body-style="padding: 20px;height: 100%"
                                        >
                                            <div
                                                class="flex flex-col min-h-0 h-full"
                                            >
                                                <div class="flex items-center">
                                                    <img
                                                        class="w-[34px] h-[34px] mr-[10px]"
                                                        :src="item.image"
                                                        alt=""
                                                    />
                                                    <div
                                                        class="text-lg font-medium line-clamp-1"
                                                    >
                                                        {{ item.name }}
                                                    </div>
                                                </div>
                                                <div class="h-[36px]">
                                                    <div
                                                        v-if="item.tips"
                                                        class="text-xs text-tx-secondary mt-[10px] line-clamp-2 flex-1"
                                                    >
                                                        {{ item.tips }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex items-center mt-[16px]"
                                                >
                                                    <div
                                                        class="text-tx-secondary mr-[30px] flex items-center text-sm"
                                                    >
                                                        <Icon
                                                            name="local-icon-yonghu"
                                                        />
                                                        <div class="ml-1">
                                                            {{
                                                                item.use_num
                                                            }}人使用过
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex collection absolute top-[10px] right-[10px]"
                                                        @click.prevent="
                                                            toCollection(
                                                                item.id
                                                            )
                                                        "
                                                    >
                                                        <Icon
                                                            v-if="
                                                                !item.is_collect
                                                            "
                                                            :size="20"
                                                            name="el-icon-Star"
                                                            color="#999"
                                                        />
                                                        <Icon
                                                            style="
                                                                transform: scale(
                                                                    1.2
                                                                );
                                                            "
                                                            v-if="
                                                                !!item.is_collect
                                                            "
                                                            :size="20"
                                                            name="el-icon-StarFilled"
                                                            color="#FFB529"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </el-card>
                                    </NuxtLink>
                                </template>
                            </Waterfall>



                            <div
                                class="flex flex-col justify-center items-center w-full h-[60vh]"
                                v-show="
                                    !creantionList.length && !creantionLoading
                                "
                            >
                                <el-image
                                    class="w-[200px] h-[200px]"
                                    :src="EmptyLayer"
                                />
                                <div class="text-tx-regular mb-4">
                                    当前选择暂无创作～
                                </div>
                                <ElButton type="primary" @click="getCreation">
                                    点击刷新
                                </ElButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import { watchDebounced } from '@vueuse/core'
import { collection, getCategoryList, getCreantionList } from '@/api/create'
import { useAppStore } from '@/stores/app'
import { Loading, Search } from '@element-plus/icons-vue'
import { getDecorate } from '~/api/app'
import { Swiper as SwiperInstance } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import EmptyLayer from 'assets/image/empty_con.png'

const appStore = useAppStore()
const currentIndex = ref(0)
const searchKeyword = ref('')
//当前选中分类id
const currentCategoryId = ref(0)
//创作列表
const creantionList = ref<any[]>([])
// 创作加载状态
const creantionLoading = ref(false)

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

const { data: categoryList, refresh } = await useAsyncData(
    () => getCategoryList({ keyword: searchKeyword.value }),
    {
        default() {
            return []
        },
        lazy: true
    }
)

const { data: pages } = await useAsyncData(() => getDecorate({ id: 5 }), {
    transform: (value) => {
        return JSON.parse(value.data)
    },
    default() {
        return []
    },
    lazy: true
})

const getTitleColor = computed(() => {
    return (type: number) => {
        switch (type) {
            case 1:
                return 'text-black'
            case 2:
                return 'text-white'
            case 3:
                return 'text-primary'
        }
    }
})

const swiperInstance = shallowRef<SwiperInstance>()
const onSwiper = (swiper: SwiperInstance) => {
    swiperInstance.value = swiper
    console.log(swiper)
}

const selectCategory = (index: any) => {
    currentIndex.value = index
    currentCategoryId.value = categoryList.value[index]?.id
    getCreation()
}

const getCreation = async () => {
    creantionLoading.value = true
    try {
        creantionList.value = await getCreantionList({
            category_id: currentCategoryId.value,
            keyword: searchKeyword.value
        })
    } finally {
        setTimeout(() => (creantionLoading.value = false), 200)
    }
}

selectCategory(0)

//收藏
const toCollection = async (id: number) => {
    await collection({ id })
    await getCreation()
    refresh()
}

definePageMeta({
    layout: false,
    showLogo: true
})
watchDebounced(
    searchKeyword,
    (value) => {
        // refresh()
        getCreation()
    },
    {
        debounce: 500
    }
)
</script>

<style lang="scss" scoped>
.creation-header {
    height: 300px;
    background-size: 100% 100%;
    background-repeat: no-repeat;

    .search {
        margin-top: 30px;

        :deep(.el-input) {
            .el-input__wrapper {
                padding-left: 20px;
                border-radius: 12px;
            }
        }
    }
}

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

.cardItem {
    .collection {
        transition: opacity 0.1s linear;
        @apply sm:opacity-0;
    }

    &:hover {
        .collection {
            opacity: 1;
        }
    }
}
</style>