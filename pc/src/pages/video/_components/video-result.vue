<template>
    <div
        class="bg-body h-full flex-1 rounded-[12px] p-4 flex flex-col gap-4 relative"
    >
        <div class="sticky top-0">
            <div
                class="border-b border-b-[#eff0f2] dark:border-[#333333] pb-4 text-2xl font-medium"
            >
                生成记录
            </div>
            <div class="mt-4" style="--el-border-radius-base: 12px">
                <el-segmented
                    class="task-type !bg-[transparent]"
                    v-model="taskStatus"
                    :options="taskStatusOptions"
                    @change="taskStatusChange"
                />
            </div>
        </div>
        <el-scrollbar
            class="video-result flex-1"
            ref="scrollBarRef"
            v-loading="pageInfo.loading"
        >
            <div
                v-if="pageInfo.lists.length > 0"
                v-infinite-scroll="load"
                infinite-scroll-distance="50"
            >
                <div
                    class="grid grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4"
                >
                    <div
                        v-for="(item, index) in pageInfo.lists"
                        :key="item.id"
                        class="rounded-[12px] p-4 flex flex-col gap-2 border border-[#eff0f2] dark:border-[#333333]"
                    >
                        <div class="flex justify-between">
                            <el-tag
                                :type="statusMap[item.status].type"
                                effect="light"
                            >
                                {{ statusMap[item.status].label }}
                            </el-tag>
                            <div
                                v-if="item.status !== 1 || item.status === 0"
                                class="flex items-center"
                            >
                                <el-tooltip
                                    v-if="item.status === 2"
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
                                <template v-if="item.status === 2">
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
                                        v-if="appStore.getSquareConfig.video_award.is_open"
                                        effect="dark"
                                        content="分享至广场"
                                        placement="bottom"
                                    >
                                        <div
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md pb-[7px] p-1 box-content"
                                            @click="shareVideo(item.id, item.is_share)"
                                        >
                                            <Icon
                                                name="local-icon-share"
                                                size="17"
                                                color="#556477"
                                            />
                                        </div>
                                    </el-tooltip>
                                </template>
                                <el-tooltip
                                    effect="dark"
                                    content="重新生成"
                                    placement="bottom"
                                >
                                    <div @click="regenerate(item)">
                                        <Icon
                                            class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md p-1 box-content"
                                            name="el-icon-RefreshRight"
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

                        <div class="relative flex-1">
                            <div
                                class="bg-[var(--el-bg-color-page)] rounded-[12px] overflow-hidden"
                            >
                                <aspect-ratio
                                    v-if="item.status === 2"
                                    :src="item.video_url"
                                    type="video"
                                    :ratio="[4, 3]"
                                />
                            </div>
                            <div
                                v-if="item.status === 3"
                                class="w-full pb-[75%]"
                            >
                                <div
                                    class="w-full h-full px-4 flex flex-col justify-center items-center absolute left-0 top-0"
                                >
                                    <img
                                        class="w-1/2 mb-4"
                                        :src="drawError"
                                        alt="生成视频失败"
                                    />
                                    <div class="my-[10px]">生成视频失败</div>
                                    <div
                                        class="text-xs text-[#798696] dark:text-white line-clamp-3 w-full break-all text-center"
                                    >
                                        错误信息：{{ item.fail_reason }}
                                    </div>
                                </div>
                            </div>
                            <div
                                class="video-loading w-full pb-[75%]"
                                v-if="item.status === 0 || item.status === 1"
                                v-loading="true"
                                element-loading-text="正在生成中..."
                            ></div>
                        </div>
                        <el-popover
                            placement="bottom"
                            title="提示词"
                            :show-arrow="false"
                            transition="custom-popover"
                            width="300px"
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
                            <el-tag>{{ item.type_desc }}</el-tag>
                        </div>
                    </div>
                </div>
            </div>
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
                    <template #title>
                        <div class="text-xl">当前任务是空的哦</div>
                    </template>
                    <template #sub-title>
                        <div class="text-info">
                            在左侧输入描述，创建你的作品吧!
                        </div>
                    </template>
                </el-result>
            </div>
        </el-scrollbar>

        <video-share
            v-if="showShare"
            ref="shareRef"
            @close="showShare = false"
            @success="(val) => sharedIds.push(val)"
        ></video-share>
    </div>
</template>

<script lang="ts" setup>
import DrawingEmpty from '@/assets/image/video_empty.png'
import { copy } from '@/utils/util'

import drawError from '@/assets/image/draw/error.png'
import { ElScrollbar } from 'element-plus'
import { deleteVideo, getVideoLists } from '@/api/video'
import feedback from '@/utils/feedback'
import VideoShare from './video-share.vue'
import { useAppStore } from "~/stores/app";
const appStore = useAppStore()

const emit = defineEmits(['regenerate'])
const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 10,
    loading: true,
    lists: [] as any[]
})

const statusMap: Record<
    number,
    {
        label: string
        type: 'success' | 'warning' | 'danger'
    }
> = {
    1: {
        label: '生成中',
        type: 'warning'
    },
    3: {
        label: '生成失败',
        type: 'danger'
    },
    2: {
        label: '生成成功',
        type: 'success'
    }
}
const taskStatus = ref<number>(-1)
const taskStatusOptions = [
    {
        label: '全部',
        value: -1
    },
    {
        label: '生成中',
        value: 1
    },
    {
        label: '生成成功',
        value: 2
    },
    {
        label: '生成失败',
        value: 3
    }
]
const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])

const taskStatusChange = async (e: number) => {
    taskStatus.value = e
    reload()
}
const timer = shallowRef()

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
}

const deleteHandle = async (id: number) => {
    await feedback.confirm('确定删除？')
    await deleteVideo({ id })
    ElMessage.success('删除成功')
    resetPage()
}

const shareVideo = async (records_id: number, is_share?: number) => {
    if (sharedIds.value.includes(records_id) || is_share) {
        feedback.msgError('该视频已分享过了！')
        return
    }
    showShare.value = true
    await nextTick()
    shareRef.value.open(records_id)
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
            status: taskStatus.value,
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

const load = () => {
    if (pageInfo.count >= pageInfo.pageNo * pageInfo.pageSize) {
        pageInfo.pageNo++
        getLists()
    }
}

const regenerate = (item: any) => {
    const data = {
        type: item.type,
        prompt: item.prompt,
        scale: item.scale,
        image: item.image,
        style_id: item.style_id
    }
    emit('regenerate', data)
}
const resetPage = async () => {
    pageInfo.pageSize = pageInfo.pageNo * pageInfo.pageSize
    pageInfo.pageNo = 1
    await getLists()
}

const scrollBarRef = shallowRef()

const reload = async () => {
    pageInfo.loading = true
    pageInfo.pageSize = 10
    pageInfo.pageNo = 1
    await getLists()
    scrollBarRef.value?.setScrollTop(0)
}

reload()

onUnmounted(() => {
    clearTimeout(timer.value)
})

defineExpose({
    refresh: async () => {
        taskStatus.value = -1
        await resetPage()
        scrollBarRef.value!.setScrollTop(0)
    }
})
</script>

<style lang="scss" scoped>
.task-type {
    :deep(.el-segmented__group) {
        gap: 10px;
        .el-segmented__item {
            padding: 0px 20px;
            height: 35px;
            @apply shadow-[0px_2px_6px_0px_#ebeefd] dark:shadow-[0px_2px_6px_0px_rgba(0,0,0,0.2)];
        }
    }
}
.video-result {
    :deep(.el-loading-mask) {
        border-radius: 12px;
        z-index: 2 !important;
    }
    :deep(.el-scrollbar__view) {
        height: 100%;
    }
}
.video-loading {
    :deep(.el-loading-mask) {
        background-color: var(--el-bg-color-page) !important;
    }
}
</style>
