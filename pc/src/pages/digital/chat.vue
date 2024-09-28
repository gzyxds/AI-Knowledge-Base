<template>
    <div
        v-show="chatStatus === ChatStatus.INITIALING"
        class="h-screen w-screen flex justify-center items-center"
    >
        <img class="w-[400px]" src="@/assets/image/loading.gif" alt="" />
    </div>
    <div v-show="chatStatus !== ChatStatus.INITIALING">
        <div
            ref="containRef"
            class="h-screen w-screen relative overflow-hidden"
            :style="{
                background: robotInfo.digital_bg
            }"
            v-if="robotInfo.digital_id && !robotInfo.digital?.is_disable"
        >
            <canvas
                ref="canvasRef"
                id="digital-canvas"
                :width="width * 2"
                :height="cHeight * 2"
            ></canvas>
            <div class="p-[20px] flex h-full relative z-10">
                <div class="flex-1 h-full flex flex-col">
                    <div class="flex-1 min-h-0">
                        <div class="flex items-center cursor-pointer">
                            <div
                                class="flex bg-white p-[5px] text-bold rounded-[50%] text-primary shadow-light"
                                @click="back"
                            >
                                <Icon name="el-icon-Back" :size="18" />
                            </div>
                            <div
                                class="text-xl flex-1 min-w-0 ml-[10px] text-white"
                            >
                                {{ robotInfo.name }}
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center"></div>
                </div>
                <div class="h-full flex">
                    <div
                        class="h-full flex flex-col items-center w-[160px] justify-end"
                    >
                        <div
                            class="recorder gradient-button"
                            :class="{
                                'recorder--stop': !isRecording && !audioPlaying
                            }"
                            @click="changeRecorder"
                        >
                            <canvas
                                v-if="hasVoice"
                                :style="{
                                    width: `${canvasOptions.width}px`,
                                    height: `${canvasOptions.height}px`
                                }"
                                :width="canvasOptions.width * canvasOptions.scale!"
                                :height="canvasOptions.height * canvasOptions.scale!"
                                :id="canvasOptions.id"
                            />
                            <Icon
                                v-if="isRecording && !hasVoice"
                                name="el-icon-Microphone"
                                :size="40"
                            />
                            <Icon
                                v-else-if="audioPlaying"
                                name="local-icon-pause"
                                :size="40"
                            />
                            <Icon
                                v-else-if="!isRecording"
                                name="el-icon-Mute"
                                :size="40"
                            />
                        </div>
                        <div
                            class="text-xs text-white bg-[rgba(51,51,51,0.3)] py-[5px] px-[10px] rounded my-[10px]"
                        >
                            <div>
                                {{ statusToTextMap[chatStatus] }}
                            </div>
                        </div>
                    </div>
                    <div
                        class="w-[400px] h-full flex flex-col mr-[20px] pt-[100px]"
                    >
                        <div
                            class="flex-1 min-h-0 bg-[rgba(0,0,0,0.5)] rounded-[20px] overflow-hidden flex flex-col"
                        >
                            <div class="flex-1 min-h-0">
                                <ElScrollbar
                                    ref="scrollbarRef"
                                    v-if="chatList.length"
                                >
                                    <div class="py-4 px-[20px]">
                                        <div ref="innerRef">
                                            <div
                                                v-for="(
                                                    item, index
                                                ) in chatList"
                                                :key="item.id + '' + index"
                                                class="mt-4 sm:pb-[20px]"
                                            >
                                                <TheChatMsgItem
                                                    v-if="item.type == 1"
                                                    type="right"
                                                    :avatar="
                                                        userStore.userInfo
                                                            .avatar
                                                    "
                                                    color="white"
                                                >
                                                    <TheChatMsgContent
                                                        :content="
                                                            String(item.content)
                                                        "
                                                    />
                                                </TheChatMsgItem>
                                                <TheChatMsgItem
                                                    v-if="item.type == 2"
                                                    type="left"
                                                    :time="item.create_time"
                                                    :avatar="
                                                        robotInfo.icons
                                                            ? robotInfo.icons
                                                            : robotInfo.image
                                                    "
                                                    bg="#fff"
                                                >
                                                    <TheChatMsgContent
                                                        :content="
                                                            String(item.content)
                                                        "
                                                        type="html"
                                                        :typing="item.typing"
                                                        :images="item.images"
                                                        :files="item.files"
                                                        :record-id="item.id"
                                                        :record-type="2"
                                                    />
                                                </TheChatMsgItem>
                                            </div>
                                        </div>
                                    </div>
                                </ElScrollbar>
                                <div
                                    v-else
                                    class="h-full flex justify-center text-tx-secondary items-center"
                                >
                                    暂无聊天记录
                                </div>
                            </div>
                            <div
                                class="flex justify-center items-center py-[10px]"
                                v-if="isReceiving"
                            >
                                <ElButton
                                    color="#fff"
                                    round
                                    @click="sseInstance?.abort()"
                                >
                                    停止
                                </ElButton>
                            </div>
                        </div>
                        <div>
                            <TheChatAction
                                ref="chatActionRef"
                                :loading="
                                    [
                                        ChatStatus.THINKING,
                                        ChatStatus.PLAYING
                                    ].includes(chatStatus)
                                "
                                :menus="robotInfo.menus"
                                :show-pause="false"
                                :show-clear="false"
                                @enter="chat"
                                @focus="inputFocus"
                            >
                            </TheChatAction>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <div class="gradient-button" @click="clearChatRecord">
                            <Icon name="local-icon-clear" :size="24" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-else
            class="h-screen w-screen flex flex-col items-center justify-center"
        >
            <el-empty
                description="该智能体暂未配置形象或形象已被禁用"
                :image="data_null"
            />
            <ElButton type="primary" round @click="back"> 返回智能体 </ElButton>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { useElementSize, watchThrottled, useWindowSize } from '@vueuse/core'
import {
    clearRobotChatRecord,
    getRobotChatRecord,
    getRobotDetail,
    robotChat,
    voiceGenerate,
    voiceTransfer
} from '@/api/robot'
import { useUserStore } from '@/stores/user'
import { Sse } from '@/utils/http/sse'
import { useAppStore } from '@/stores/app'
import { ElScrollbar } from 'element-plus'
import data_null from '@/assets/image/create_record_null.png'
const videoRef = shallowRef<HTMLVideoElement>()
const router = useRouter()
const userStore = useUserStore()
const route = useRoute()
const robotId = computed(() => route.query.id)
const squareId = computed(() => route.query.squareId)
const cateId = computed(() => route.query.cateId)
const appStore = useAppStore()
const robotInfo = ref<any>({})
const getRobotInfo = async () => {
    robotInfo.value = await getRobotDetail({
        id: robotId.value
    })
}
const chatList = ref<any[]>([])
let lastPlayId = 0
const getChatList = async () => {
    const data = await getRobotChatRecord({
        square_id: squareId.value,
        category_id: cateId.value,
        robot_id: robotId.value,
        page_size: 25000
    })
    chatList.value = data.lists || []
    if (chatStatus.value == ChatStatus.THINKING) {
        const last = chatList.value[chatList.value.length - 1]
        if (last && last.id !== lastPlayId) {
            lastPlayId = last.id
            playRecord(lastPlayId)
        }
    }
}

await useAsyncData(() => getChatList(), { lazy: true })

const back = () => {
    if (squareId.value) {
        router.replace({
            path: '/robot_square'
        })
    } else {
        router.replace({
            path: '/application/layout/robot'
        })
    }
}

enum ChatStatus {
    INITIALING,
    //默认
    DEFAULT,
    //录音中
    RECORDING,
    // 正在思考中
    THINKING,
    PLAYING
}
const chatStatus = ref(ChatStatus.INITIALING)
const statusToTextMap = reactive({
    [ChatStatus.INITIALING]: '正在初始化对话...',
    [ChatStatus.DEFAULT]: '点击开始说话',
    [ChatStatus.RECORDING]: '我在听，您请说...',
    [ChatStatus.THINKING]: '稍等，让我想一想',
    [ChatStatus.PLAYING]: '正在回复中...'
})
const canvasRef = shallowRef<HTMLCanvasElement>()
const videoSrc = computed(() => {
    if (chatStatus.value == ChatStatus.PLAYING) {
        return robotInfo.value.digital.wide_talk_video
    } else {
        return robotInfo.value.digital.wide_stay_video
    }
})

const clearChatRecord = async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (isReceiving.value) return
    await feedback.confirm('确定清空记录？')
    await clearRobotChatRecord({
        square_id: squareId.value,
        category_id: cateId.value,
        robot_id: robotId.value
    })
    getChatList()
}
const chatActionRef = shallowRef()
const inputFocus = () => {
    if (!userStore.isLogin) {
        chatActionRef.value?.blur()
        return userStore.toggleShowLogin()
    }
    scrollToBottom()
}
let sseInstance: Sse | null = null
const isReceiving = ref(false)
const chat = async (value: string, type = 'input') => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (!value) return feedback.msgError('请输入问题')
    if (isReceiving.value) return
    if (!robotId.value) return
    stop()

    changeChatStatus(ChatStatus.THINKING)
    const key = Date.now()
    isReceiving.value = true
    chatList.value.push({
        type: 1,
        content: value
    })
    chatList.value.push({
        type: 2,
        typing: true,
        content: '',
        key
    })

    chatActionRef.value?.setInputValue()
    const currentChat = chatList.value.find((item: any) => item.key === key)

    sseInstance = robotChat({
        square_id: squareId.value,
        cate_id: cateId.value,
        robot_id: robotId.value,
        question: value,
        stream: true
    })

    sseInstance.addEventListener('chat', ({ data: dataJson }: any) => {
        const { data, index } = dataJson
        if (!currentChat.content) {
            currentChat.content = ''
        }
        currentChat.content += data
    })

    sseInstance.addEventListener('file', ({ data: dataJson }: any) => {
        try {
            const urls = JSON.parse(dataJson.data)
            currentChat.files = urls
        } catch (error) {
            console.error(error)
        }
    })
    sseInstance.addEventListener('image', ({ data: dataJson }: any) => {
        try {
            const urls = JSON.parse(dataJson.data)
            currentChat.images = urls
        } catch (error) {
            console.error(error)
        }
    })
    sseInstance.addEventListener('close', async () => {
        setTimeout(async () => {
            await getChatList()
            currentChat.typing = false
            isReceiving.value = false
            scrollToBottom()
        }, 500)
    })
    sseInstance.addEventListener('error', async (ev) => {
        changeChatStatus(ChatStatus.DEFAULT)
        if (ev.data?.code === 1100) {
            try {
                if (!appStore.getIsShowRecharge) {
                    feedback.msgError(
                        `${appStore.getTokenUnit}数量已用完。请联系客服增加`
                    )
                } else {
                    await feedback.confirm(
                        `${appStore.getTokenUnit}数量已用完，请前往充值`
                    )
                    router.push('/user/recharge')
                }
            } finally {
                type === 'input' && chatActionRef.value?.setInputValue(value)
            }
            return
        }
        if (ev.errorType === 'connectError') {
            feedback.msgError('请求失败，请重试')
        }
        if (['connectError', 'responseError'].includes(ev.errorType!)) {
            chatList.value.splice(chatList.value.length - 2, 2)
            type === 'input' && chatActionRef.value?.setInputValue(value)
        }
        currentChat.typing = false
        setTimeout(() => {
            isReceiving.value = false
        }, 200)
    })
}
const scrollbarRef = shallowRef<InstanceType<typeof ElScrollbar>>()
const innerRef = ref<HTMLDivElement>()
const scrollToBottom = async () => {
    const scrollHeight = scrollbarRef.value?.wrapRef?.scrollHeight!
    scrollbarRef.value?.setScrollTop(scrollHeight)
}
const { height } = useElementSize(innerRef)
watchThrottled(
    height,
    () => {
        isReceiving.value && scrollToBottom()
    },
    { throttle: 500, immediate: true }
)
const idleReplyTimer = ref()
const isAutoOpen = ref(true)

const changeChatStatus = (status: any) => {
    chatStatus.value = status
}

const isAutoStop = ref(false)
const startTimer = ref(0)
const hasVoice = ref(false)
const recordTimer = ref<any>(0)
const canvasOptions = reactive<AudioGraphUserOptions>({
    id: 'audio-canvas',
    width: 80,
    height: 40,
    minHeight: 5,
    scale: 2
})
const { render, stopRender, draw } = useRenderAudioGraph(canvasOptions)
const { start, stop, isRecording, authorize, close, isOpen } = useRecorder({
    onstart() {
        changeChatStatus(ChatStatus.RECORDING)
        clearTimeout(idleReplyTimer.value)
        hasVoice.value = false
        startTimer.value = Date.now()
    },
    async onstop(result) {
        stopRender()
        hasVoice.value = false
        if (!isAutoStop.value) {
            // chatStatus.value = ChatStatus.DEFAULT
            changeChatStatus(ChatStatus.DEFAULT)
            return
        }
        isAutoStop.value = false
        changeChatStatus(ChatStatus.THINKING)
        try {
            const res: any = await voiceTransfer({
                file: result.blob as any
            })
            if (!res.text) {
                isAutoOpen.value && startRecord()
                return
            }
            chat(res.text, 'voice')
        } catch (error) {
            isAutoOpen.value && startRecord()
        }
    },
    ondata(result) {
        const now = Date.now()
        if (hasVoice.value) {
            render(result)
        }
        // 过滤掉杂音
        if (result.powerLevel >= 10) {
            clearTimeout(recordTimer.value)
            chatStatus.value = ChatStatus.RECORDING
            hasVoice.value = true

            startTimer.value = now
            //有音频输入，延时1s停止录音
            recordTimer.value = setTimeout(() => {
                isAutoStop.value = true
                clearTimeout(idleReplyTimer.value)
                pause()
                stop()
            }, 2000)
        }
        // 5s内没有语音输入
        if (
            now - startTimer.value >=
            robotInfo.value.digital?.idle_time * 1000
        ) {
            if (!hasVoice.value) {
                playIdleReply()
                stop()
            }
        }
    }
})

const { play, pause, audioPlaying } = useAudioPlay({
    onstart() {
        chatStatus.value = ChatStatus.PLAYING
        if (isPlayIdleReply.value) {
            isPlayIdleReply.value = false
        }
    },
    onstop() {
        changeChatStatus(ChatStatus.RECORDING)
        if (!isAutoOpen.value) {
            changeChatStatus(ChatStatus.DEFAULT)
        } else {
            startRecord()
        }
    },
    onerror() {
        changeChatStatus(ChatStatus.DEFAULT)
    }
})

const playRecord = async (id: number) => {
    const fun = async () => {
        return await getFile({
            type: 2,
            record_id: id
        })
    }
    play(fun, false)
}

const startRecord = async () => {
    if (isRecording.value) {
        return
    }
    start()
    return
}

const changeRecorder = async () => {
    if (chatStatus.value == ChatStatus.PLAYING) {
        pause()
        startRecord()
        return
    }
    if (chatStatus.value == ChatStatus.THINKING) {
        return
    }
    if (isRecording.value) {
        isAutoOpen.value = false
        stop()
        changeChatStatus(ChatStatus.DEFAULT)
    } else {
        isAutoOpen.value = true
        startRecord()
    }
}

const getFile = async (params: any) => {
    try {
        const { url } = await voiceGenerate(params)
        return url
    } catch (error) {
        changeChatStatus(ChatStatus.DEFAULT)
        return Promise.reject()
    }
}
const file = ref('')
const isPlayIdleReply = ref(false)
const playIdleReply = async () => {
    if (
        !robotInfo.value.is_digital ||
        !robotInfo.value.digital_id ||
        robotInfo.value.digital.is_disable
    ) {
        return Promise.reject()
    }
    if (!file.value) {
        file.value = await getFile({
            type: 3,
            record_id: robotInfo.value.id
        })
    }
    if (!file.value) return Promise.reject()
    isPlayIdleReply.value = true
    const key = Date.now()
    chatList.value.push({
        type: 2,
        typing: false,
        content: robotInfo.value.digital.idle_reply,
        key
    })
    await nextTick()
    scrollToBottom()
    play(file.value, false)
}
const removeSse = () => {
    sseInstance?.removeEventListener('close')
    sseInstance?.removeEventListener('chat')
    sseInstance?.removeEventListener('error')
    sseInstance?.abort()
    isReceiving.value = false
}
onUnmounted(() => {
    removeSse()
    pause()
    stop()
})
const containRef = shallowRef()
const { width, height: cHeight } = useWindowSize()
const stayVideo = shallowRef<HTMLVideoElement>()
const talkVideo = shallowRef<HTMLVideoElement>()
const loadVideo = async (src: string): Promise<HTMLVideoElement> => {
    return new Promise((resolve, reject) => {
        const video = document.createElement('video')
        video.src = src
        video.preload = 'auto'
        video.loop = true
        video.muted = true
        video.addEventListener('loadedmetadata', (e) => {
            video.width = video.videoWidth
            video.height = video.videoHeight
            resolve(video)
        })
        video.addEventListener('error', (e) => {
            reject(e)
        })
        video.addEventListener('play', (e) => {
            playVideo()
        })
    })
}
const playVideo = () => {
    if (!canvasRef.value) return
    const w = width.value * 2
    const h = cHeight.value * 2
    const ctx = canvasRef.value.getContext('2d')
    if (!ctx) return
    const video =
        chatStatus.value === ChatStatus.PLAYING
            ? talkVideo.value
            : stayVideo.value

    if (!video) return
    ctx.clearRect(0, 0, w, h)
    const { videoHeight, videoWidth } = video
    let cx = 0
    let cy = 0
    let cw = videoWidth
    let ch = videoHeight
    if (videoWidth / videoHeight >= w / h) {
        const sW = (w * videoHeight) / h
        cx = (videoWidth - sW) / 2
        cw = sW
    } else {
        const sH = (h * videoWidth) / w
        cy = (videoHeight - sH) / 2
        ch = sH
    }
    ctx.drawImage(video, cx, cy, cw, ch, 0, 0, w, h)
    requestAnimationFrame(playVideo)
}

onMounted(async () => {
    await getRobotInfo()
    if (!robotInfo.value.digital_id || robotInfo.value.digital.is_disable) {
        return
    }
    isAutoOpen.value = true
    stayVideo.value = await loadVideo(robotInfo.value.digital.wide_stay_video)
    talkVideo.value = await loadVideo(robotInfo.value.digital.wide_talk_video)
    stayVideo.value.play()
    talkVideo.value.play()
    try {
        await authorize()
        startRecord()
    } catch (error) {
        changeChatStatus(ChatStatus.DEFAULT)
    }
    await nextTick()
    scrollToBottom()
})

definePageMeta({
    layout: 'blank'
})
</script>

<style lang="scss" scoped>
#digital-video,
#digital-canvas {
    width: 100%;
    height: 100%;
    position: absolute;
    object-fit: cover;
}
#digital-video {
    transform: scale(0);
}
.gradient-button {
    width: 48px;
    height: 48px;
    opacity: 1;
    background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
    box-shadow: 0px 3.11px 7.78px 0px rgba(0, 0, 0, 0.2);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    @apply text-white;
}

.recorder {
    width: 90px;
    height: 90px;
    &--stop {
        background: #b7b7b7;
        border: 1px solid #ffffff;
    }
}
</style>
