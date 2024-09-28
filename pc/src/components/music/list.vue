<template>
    <div>
        <div v-for="(item, index) in musicList" :key="item.id">
            <div
                class="flex bg-page mb-[20px] p-[20px] rounded-[12px] cursor-pointer"
                @click="selectMusic(item)"
                :id="`music-item-${item.id}`"
            >
                <template v-if="item.status === 1">
                    <div
                        class="flex-1 flex flex-col justify-center items-center min-h-[75px]"
                    >
                        <Icon
                            class="is-loading"
                            name="el-icon-Loading"
                            :size="25"
                        />
                        <div class="mt-4">歌曲生成中，请稍等！</div>
                    </div>
                </template>
                <template v-else>
                    <div class="flex-1 flex items-center">
                        <div
                            class="w-[75px] h-[75px] flex items-center justify-center flex-none relative"
                        >
                            <el-image
                                v-if="item.image_url"
                                :src="item.image_url"
                                class="w-full h-full rounded-[12px]"
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
                        <div class="ml-[20px]" v-if="item.status === 2">
                            <div
                                class="text-[16px] font-bold"
                                :class="{
                                    '!text-primary': currentId === item.id
                                }"
                            >
                                {{ item.title }}
                            </div>
                            <div
                                v-if="item.style_desc"
                                class="mt-[4px] text-tx-secondary"
                            >
                                {{ item.style_desc }}
                            </div>
                            <div class="mt-[4px] text-tx-secondary">
                                {{ item.duration }}
                            </div>
                        </div>
                        <div
                            class="flex-1 flex justify-center"
                            v-if="item.status === 3"
                        >
                            歌曲生成失败，请重试！
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="text-tx-secondary">
                            {{ item.create_time }}
                        </div>
                        <div class="flex items-center mt-[25px]">
                            <el-tooltip
                                v-if="item.audio_url"
                                effect="dark"
                                content="下载"
                                placement="bottom"
                            >
                                <div
                                    class="mr-6"
                                    @click.stop="downloadMusic(item.audio_url, item.title)"
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
                                v-if="item.audio_url && appStore.getSquareConfig.music_award.is_open"
                                effect="dark"
                                content="分享至广场"
                                placement="bottom"
                            >
                                <div
                                    class="cursor-pointer dark:hover:bg-[rgba(255,255,255,0.05)] hover:bg-[rgba(0,0,0,0.05)] rounded-md mr-6 pb-[7px] p-1 box-content"
                                    @click.stop="shareMusic(item.id, item.is_share)"
                                >
                                    <Icon
                                        name="local-icon-share"
                                        size="17"
                                        color="#556477"
                                    />
                                </div>
                            </el-tooltip>
                            <el-tooltip
                                effect="dark"
                                content="删除"
                                placement="bottom"
                            >
                                <div @click.stop="handleDelete(item.id, index)">
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
                </template>
            </div>
        </div>

        <music-share
            v-if="showShare"
            ref="shareRef"
            @close="showShare = false"
            @success="(val) => sharedIds.push(val)"
        ></music-share>
    </div>
</template>
<script setup lang="ts">
import { deleteMusic } from '@/api/music'
import { useMusicPlay } from './useMusicPlay'
import MusicShare from './music-share.vue'
import feedback from "~/utils/feedback";
import VideoShare from "~/pages/video/_components/video-share.vue";
import {downloadImgFile} from "~/utils/download";
import {deleteHandle} from "~/pages/draw/hooks/useDrawEffect";
import { useAppStore } from "~/stores/app";

const appStore = useAppStore()

const props = withDefaults(
    defineProps<{
        musicList: any[]
    }>(),
    {
        musicList: () => []
    }
)

const emit = defineEmits<{
    update: []
}>()
const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])

const { playing, currentId, setCurrentId, togglePlay, currentMusic, getMusic } =
    useMusicPlay()

const downloadMusic = async (url: string, name: string) => {
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

/**
 * 分享到广场
 */
const shareMusic = async (records_id: number, is_share?: number) => {
    try {
        if (sharedIds.value.includes(records_id) || is_share) {
            feedback.msgError('该音乐已经分享过了！')
            return
        }
        showShare.value = true
        await nextTick()
        shareRef.value.open(records_id)
    } catch (error) {
        console.log(error)
    }
}

const handleDelete = async (id: number, index: number) => {
    await feedback.confirm('确定删除？')
    await deleteMusic({
        id
    })
    await getMusic()
    currentId.value = -1
    emit('update')
}

const selectMusic = (item: any) => {
    if (item.id === currentId.value) {
        togglePlay()
        return
    }
    item.status === 2 && setCurrentId(item.id)
}
</script>

<style lang="scss" scoped></style>
