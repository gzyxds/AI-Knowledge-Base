<template>
    <div class="h-full bg-body rounded-[12px] p-[16px] flex flex-col">
        <div
            class="text-2xl font-bold border-b border-solid border-br-light pb-[16px]"
        >
            生成记录
        </div>
        <div class="flex-1 min-h-0 flex flex-col">
            <div class="category-lists">
                <div v-for="(item, index) in categoryList" :key="item.type">
                    <div
                        class="category-item bg-white"
                        :class="{
                            'is-active': currentType === item.type
                        }"
                        @click="changeType(item.type)"
                    >
                        {{ item.name }}
                    </div>
                </div>
            </div>
            <div class="flex-1 min-h-0" v-loading="pageInfo.loading">
                <div class="h-full flex flex-col" v-if="pageInfo.lists.length">
                    <div class="flex-1 min-h-0 flex">
                        <div class="flex-1 min-w-0 h-full">
                            <ElScrollbar ref="scrollBarRef">
                                <div
                                    v-infinite-scroll="load"
                                    infinite-scroll-distance="50"
                                >
                                    <MusicList
                                        :music-list="pageInfo.lists"
                                        @update="resetPage"
                                    />
                                </div>
                            </ElScrollbar>
                        </div>
                        <MusicDisplay />
                    </div>
                    <div class="mt-[16px]">
                        <MusicPlayer ref="musicPlayerRef" class="bg-page rounded-[12px]" />
                    </div>
                </div>

                <div
                    v-else-if="!pageInfo.loading"
                    class="h-full flex flex-col items-center justify-center"
                >
                    <div class="text-tx-secondary">
                        <Icon :size="45" name="local-icon-music1" />
                    </div>

                    <div class="my-[10px]">当前还没有音乐哦</div>
                    <div class="text-tx-secondary text-sm">
                        在左侧输入描述，创建你的作品吧！
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '~/stores/user'
import { deleteMusic, getMusicLists, getMusicDetail } from '@/api/music'
import { shallowRef } from 'vue'
import { useMusicPlay } from '@/components/music/useMusicPlay'
const userStore = useUserStore()
const scrollBarRef = shallowRef()
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

const { getMusic, currentId, pause } = useMusicPlay()
const currentType = ref(-1)
const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 15,
    loading: true,
    lists: [] as any[]
})

const timer = shallowRef()
const currentGeneratingNum = ref(0)
const checkHasGenerating = async () => {
    clearTimeout(timer.value)
    const ids = pageInfo.lists
        .filter((item) => item.status === 1)
        .map((item) => item.id)
    if (ids.length > 0) {
        timer.value = setTimeout(() => {
            resetPage()
        }, 6000)
    }
    if (ids.length !== currentGeneratingNum.value) {
        getMusic()
    }
    currentGeneratingNum.value = ids.length
}

const getLists = async () => {
    try {
        const data = await getMusicLists({
            status: currentType.value,
            page_no: pageInfo.pageNo,
            page_size: pageInfo.pageSize
        })
        pageInfo.count = data.count
        if (pageInfo.pageNo === 1) {
            pageInfo.lists = []
        }
        pageInfo.lists.push(...data.lists)
    } catch (error) {
    } finally {
        pageInfo.loading = false
        checkHasGenerating()
    }
}

getMusic()

const load = () => {
    if (pageInfo.count >= pageInfo.pageNo * pageInfo.pageSize) {
        pageInfo.pageNo++
        getLists()
    }
}

const resetPage = async () => {
    pageInfo.pageSize = pageInfo.pageNo * pageInfo.pageSize
    pageInfo.pageNo = 1
    await getLists()
}

const reload = async () => {
    pageInfo.loading = true
    pageInfo.pageSize = 15
    pageInfo.pageNo = 1
    await getLists()
}

const setSelectFirst = () => {
    const item = pageInfo.lists.find((item) => {
        return item.status === 2
    })
    if (item) {
        currentId.value = item.id
    }
}

const changeType = async (type: number) => {
    pause()
    currentType.value = type
    currentId.value = -1
    await reload()
    setSelectFirst()
}

defineExpose({
    refresh: async () => {
        currentType.value = -1
        await resetPage()
        scrollBarRef.value!.setScrollTop(0)
    }
})

onMounted(async () => {
    await reload()
    setSelectFirst()
})

watch(currentId, (value) => {
    if (scrollBarRef.value) {
        const item = document.getElementById(`music-item-${value}`)
        if (!item) return
        const itemRect = item?.getBoundingClientRect()
        const scrollRect = scrollBarRef.value.wrapRef.getBoundingClientRect()
        if (itemRect.top < scrollRect.top) {
            scrollBarRef.value!.setScrollTop(item?.offsetTop)
        }
        if (itemRect.bottom > scrollRect.bottom) {
            scrollBarRef.value!.setScrollTop(
                item?.offsetTop - scrollRect.height + itemRect.height
            )
        }
    }
})

onUnmounted(() => {
    clearTimeout(timer.value)
})
</script>

<style lang="scss" scoped>
.category-lists {
    display: flex;
    padding: 16px 0;
    margin: 0 -6px;
    .category-item {
        line-height: 35px;
        border-radius: 12px;

        text-align: center;
        height: 35px;
        padding: 0px 24px;
        font-size: 14px;
        margin: 0 6px;
        cursor: pointer;
        @apply line-clamp-1 text-tx-primary  shadow-light bg-body dark:bg-[#333];

        &.is-active {
            @apply text-white bg-primary;
        }
    }
}
</style>
