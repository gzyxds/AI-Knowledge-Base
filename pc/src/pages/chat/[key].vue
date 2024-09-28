<template>
    <div>
        <div
            class="layout-bg"
            :style="{
                height: `${windowHeight}px`
            }"
        >
            <div
                class="h-full"
                v-if="chatType === ChatTypeEnum.TEXT"
                :class="{
                    'p-main': !appStore.isMobile
                }"
            >
                <div
                    class="h-full flex flex-col max-w-[720px] mx-auto bg-page rounded-[10px] overflow-hidden"
                    style="box-shadow: 0px 5px 40px 0px rgba(0, 0, 0, 0.05)"
                >
                    <div class="flex p-main items-center bg-white">
                        <img
                            v-if="robotInfo.robot.image"
                            :src="robotInfo.robot.image"
                            class="w-[40px] h-[40px] mr-[10px] flex-none rounded-full"
                            alt=""
                        />
                        <div>
                            <div class="text-2xl line-clamp-1">
                                {{ robotInfo.robot.name }}
                            </div>
                            <div class="text-tx-secondary line-clamp-1">
                                {{ robotInfo.robot.intro }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 min-h-0">
                        <el-watermark
                            class="h-full"
                            :content="appStore.getChatConfig.watermark"
                            :font="{
                                color: 'rgba(0,0,0,0.06)',
                                fontSize: 12
                            }"
                        >
                            <div
                                ref="containerRef"
                                class="h-full flex flex-col rounded relative"
                            >
                                <div
                                    class="absolute top-0 left-0 w-full h-full flex flex-col z-10"
                                >
                                    <div class="flex-1 min-h-0">
                                        <ElScrollbar ref="scrollbarRef">
                                            <div class="p-main">
                                                <div ref="innerRef">
                                                    <TheChatMsgItem
                                                        v-if="
                                                            robotInfo.robot
                                                                .welcome_introducer
                                                        "
                                                        class="mb-[20px]"
                                                        type="left"
                                                        :avatar="`${
                                                            robotInfo.robot
                                                                .icons
                                                                ? robotInfo
                                                                      .robot
                                                                      .icons
                                                                : robotInfo
                                                                      .robot
                                                                      .image
                                                        }`"
                                                        bg="#fff"
                                                    >
                                                        <Markdown
                                                            :content="
                                                                robotInfo.robot
                                                                    .welcome_introducer
                                                            "
                                                            @click-custom-link="
                                                                chat(
                                                                    $event,
                                                                    'link'
                                                                )
                                                            "
                                                        />
                                                    </TheChatMsgItem>

                                                    <div
                                                        v-for="(
                                                            item, index
                                                        ) in chatList"
                                                        :key="
                                                            item.id + '' + index
                                                        "
                                                        class="mt-4"
                                                    >
                                                        <TheChatMsgItem
                                                            v-if="
                                                                item.type == 1
                                                            "
                                                            type="right"
                                                            bg="var(--el-color-primary)"
                                                            color="white"
                                                            :avatar="
                                                                user_avatar
                                                            "
                                                        >
                                                            <TheChatMsgContent
                                                                :content="
                                                                    item.content
                                                                "
                                                            />
                                                            <template #actions>
                                                                <div
                                                                    class="my-[5px]"
                                                                >
                                                                    <ElButton
                                                                        link
                                                                        type="info"
                                                                        @click="
                                                                            copy(
                                                                                item.content
                                                                            )
                                                                        "
                                                                    >
                                                                        <template
                                                                            #icon
                                                                        >
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
                                                            v-if="
                                                                item.type == 2
                                                            "
                                                            type="left"
                                                            :time="
                                                                item.create_time
                                                            "
                                                            :avatar="`${
                                                                robotInfo.robot
                                                                    .icons
                                                                    ? robotInfo
                                                                          .robot
                                                                          .icons
                                                                    : robotInfo
                                                                          .robot
                                                                          .image
                                                            }`"
                                                            bg="#fff"
                                                        >
                                                            <TheChatMsgContent
                                                                :content="
                                                                    String(
                                                                        item.content
                                                                    )
                                                                "
                                                                type="html"
                                                                :typing="
                                                                    item.typing
                                                                "
                                                                show-copy
                                                                :show-context="
                                                                    !!robotInfo
                                                                        .robot
                                                                        .is_show_context
                                                                "
                                                                :show-quote="
                                                                    !!robotInfo
                                                                        .robot
                                                                        .is_show_quote
                                                                "
                                                                :show-voice="
                                                                    appStore.getIsVoiceOpen
                                                                "
                                                                :context="
                                                                    item.context
                                                                "
                                                                :quotes="
                                                                    item.quotes
                                                                "
                                                                :images="
                                                                    item.images
                                                                "
                                                                :files="
                                                                    item.files
                                                                "
                                                                :videos="
                                                                    item.videos
                                                                "
                                                                :record-id="
                                                                    item.id
                                                                "
                                                                :record-type="2"
                                                                :channel="key"
                                                                :user-id="
                                                                    userStore.visitorId
                                                                "
                                                            />
                                                            <template
                                                                #outer_actions
                                                            >
                                                                <ElButton
                                                                    v-if="
                                                                        item.create_time
                                                                    "
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
                                                                    :plain="
                                                                        true
                                                                    "
                                                                    bg
                                                                    size="small"
                                                                    :disabled="
                                                                        item.is_feedback
                                                                    "
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
                                                            </template>
                                                        </TheChatMsgItem>
                                                    </div>
                                                </div>
                                            </div>
                                        </ElScrollbar>
                                    </div>

                                    <div class="bg-white">
                                        <TheChatAction
                                            ref="chatActionRef"
                                            :loading="isReceiving"
                                            :menus="chatMenus"
                                            btn-color="#f6f6f6"
                                            @enter="chat"
                                            @clear="clearChatRecord"
                                            @pause="sseInstance?.abort()"
                                        />
                                        <div
                                            v-if="robotInfo.robot?.copyright"
                                            class="pb-[10px] text-center text-tx-regular"
                                        >
                                            {{ robotInfo.robot?.copyright }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </el-watermark>
                    </div>
                </div>
            </div>
            <div
                class="h-full relative"
                v-if="chatType === ChatTypeEnum.DIGITAL"
            >
                <canvas
                    ref="canvasRef"
                    id="digital-canvas"
                    :width="width * 2"
                    :height="cHeight * 2"
                ></canvas>

                <div
                    v-show="chatStatus === ChatStatus.INITIALING"
                    class="h-full flex justify-center items-center"
                >
                    <img
                        class="w-[400px]"
                        src="@/assets/image/loading.gif"
                        alt=""
                    />
                </div>
                <div
                    class="h-full"
                    v-show="chatStatus !== ChatStatus.INITIALING"
                    :style="{
                        background: robotInfo.robot.digital_bg
                    }"
                >
                    <div class="p-[20px] h-full flex relative z-10">
                        <div class="flex-1 h-full flex flex-col">
                            <div class="flex-1 min-h-0">
                                <div class="flex items-center cursor-pointer">
                                    <div
                                        class="text-xl flex-1 min-w-0 text-white"
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
                                        'recorder--stop':
                                            !isRecording && !audioPlaying
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
                                                        :key="
                                                            item.id + '' + index
                                                        "
                                                        class="mt-4 sm:pb-[20px]"
                                                    >
                                                        <TheChatMsgItem
                                                            v-if="
                                                                item.type == 1
                                                            "
                                                            type="right"
                                                            :avatar="
                                                                user_avatar
                                                            "
                                                            color="white"
                                                        >
                                                            <TheChatMsgContent
                                                                :content="
                                                                    String(
                                                                        item.content
                                                                    )
                                                                "
                                                            />
                                                        </TheChatMsgItem>
                                                        <TheChatMsgItem
                                                            v-if="
                                                                item.type == 2
                                                            "
                                                            type="left"
                                                            :time="
                                                                item.create_time
                                                            "
                                                            :avatar="
                                                                robotInfo.robot
                                                                    .icons
                                                                    ? robotInfo
                                                                          .robot
                                                                          .icons
                                                                    : robotInfo
                                                                          .robot
                                                                          .image
                                                            "
                                                            bg="#fff"
                                                        >
                                                            <TheChatMsgContent
                                                                :content="
                                                                    String(
                                                                        item.content
                                                                    )
                                                                "
                                                                type="html"
                                                                :typing="
                                                                    item.typing
                                                                "
                                                                :images="
                                                                    item.images
                                                                "
                                                                :files="
                                                                    item.files
                                                                "
                                                                :videos="
                                                                    item.videos
                                                                "
                                                                :record-id="
                                                                    item.id
                                                                "
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
                                        :menus="chatMenus"
                                        :show-pause="false"
                                        :show-clear="false"
                                        @enter="chat"
                                    >
                                    </TheChatAction>
                                </div>
                            </div>
                            <div
                                class="flex flex-col justify-center items-center"
                            >
                                <div
                                    class="gradient-button"
                                    @click="clearChatRecord"
                                >
                                    <Icon name="local-icon-clear" :size="24" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Login ref="loginRef" @confirm="login" />

        <Popup
            ref="popupRef"
            :async="true"
            width="390"
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
import { useWindowSize, useEventListener } from '@vueuse/core'
import { useElementSize, watchThrottled } from '@vueuse/core'
import { ElScrollbar } from 'element-plus'
import 'element-plus/es/components/scrollbar/style/index.mjs'
import Login from './_components/login.vue'
import user_avatar from '@/assets/image/user_avatar.png'
import { useLocalStorage } from '@vueuse/core'
import data_null from '@/assets/image/create_record_null.png'
import {
    getRobotChatRecord,
    robotChat,
    clearRobotChatRecord,
    getReleaseDetail,
    voiceTransfer,
    voiceGenerate,
    chatFeedBack
} from '~/api/robot'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import type { Sse } from '~/utils/http/sse'
import { AUTHORIZATION_KEY } from '@/enums/cacheEnums'
import Popup from '~/components/popup/index.vue'

enum ChatTypeEnum {
    TEXT = 1,
    DIGITAL = 2
}

const appStore = useAppStore()
const route = useRoute()
const userStore = useUserStore()
const { copy } = useCopy()
const loginRef = shallowRef<InstanceType<typeof Login>>()

const { key = '' } = route.params as { key: string }
const password = ref('')
const { height: windowHeight, width: windowWidth } = useWindowSize()

const { data: robotInfo } = await useAsyncData(
    () =>
        getReleaseDetail({
            apikey: key
        }),
    {
        default() {
            return {
                robot: {}
            }
        }
    }
)

// ------------ 处理反馈弹窗
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const feedbackParams = reactive({
    _index: -1,
    robot_id: robotInfo.value.robot.id,
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

const chatType = computed(() => {
    if (robotInfo.value.chat_type === ChatTypeEnum.DIGITAL) {
        if (
            robotInfo.value.robot.is_digital &&
            robotInfo.value.digital.id &&
            !robotInfo.value.digital.is_disable
        ) {
            return ChatTypeEnum.DIGITAL
        }
        return ChatTypeEnum.TEXT
    }
    return ChatTypeEnum.TEXT
})

if (appStore.isMobile && chatType.value === ChatTypeEnum.DIGITAL) {
    window.location.replace(
        `/mobile/packages/pages/digital_chat/share_chat?key=${key}`
    )
}

const chatMenus = computed(
    () => robotInfo.value.menus?.map((keyword: string) => ({ keyword })) || []
)
const checkNeedPwd = async () => {
    const pwd = useLocalStorage<Record<string, string>>(AUTHORIZATION_KEY, {})
    password.value = pwd.value[key] || ''
    if (robotInfo.value.pwd && !password.value) {
        loginRef.value?.open()
        return Promise.reject()
    }
}

const login = async (data: any) => {
    const pwd = useLocalStorage<Record<string, string>>(AUTHORIZATION_KEY, {})
    password.value = data.password
    pwd.value = Object.assign(pwd.value, {
        [key]: data.password
    })
    loginRef.value?.close()
    init()
    // await getChatList()
    // scrollToBottom()
}

const logout = () => {
    const pwd = useLocalStorage<Record<string, string>>(AUTHORIZATION_KEY, {})
    pwd.value = Object.assign(pwd.value, {
        [key]: ''
    })
}

const chatList = ref<any[]>([])
let lastPlayId = 0
const getChatList = async () => {
    try {
        const data = await getRobotChatRecord(
            {
                share_apikey: key,
                identity: userStore.visitorId,
                page_size: 25000
            },
            {
                password: password.value,
                authorization: key,
                identity: userStore.visitorId
            }
        )
        chatList.value = data.lists || []
        setTimeout(() => {
            scrollToBottom()
        })
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
    } catch (error) {
        if (error == '访问密码错误!') {
            logout()
            await checkNeedPwd()
        }
        return Promise.reject()
    }
}

const clearChatRecord = async () => {
    await checkNeedPwd()
    await feedback.confirm('确定清空记录？')
    await clearRobotChatRecord(
        {},
        {
            password: password.value,
            authorization: key,
            identity: userStore.visitorId
        }
    )
    getChatList()
}

let sseInstance: Sse | null = null
const isReceiving = ref(false)
const chatActionRef = shallowRef()
const chat = async (value: string, type = 'input') => {
    await userStore.getFingerprint()
    await checkNeedPwd()
    if (!value) return feedback.msgError('请输入问题')
    if (isReceiving.value) return
    isReceiving.value = true
    changeChatStatus(ChatStatus.THINKING)
    const chatKey = Date.now()
    chatList.value.push({
        type: 1,
        content: value
    })
    chatList.value.push({
        type: 2,
        typing: true,
        content: '',
        key: chatKey
    })
    chatActionRef.value?.setInputValue()
    const currentChat = chatList.value.find((item: any) => item.key === chatKey)
    sseInstance = robotChat(
        {
            question: value,
            stream: true
        },
        {
            password: password.value,
            authorization: key,
            identity: userStore.visitorId
        }
    )
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
    sseInstance.addEventListener('video', ({ data: dataJson }: any) => {
        try {
            const urls = JSON.parse(dataJson.data)
            currentChat.videos = urls
        } catch (error) {
            console.error(error)
        }
    })
    sseInstance.addEventListener('close', () => {
        setTimeout(async () => {
            await getChatList()
            currentChat.typing = false
            isReceiving.value = false
            scrollToBottom()
        }, 600)
    })
    sseInstance.addEventListener('error', (ev) => {
        changeChatStatus(ChatStatus.DEFAULT)
        if (ev.errorType === 'connectError') {
            feedback.msgError('请求失败，请重试')
        }
        // 登录失效
        if (ev.data?.code === 1200) {
            feedback.msgError(ev.data?.message)
            logout()
            setTimeout(() => {
                checkNeedPwd()
            }, 10)
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
// const containerRef = shallowRef()
// const { height: containerHeight, width: containerWidth } =
//   useElementSize(containerRef)
// const isVertical = computed(() => {
//   if (containerWidth.value / containerHeight.value > 1) {
//     return false
//   }
//   return true
// })

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

const idleReplyTimer = ref()
const isAutoOpen = ref(true)

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
    async ondata(result) {
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
    await authorize()
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
        const { url } = await voiceGenerate(params, {
            password: password.value,
            authorization: key,
            identity: userStore.visitorId
        })
        return url
    } catch (error) {
        changeChatStatus(ChatStatus.DEFAULT)
        return Promise.reject()
    }
}
const file = ref('')
const isPlayIdleReply = ref(false)
const playIdleReply = async () => {
    if (!robotInfo.value.robot.is_digital || !robotInfo.value.digital.id) {
        return Promise.reject()
    }
    if (!file.value) {
        file.value = await getFile({
            type: 3,
            record_id: robotInfo.value.robot.id
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
watch(chatStatus, (value) => {
    switch (value) {
        case ChatStatus.RECORDING: {
            startRecord()
        }
    }
})

const containRef = shallowRef()
const canvasRef = shallowRef<HTMLCanvasElement>()
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
        video.autoplay = false
        video.playsInline = true
        video.play()

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
const init = async () => {
    await getChatList()
    if (chatType.value == ChatTypeEnum.DIGITAL) {
        stayVideo.value = await loadVideo(
            robotInfo.value.digital.wide_stay_video
        )
        talkVideo.value = await loadVideo(
            robotInfo.value.digital.wide_talk_video
        )

        isAutoOpen.value = true
        try {
            await authorize()
            startRecord()
        } catch (error) {
            changeChatStatus(ChatStatus.DEFAULT)
        }
        setTimeout(() => {
            scrollToBottom()
        }, 100)
    }
}

onMounted(async () => {
    await userStore.getFingerprint()
    await checkNeedPwd()

    init()
})

const removeSse = () => {
    sseInstance?.removeEventListener('chat')
    sseInstance?.removeEventListener('close')
    sseInstance?.removeEventListener('error')
    sseInstance?.abort()
}

onUnmounted(() => {
    removeSse()
})

definePageMeta({
    layout: 'blank',
    hasPanel: false
})

useHead({
    title: robotInfo.value.name
})
</script>

<style lang="scss" scoped>
.layout-bg {
    background: url(../../assets/image/layout_bg.png) no-repeat;
    background-size: cover;
    background-position: center;
}

#digital-canvas {
    width: 100%;
    height: 100%;
    position: absolute;
    object-fit: cover;
    z-index: 8;
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
    &--small {
        width: 40px;
        height: 40px;
        position: absolute;
        right: 10px;
        bottom: 200px;
    }
}

.recorder {
    width: 90px;
    height: 90px;

    &--small {
        position: absolute;
        width: 60px;
        height: 60px;
        right: 10px;
        bottom: 100px;
    }

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