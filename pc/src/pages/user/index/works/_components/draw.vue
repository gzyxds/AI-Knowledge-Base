<template>
    <div class="square h-full flex flex-col">
        <div class="nav flex px-4 pt-4">
            <div class="category-list flex-1">
                <div
                    v-for="item in cateLists"
                    :key="item.type"
                    class="category-item"
                    :class="{
                        'category-item--active': item.type === queryParams.model
                    }"
                    @click="changeCategory(item.type)"
                >
                    {{ item.name }}
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <div
                class="h-full flex flex-col justify-center"
                v-if="!pager.lists.length && !pager.loading"
            >
                <el-empty
                    :image-size="150"
                    :image="DrawingEmpty"
                    description="暂时没有绘画哦，快去生成试试吧"
                />
            </div>
            <ElScrollbar v-else>
                <div class="main">
                    <div
                        v-infinite-scroll="getDrawData"
                        :infinite-scroll-delay="200"
                        :infinite-scroll-distance="400"
                        :infinite-scroll-disabled="!pager.more"
                    >
                        <template v-if="pager.lists.length">
                            <Waterfall
                                ref="waterFull"
                                :delay="100"
                                :list="pager.lists"
                                :width="326"
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
                                                :image="
                                                    item?.image ||
                                                    item?.image_url
                                                "
                                                @refresh="loadImageSuccess"
                                                @on-click="showPreview(item)"
                                            ></image-cover>
                                        </div>

                                        <div
                                            class="image-del"
                                            @click="handleDelete(item.id)"
                                        >
                                            <Icon
                                                class="cursor-pointer rounded-md p-1 box-content"
                                                name="el-icon-Delete"
                                                size="18"
                                                color="#fff"
                                            />
                                        </div>
                                    </div>
                                </template>
                            </Waterfall>
                            <div
                                v-if="pager.loading"
                                class="flex justify-center items-center mt-[50px]"
                            >
                                <el-icon size="25" class="is-loading"
                                    ><Loading
                                /></el-icon>
                                <span
                                    class="mt-[4px] ml-[10px] text-tx-secondary"
                                    >加载中...</span
                                >
                            </div>
                        </template>
                    </div>
                </div>
            </ElScrollbar>
        </div>
        <ImagePreview
            v-model:show="previewState.show"
            :data="previewState.data"
        />
        <!-- <el-image-viewer
            v-if="previewLists.length"
            :url-list="previewLists"
            :hide-on-click-modal="true"
            @close="previewLists = []"
        /> -->
    </div>
</template>
<script setup lang="ts">
import Waterfall from '~/components/waterfall/index.vue'
import { drawingRecord } from '@/api/draw'
import { Loading, Search } from '@element-plus/icons-vue'
import { useCopy } from '~/composables/useCopy'
import { useAppStore } from '~/stores/app'
import ImageCover from '~/components/image-cover/index.vue'
import ImagePreview from './image-preview.vue'
import { useUserStore } from '~/stores/user'
import DrawingEmpty from '@/assets/image/draw/empty-image.png'
import { deleteHandle } from '@/pages/draw/hooks/useDrawEffect'
const { copy } = useCopy()
const appStore = useAppStore()
const userStore = useUserStore()

definePageMeta({
    auth: false,
    layout: false
})

interface QueryParamsType {
    status: number
    page_no: number
    page_size: number
    model: string
}

//弹框ref
const popRef = shallowRef()

const waterFull = shallowRef<any>(null)
const queryParams = reactive<QueryParamsType>({
    status: 3,
    page_no: 0,
    page_size: 20,
    model: 'sd'
})
const pager = reactive({
    more: true,
    loading: false,
    lists: [] as any[]
})
const cateLists = ref<any[]>([
    { name: 'SD绘画', type: 'sd' },
    { name: 'DALLE绘画', type: 'dalle3' },
    { name: 'MJ绘画', type: 'mj_goapi' }
])
const previewLists = ref<any>([])
const previewState = reactive({
    show: false,
    data: {} as any
})
const showPreview = (item: any) => {
    previewState.show = true
    previewState.data = item
}
const breakpoints = {
    4000: { rowPerView: 8 },
    2000: { rowPerView: 6 },
    1800: { rowPerView: 6 },
    1600: { rowPerView: 5 },
    1440: { rowPerView: 5 },
    1360: { rowPerView: 5 },
    1280: { rowPerView: 4 },
    1024: { rowPerView: 4 }
}

const loadImageSuccess = () => {
    waterFull?.value?.renderer()
    // waterFullItem.height = '100%'
}

const resetParams = () => {
    pager.more = true
    queryParams.page_no = 0
}

const handleDelete = async (id: number) => {
    await deleteHandle(id)
    resetParams()
}

const changeCategory = (type: string) => {
    if (queryParams.model === type) return
    queryParams.model = type
    resetParams()
    getDrawData()
}

const getDrawData = async () => {
    if (pager.loading) return
    if (pager.more) {
        queryParams.page_no += 1
    } else {
        return
    }
    pager.loading = true
    try {
        const data = await drawingRecord(queryParams)
        const { lists, page_no, page_size, count } = data
        if (page_no * page_size > count) {
            pager.more = false
        }
        if (page_no == 1) {
            pager.lists = lists
        } else {
            pager.lists = [...pager.lists, ...lists]
        }
        setTimeout(() => (pager.loading = false), 500)
    } catch (error) {
        pager.loading = false
        console.log('获取绘画广场列表错误=>', error)
    }
}

// const onPraise = async (val: any) => {
//     if (!userStore.isLogin) {
//         userStore.toggleShowLogin(true)
//         return
//     }
//     try {
//         await drawPraise({
//             id: val.id,
//             praise: val.is_praise ? 0 : 1
//         })
//         // 分类ID是0说明是当前在喜欢页
//         if (queryParams.category_id === 0) {
//             resetParams()
//             getDrawData()
//         } else {
//             val.is_praise = val.is_praise ? 0 : 1
//         }
//     } catch (e) {
//         console.error(e)
//     }
// }

getDrawData()
</script>
<style lang="scss" scoped>
.nav {
    .category-list {
        .category-item {
            display: inline-block !important;
            line-height: 30px;
            border-radius: 6px;
            box-shadow: 0 2px 6px #ebeefd;
            text-align: center;
            height: 30px;
            padding: 0 18px;
            margin-right: 10px;
            cursor: pointer;
            background-color: white;
            @apply line-clamp-1 text-tx-primary;
            &--active {
                box-shadow: 0 3px 6px;
                @apply bg-primary text-white shadow-light;
            }
        }
    }
}

.main {
    .image-payload:hover {
        .image-bg {
            //transform: scale(1.1);
        }
        .image-del {
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
        position: relative;
        cursor: pointer;
        overflow: hidden;
        border-radius: 12px;
        .image-bg {
            user-select: none;
            //pointer-events: none;
            //transition: all 1s;
        }
        .image-del {
            transition: all 0.5s;
            opacity: 0;
            position: absolute;
            top: 15px;
            right: 15px;
            width: 30px;
            height: 30px;
            padding: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
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
    }
}
</style>
