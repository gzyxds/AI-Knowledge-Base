<template>
    <div
        class="h-full bg-body rounded-[15px] p-[16px] flex flex-col"
        v-loading="pageInfo.loading"
    >
        <el-scrollbar
            v-if="pageInfo.lists.length > 0"
            class="video-result flex-1"
            ref="scrollBarRef"
        >
            <div v-infinite-scroll="load" infinite-scroll-distance="50">
                <div
                    class="grid grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-4"
                >
                    <div
                        v-for="(item, index) in pageInfo.lists"
                        :key="item.id"
                        class="rounded-xl p-4 flex flex-col gap-2 border border-[#eff0f2] dark:border-[#333333]"
                    >
                        <div class="flex justify-between">
                            <el-tag>{{ item.type_desc }}</el-tag>
                            <div class="flex items-center">
                                <el-tooltip
                                    effect="dark"
                                    content="复制提示词"
                                    placement="bottom"
                                >
                                    <div @click="copy(item.prompt)">
                                        <Icon
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                            name="el-icon-CopyDocument"
                                            size="18"
                                            color="#556477"
                                        />
                                    </div>
                                </el-tooltip>
                                <el-tooltip
                                    effect="dark"
                                    content="下载视频"
                                    placement="bottom"
                                >
                                    <div
                                        @click="
                                            downloadFile(item.video_url, '视频')
                                        "
                                    >
                                        <Icon
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                            name="el-icon-Download"
                                            size="18"
                                            color="#556477"
                                        />
                                    </div>
                                </el-tooltip>

                                <el-tooltip
                                    effect="dark"
                                    content="删除"
                                    placement="bottom"
                                >
                                    <div @click="deleteHandle(item.id)">
                                        <Icon
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                            name="el-icon-Delete"
                                            size="18"
                                            color="#556477"
                                        />
                                    </div>
                                </el-tooltip>
                            </div>
                        </div>

                        <div
                            class="relative rounded-[12px] overflow-hidden flex-1"
                        >
                            <div class="bg-[var(--el-bg-color-page)]">
                                <aspect-ratio
                                    :src="item.video_url"
                                    type="video"
                                    :ratio="[4, 3]"
                                />
                            </div>
                        </div>
                        <el-popover
                            placement="bottom"
                            title="提示词"
                            width="300px"
                            :show-arrow="false"
                            transition="custom-popover"
                            trigger="hover"
                            :content="item.prompt"
                        >
                            <template #reference>
                                <div class="w-full box-border">
                                    <div class="line-clamp-1">
                                        {{ item.prompt }}
                                    </div>
                                </div>
                            </template>
                        </el-popover>

                        <div class="flex justify-between items-center">
                            <span class="text-[#8794A3]">
                                {{ item.create_time }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </el-scrollbar>
        <div
            v-else-if="!pageInfo.loading"
            class="h-full flex items-center justify-center"
        >
            <el-result>
                <template #icon>
                    <el-image
                        class="w-[150px] dark:opacity-60"
                        :src="DrawingEmpty"
                    />
                </template>

                <template #sub-title>
                    <div class="text-info">暂时没有视频哦，快去生成试试吧</div>
                </template>
            </el-result>
        </div>
    </div>
</template>

<script lang="ts" setup>
import DrawingEmpty from '@/assets/image/video_empty.png'
import { copy } from '@/utils/util'
import { ElScrollbar } from 'element-plus'
import { deleteVideo, getVideoLists } from '@/api/video'
import feedback from '@/utils/feedback'

const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 15,
    loading: true,
    lists: [] as any[]
})

const deleteHandle = async (id: number) => {
    await feedback.confirm('确定删除？')
    await deleteVideo({ id })
    ElMessage.success('删除成功')
    resetPage()
}
const downloadFile = async (url: string, name: string) => {
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

const getLists = async () => {
    try {
        const data = await getVideoLists({
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

const load = () => {
    if (pageInfo.count >= pageInfo.pageNo * pageInfo.pageSize) {
        pageInfo.pageNo++
        getLists()
    }
}

const resetPage = async () => {
    scrollBarRef.value!.setScrollTop(0)
    pageInfo.pageSize = pageInfo.pageNo * pageInfo.pageSize
    pageInfo.pageNo = 1
    await getLists()
}

const scrollBarRef = shallowRef()

const reload = async () => {
    pageInfo.loading = true
    pageInfo.pageSize = 15
    pageInfo.pageNo = 1
    await getLists()
    scrollBarRef.value?.setScrollTop(0)
}

reload()
</script>
