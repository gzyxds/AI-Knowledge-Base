<template>
    <div class="h-full bg-body rounded-[15px] p-[16px] flex flex-col">
        <div class="flex-1 min-h-0 flex flex-col">
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
                        <MusicPlayer ref="musicPlayerRef" class="bg-page" />
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
                    <div class="text-tx-secondary text-sm flex item-center">
                        快去创建你的作品吧！
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useUserStore } from '~/stores/user'
import { getMusicLists } from '@/api/music'
import { shallowRef } from 'vue'
import { useMusicPlay } from '@/components/music/useMusicPlay'

const scrollBarRef = shallowRef()

const { getMusic, currentId, pause } = useMusicPlay()

const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 15,
    loading: false,
    lists: [] as any[]
})

const getLists = async () => {
    try {
        const data = await getMusicLists({
            status: 2,
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
</script>

<style lang="scss" scoped></style>
