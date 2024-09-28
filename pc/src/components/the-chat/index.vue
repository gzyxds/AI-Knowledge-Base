<template>
    <div class="h-full flex flex-col">
        <div
            class="flex items-center p-4 px-[40px] border-b border-solid border-br-light"
        >
            <img
                class="flex-none w-[40px] h-[40px] rounded-full"
                :src="appInfo.image"
                alt=""
            />
            <div class="ml-[15px]">
                <div class="flex items-center">
                    <div class="text-2xl line-clamp-1">
                        {{ appInfo.name }}
                    </div>
                    <NuxtLink
                        v-if="!squareId"
                        to="/application/layout/robot"
                        class="ml-[10px]"
                    >
                        <ElButton
                            type="info"
                            round
                            text
                            bg
                            style="
                                border: none;
                                --el-button-hover-text-color: var(
                                    --el-color-info
                                );
                            "
                        >
                            切换智能体
                        </ElButton>
                    </NuxtLink>
                </div>

                <div class="text-tx-secondary mt-[4px] line-clamp-2">
                    {{ appInfo.intro }}
                </div>
            </div>
        </div>
        <el-watermark
            class="flex-1 min-h-0"
            :content="appStore.getChatConfig.watermark"
            :font="{
                color: isDark ? 'rgba(256,256,256,0.08)' : 'rgba(0,0,0,0.06)',
                fontSize: 12
            }"
        >
            <div
                ref="containerRef"
                class="h-full flex flex-col rounded relative"
                :style="{
                    background:
                        chatType == ChatTypeEnum.DIGITAL
                            ? appInfo.digital_bg
                            : ''
                }"
            >
                <div
                    class="absolute top-0 left-0 w-full h-full flex flex-col z-10"
                >
                    <div class="flex-1 min-h-0">
                        <ElScrollbar ref="scrollbarRef">
                            <div class="py-4 px-8">
                                <div ref="innerRef">
                                    <TheChatMsgItem
                                        v-if="appInfo.welcome_introducer"
                                        class="mb-[20px]"
                                        type="left"
                                        bg="var(--el-bg-color-page)"
                                        :avatar="
                                            appInfo.icons
                                                ? appInfo.icons
                                                : appInfo.image
                                        "
                                        :class="{
                                            'opacity-70':
                                                chatType ===
                                                ChatTypeEnum.DIGITAL
                                        }"
                                    >
                                        <Markdown
                                            :content="
                                                appInfo.welcome_introducer
                                            "
                                            @click-custom-link="
                                                chat($event, 'link')
                                            "
                                        />
                                    </TheChatMsgItem>
                                    <div
                                        v-for="(item, index) in chatList"
                                        :key="item.id + '' + index"
                                        class="mt-4 sm:pb-[20px]"
                                    >
                                        <TheChatMsgItem
                                            v-if="item.type == 1"
                                            type="right"
                                            :avatar="userStore.userInfo.avatar"
                                            color="white"
                                            :class="{
                                                'opacity-70':
                                                    chatType ===
                                                    ChatTypeEnum.DIGITAL
                                            }"
                                        >
                                            <TheChatMsgContent
                                                :content="String(item.content)"
                                            />
                                            <template #actions>
                                                <div class="my-[5px]">
                                                    <ElButton
                                                        link
                                                        type="info"
                                                        @click="
                                                            copy(item.content)
                                                        "
                                                    >
                                                        <template #icon>
                                                            <Icon
                                                                name="el-icon-CopyDocument"
                                                            />
                                                        </template>
                                                        复制
                                                    </ElButton>
                                                </div>
                                            </template>
                                        </TheChatMsgItem>
                                        <TheChatMsgItem
                                            v-if="item.type == 2"
                                            type="left"
                                            :time="item.create_time"
                                            :avatar="
                                                appInfo.icons
                                                    ? appInfo.icons
                                                    : appInfo.image
                                            "
                                            bg="var(--el-bg-color-page)"
                                            :class="{
                                                'opacity-70':
                                                    chatType ===
                                                    ChatTypeEnum.DIGITAL
                                            }"
                                            :modelName="item.model"
                                        >
                                            <TheChatMsgContent
                                                :content="String(item.content)"
                                                type="html"
                                                :typing="item.typing"
                                                :line-numbers="
                                                    !appStore.isMobile
                                                "
                                                show-copy
                                                :show-context="
                                                    !!appInfo.is_show_context
                                                "
                                                :show-quote="
                                                    !!appInfo.is_show_quote
                                                "
                                                :show-voice="
                                                    appStore.getIsVoiceOpen
                                                "
                                                :context="item.context"
                                                show-poster
                                                :record-list="chatList"
                                                :quotes="item.quotes"
                                                :images="item.images"
                                                :files="item.files"
                                                :videos="item.videos"
                                                :record-id="item.id"
                                                :record-type="2"
                                            />
                                            <template #outer_actions>
                                                <ElButton
                                                    v-if="item.create_time"
                                                    class="ml-[52px] mt-2"
                                                    style="
                                                        --el-button-border-color: transparent;
                                                        --el-color-info-light-8: transparent;
                                                    "
                                                    :type="
                                                        item.is_feedback
                                                            ? 'info'
                                                            : 'primary'
                                                    "
                                                    :plain="true"
                                                    bg
                                                    size="small"
                                                    :disabled="item.is_feedback"
                                                    @click="
                                                        openFeedBackPop(
                                                            item,
                                                            index
                                                        )
                                                    "
                                                >
                                                    {{
                                                        item.is_feedback
                                                            ? '已反馈'
                                                            : '反馈'
                                                    }}
                                                </ElButton>

                                                <div v-if="index === chatList.length - 1 && !isReceiving" class="flex flex-col" style="margin-left: 52px">
                                                    <div
                                                      v-for="(text, textIndex) in (relatedIssuesArr.length ? relatedIssuesArr : item.correlation)"
                                                      :key="textIndex"
                                                      class="inline-flex items-center rounded-[12px] bg-page cursor-pointer mt-[10px] hover:bg-primary-light-9"
                                                      style="padding: 8px 12px; width: fit-content;"
                                                      @click.stop="chat(text, 'input')"
                                                    >
                                                        <span class="mr-2 text-tx-primary">{{ text }}</span>
                                                        <Icon name="el-icon-Right" color="#999" size="20" />
                                                    </div>
                                                </div>
                                            </template>
                                        </TheChatMsgItem>
                                    </div>
                                </div>
                            </div>
                        </ElScrollbar>
                    </div>
                    <div
                        v-show="chatType == ChatTypeEnum.DIGITAL"
                        class="flex flex-col justify-center items-center"
                    >
                        <canvas
                            :style="{
                                width: `${canvasOptions.width}px`,
                                height: `${canvasOptions.height}px`
                            }"
                            :width="canvasOptions.width * canvasOptions.scale!"
                            :height="canvasOptions.height * canvasOptions.scale!"
                            :id="canvasOptions.id"
                        />
                        <div class="text-xs text-white">
                            <div>
                                {{ statusToTextMap[chatStatus] }}
                            </div>
                        </div>
                    </div>
                    <div class="px-[30px]">
                        <TheChatAction
                            ref="chatActionRef"
                            :loading="
                                isReceiving ||
                                chatStatus === ChatStatus.THINKING
                            "
                            :show-manual="!!appInfo.is_artificial"
                            :btn-color="isDark ? '#333' : '#f6f6f6'"
                            @enter="chat"
                            @clear="clearChatRecord"
                            @pause="sseInstance?.abort()"
                            @focus="inputFocus"
                            :menus="appInfo.menus"
                        >
                            <template #btn>
                                <NuxtLink
                                    target="_blank"
                                    :to="{
                                        path: '/digital/chat',
                                        query: {
                                            id: robotId,
                                            squareId: squareId,
                                            cateId: robotStore.sessionId
                                        }
                                    }"
                                    v-if="appInfo.is_digital"
                                    class="flex items-center mr-[10px]"
                                >
                                    <ElButton type="primary" round plain>
                                        形象对话</ElButton
                                    >
                                </NuxtLink>
                            </template>
                        </TheChatAction>
                        <!-- <div
                          v-if="appInfo.copyright"
                          class="pb-[10px] text-center text-tx-regular"
                        >
                          {{ appInfo.copyright }}
                        </div> -->
                    </div>

                    <div
                        v-if="chatType == ChatTypeEnum.DIGITAL"
                        class="recorder"
                        :class="{
                            'recorder--stop': !isRecording
                        }"
                        @click="changeRecorder"
                    >
                        <Icon
                            v-if="isRecording"
                            name="el-icon-Microphone"
                            :size="40"
                        />
                        <Icon v-else name="el-icon-Mute" :size="40" />
                    </div>
                </div>
                <div
                    class="absolute top-0 left-0 w-full h-full flex justify-center items-center"
                    v-show="chatType == ChatTypeEnum.DIGITAL"
                >
                    <video
                        v-show="!audioPlaying"
                        class="h-full w-full object-scale-down"
                        :src="
                            isVertical
                                ? appInfo.digital?.vertical_stay_video
                                : appInfo.digital?.wide_stay_video
                        "
                        autoplay
                        muted
                        loop
                    ></video>
                    <video
                        v-show="audioPlaying"
                        class="h-full w-full object-scale-down"
                        :src="
                            isVertical
                                ? appInfo.digital?.vertical_talk_video
                                : appInfo.digital?.wide_talk_video
                        "
                        autoplay
                        muted
                        loop
                    ></video>
                </div>
            </div>
        </el-watermark>

        <Popup
            ref="popupRef"
            :async="true"
            title="问题反馈"
            :appendToBody="false"
            class="feedback-pop"
        >
            <LTextarea
                v-model="feedbackParams.content"
                rows="8"
                placeholder="描述一下你遇到了什么问题"
            >
            </LTextarea>
            <template #footer>
                <div class="flex justify-center mt-4">
                    <ElButton type="primary" @click="handleFeedBack">
                        提交反馈
                    </ElButton>
                </div>
            </template>
        </Popup>
    </div>
</template>

<script setup lang="ts">
import { useElementSize, watchThrottled, useDark } from '@vueuse/core'
import { ElScrollbar } from 'element-plus'
import { useAppStore } from '~/stores/app'
import type { Sse } from '~/utils/http/sse'
import { useUserStore } from '~/stores/user'
import { useRechargeStore } from '@/stores/recharge'
import {
    clearRobotChatRecord,
    getRobotChatRecord,
    getRobotDetail,
    robotChat,
    voiceGenerate,
    voiceTransfer,
    chatFeedBack
} from '@/api/robot'
import { useRobotStore } from '@/stores/robot'
import { useRenderAudioGraph } from '@/composables/useRecorder'
import Popup from '~/components/popup/index.vue'

const props = defineProps<{
    robotId: number | string
    squareId?: number | string
}>()

enum ChatTypeEnum {
    TEXT = 1,
    DIGITAL = 2
}

const isDark = useDark()
const appStore = useAppStore()
const userStore = useUserStore()
const robotStore = useRobotStore()

const chatType = ref(ChatTypeEnum.TEXT)
const { copy } = useCopy()

// ------------ 处理反馈弹窗
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const feedbackParams = reactive({
    _index: -1,
    robot_id: props.robotId,
    record_id: -1,
    content: ''
})

const openFeedBackPop = (row: { id: number }, index: number) => {
    feedbackParams.record_id = row.id
    feedbackParams._index = index
    popupRef.value?.open()
}

const handleFeedBack = async () => {
    try {
        await chatFeedBack(feedbackParams)
        popupRef.value?.close()
        feedbackParams.content = ''
        chatList.value[feedbackParams._index].is_feedback = 1
    } catch (error) {
        console.log('反馈提交失败-->', error)
    }
}

// ------------ 处理反馈弹窗

const getSession = async () => {
    if (!props.squareId) {
        await robotStore.getSessionLists()
        robotStore.setSessionSelect()
    } else {
        return []
    }
}

await useAsyncData(() => getSession(), {
    lazy: true
})

const chatList = ref<any[]>([])
const router = useRouter()
let lastPlayId = 0
const getChatList = async () => {
    if (!robotStore.sessionId && !props.squareId) return []
    const data = await getRobotChatRecord({
        square_id: props.squareId,
        category_id: robotStore.sessionId,
        robot_id: props.robotId,
        page_size: 25000
    })
    chatList.value = data.lists || []
    if (
        chatType.value === ChatTypeEnum.DIGITAL &&
        chatStatus.value == ChatStatus.THINKING
    ) {
        const last = chatList.value[chatList.value.length - 1]
        if (last && last.id !== lastPlayId) {
            lastPlayId = last.id
            playRecord(lastPlayId)
        }
    }
}

await useAsyncData(() => getChatList(), {
    lazy: true
})
const { data: appInfo, refresh: getDetail } = await useAsyncData(
    () =>
        getRobotDetail({
            id: props.robotId
        }),
    {
        default() {
            return {}
        },
        lazy: true
    }
)

watch(
    () => props.robotId,
    () => {
        getDetail()
    }
)

const clearChatRecord = async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (!robotStore.sessionId && !props.squareId) return
    await feedback.confirm('确定清空记录？')
    await clearRobotChatRecord({
        square_id: props.squareId,
        category_id: robotStore.sessionId,
        robot_id: props.robotId
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
let isSessionAdd = false
const relatedIssuesArr = ref<string[]>([])
const chat = async (value: string, type = 'input') => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (!value) return feedback.msgError('请输入问题')
    if (isReceiving.value) return
    if (!props.robotId) return
    // chatStatus.value =
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
    if (!props.squareId) {
        if (!robotStore.sessionId) {
            isSessionAdd = true
            await robotStore.sessionAdd()
            isSessionAdd = false
        }
        if (robotStore.getCurrentSession.name === '新的会话') {
            await robotStore.sessionEdit({
                id: robotStore.sessionId,
                name: value
            })
        }
    }

    sseInstance = robotChat({
        square_id: props.squareId,
        cate_id: robotStore.sessionId,
        robot_id: props.robotId,
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
    sseInstance.addEventListener('question', ({ data: dataJson }: any) => {
        relatedIssuesArr.value = JSON.parse(dataJson.data)
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
    sseInstance.addEventListener('video', ({ data: dataJson }: any) => {
        try {
            const urls = JSON.parse(dataJson.data)
            currentChat.videos = urls
        } catch (error) {
            console.error(error)
        }
    })

    sseInstance.addEventListener('close', async () => {
        await userStore.getUser()
        setTimeout(async () => {
            await getChatList()
            currentChat.typing = false
            isReceiving.value = false
            await nextTick()
            scrollToBottom()
        }, 500)
    })
    sseInstance.addEventListener('error', async (ev) => {
        isAutoOpen.value && changeChatStatus(ChatStatus.RECORDING)
        type === 'input' && chatActionRef.value?.setInputValue(value)
        if (ev.data?.code === 1100) {
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

            return
        }
        if (ev.errorType === 'connectError') {
            feedback.msgError('请求失败，请重试')
        }
        if (['connectError', 'responseError'].includes(ev.errorType!)) {
            chatList.value.splice(chatList.value.length - 2, 2)
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
const containerRef = shallowRef()
const { height: containerHeight, width: containerWidth } =
    useElementSize(containerRef)
const isVertical = computed(() => {
    if (containerWidth.value / containerHeight.value > 1) {
        return false
    }
    return true
})
const removeSse = () => {
    sseInstance?.removeEventListener('close')
    sseInstance?.removeEventListener('chat')
    sseInstance?.removeEventListener('error')
    sseInstance?.abort()
    isReceiving.value = false
}

watch(
    () => robotStore.sessionId,
    async (value) => {
        if (value && !isSessionAdd) {
            removeSse()
            await getChatList()
            scrollToBottom()
        }
    },
    {
        immediate: true
    }
)

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
const changeChatStatus = (status: any) => {
    if (chatType.value === ChatTypeEnum.DIGITAL) {
        chatStatus.value = status
    }
}

const isAutoStop = ref(false)
const startTimer = ref(0)
const hasVoice = ref(false)
const recordTimer = ref<any>(0)
const idleReplyTimer = ref<any>(0)
const canvasOptions = reactive<AudioGraphUserOptions>({
    id: 'audio-canvas',
    width: 100,
    height: 40,
    minHeight: 5,
    scale: 2
})
const { render, stopRender, draw } = useRenderAudioGraph(canvasOptions)
const { start, stop, isRecording, authorize, close, isOpen } = useRecorder({
    onstart() {
        hasVoice.value = false
        startTimer.value = Date.now()
    },
    async onstop(result) {
        stopRender()
        draw(new Float64Array(new Array(128).fill(0)), 0)

        if (!isAutoStop.value) {
            // chatStatus.value = ChatStatus.DEFAULT
            return
        }
        isAutoStop.value = false
        changeChatStatus(ChatStatus.THINKING)
        try {
            const res: any = await voiceTransfer({
                file: result.blob as any
            })
            if (!res.text) {
                isAutoOpen.value && changeChatStatus(ChatStatus.RECORDING)
                return
            }
            chat(res.text, 'voice')
        } catch (error) {
            isAutoOpen.value && changeChatStatus(ChatStatus.RECORDING)
        }
    },
    ondata(result) {
        render(result)
        const now = Date.now()

        // 过滤掉杂音
        if (result.powerLevel > 15) {
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
        if (now - startTimer.value >= 5000) {
            if (!hasVoice.value) {
            }
        }
    }
})
const createIdleReplyTimer = () => {
    idleReplyTimer.value = setTimeout(() => {
        playIdleReply()
    }, appInfo.value.digital?.idle_time * 1000 || 0)
}
const { play, pause, audioPlaying } = useAudioPlay({
    onstart() {
        chatStatus.value = ChatStatus.PLAYING
        if (isPlayIdleReply.value) {
            isPlayIdleReply.value = false
            stop()
        }
    },
    onstop() {
        createIdleReplyTimer()
        if (!isAutoOpen.value) {
            changeChatStatus(ChatStatus.DEFAULT)
        } else {
            changeChatStatus(ChatStatus.RECORDING)
        }
    },
    onerror() {
        changeChatStatus(ChatStatus.DEFAULT)
    }
})
const getFile = async (params: any) => {
    const { url } = await voiceGenerate(params)
    return url
}
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
    if (isRecording.value || chatType.value !== ChatTypeEnum.DIGITAL) {
        return
    }
    start()
    return
}
const isAutoOpen = ref(true)
const changeRecorder = async () => {
    if ([ChatStatus.PLAYING, ChatStatus.THINKING].includes(chatStatus.value)) {
        return
    }
    if (isRecording.value) {
        isAutoOpen.value = false
        stop()
        changeChatStatus(ChatStatus.DEFAULT)
    } else {
        isAutoOpen.value = true
        changeChatStatus(ChatStatus.RECORDING)
    }
}
const file = ref('')
const isPlayIdleReply = ref(false)
const playIdleReply = async () => {
    if (chatType.value !== ChatTypeEnum.DIGITAL) {
        return Promise.reject()
    }
    if (
        !appInfo.value.is_digital ||
        !appInfo.value.digital_id ||
        appInfo.value.is_disable
    ) {
        return Promise.reject()
    }
    if (!file.value) {
        file.value = await getFile({
            type: 3,
            record_id: appInfo.value.id
        })
    }
    if (!file.value) return Promise.reject()
    isPlayIdleReply.value = true
    const key = Date.now()
    chatList.value.push({
        type: 2,
        typing: false,
        content: appInfo.value.digital.idle_reply,
        key
    })
    await nextTick()
    scrollToBottom()
    play(file.value, false)
}

const chatTypeChange = async (value: any) => {
    if (value === ChatTypeEnum.TEXT) {
        pause()
        close()
        chatStatus.value = ChatStatus.INITIALING
        clearTimeout(idleReplyTimer.value)
        getChatList()
    } else {
        isAutoOpen.value = true
        await authorize()
        draw(null, 0)
        try {
            await playIdleReply()
        } catch (error) {
            changeChatStatus(ChatStatus.RECORDING)
        }
        scrollToBottom()
    }
}
watch(chatStatus, (value) => {
    switch (value) {
        case ChatStatus.RECORDING: {
            startRecord()
        }
    }
})

watch(
    () => props.robotId,
    async (value) => {
        removeSse()
        robotStore.setRobotId(value)
        await getChatList()
        scrollToBottom()
        await getSession()
    },
    {
        immediate: true
    }
)
onMounted(async () => {
    await nextTick()
    if (chatList.value.length) {
        scrollToBottom()
    }
})
onUnmounted(() => {
    removeSse()
})
</script>
<style lang="scss" scoped>
.recorder {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    cursor: pointer;
    color: #fff;
    right: 72px;
    bottom: 55px;
    width: 90px;
    height: 90px;
    opacity: 1;
    border-radius: 50%;
    background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
    box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.2);

    &--stop {
        background: #b7b7b7;
        border: 1px solid #ffffff;
    }
}
.feedback-pop {
    :deep(.el-dialog__body) {
        padding: 0 !important;
    }
}
</style>
