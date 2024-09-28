<template>
    <view class="flex-col flex flex-1 min-h-0">
        <view class="flex-1 min-h-0">
            <l-watermark
                class="w-full h-full"
                <!-- #ifdef MP-WEIXIN -->
                z-index="-1"
                <!-- #endif -->
                :content="appStore.getChatConfig.watermark"
                :font="{
                    color: 'rgba(0,0,0,0.06)',
                    fontSize: 12
                }"
            >
                <z-paging
                    ref="pagingRef"
                    v-model="chatList"
                    use-chat-record-mode
                    :fixed="false"
                    height="100%"
                    :auto="false"
                    :safe-area-inset-bottom="false"
                    :auto-clean-list-when-reload="false"
                    :show-chat-loading-when-reload="true"
                    :default-page-size="20"
                    @query="queryList"
                    @keyboardHeightChange="keyboardHeightChange"
                    @hidedKeyboard="hidedKeyboard"
                >
                    <template #top>
                        <view>
                            <model-picker
                                v-model:sub_id="modelKey"
                                @update:modelConfig="changeModel"
                            />
                        </view>
                    </template>
                    <!-- 顶部提示文字 -->

                    <!-- style="transform: scaleY(-1)"必须写，否则会导致列表倒置（必须写在for循环标签上，不得写在容器上）！！！ -->
                    <!-- 注意不要直接在chat-item组件标签上设置style，因为在微信小程序中是无效的，请包一层view -->

                    <view
                        class="scroll-view-content pb-[40rpx]"
                        ref="contentRef"
                    >
                        <view
                            v-for="(item, index) in chatList"
                            :key="`${item.id} + ${index} + ''`"
                            style="transform: scaleY(-1)"
                        >
                            <view class="chat-record mt-[20rpx] pb-[40rpx]">
                                <chat-record-item
                                    v-if="active === 0"
                                    :record-id="item.id"
                                    :type="item.type == 1 ? 'right' : 'left'"
                                    :content="item.content"
                                    :loading="item.loading"
                                    :files-plugin="item.files_plugin"
                                    :avatar="
                                        item.type == 1
                                            ? userStore.userInfo.avatar
                                            : appStore.getChatConfig.chat_logo
                                    "
                                    :index="index"
                                    :time="
                                        item.type == 2 ? item.create_time : ''
                                    "
                                    :showCopyBtn="true"
                                    :showVoiceBtn="item.type == 2"
                                    :model-name="item.model"
                                    :show-poster="item.type == 2"
                                    @poster="handleDrawPoster(item.id)"
                                >
                                    <template #btn>
                                        <view
                                            v-if="item.type == 2 && index === 0"
                                            class="text-content text-sm flex items-center mr-[20rpx] mt-[16rpx]"
                                            @click.stop="rewrite(index)"
                                        >
                                            <u-icon
                                                name="reload"
                                                size="26"
                                            ></u-icon>
                                            <text class="ml-1">重新回答</text>
                                        </view>
                                    </template>
                                    <template #sub_actions>
                                        <view v-if="index === 0 && !isReceiving" class="flex flex-col" style="margin-left: 84rpx">
                                            <view
                                                v-for="(text, textIndex) in (relatedIssuesArr.length ? relatedIssuesArr : item.correlation)"
                                                :key="textIndex"
                                                class="inline-flex items-center rounded-md bg-white cursor-pointer mt-[20rpx]"
                                                style="padding: 16rpx 24rpx; width: fit-content;"
                                                @click.stop="sendLock(text)"
                                            >
                                                <text class="mr-1 text-base text-content">{{ text }}</text>
                                                <u-icon name="arrow-rightward" color="#999" size="30" />
                                            </view>
                                        </view>
                                    </template>
                                </chat-record-item>
                            </view>
                        </view>
                    </view>
                    <template #empty>
                        <view class="w-full min-h-full">
                            <view class="py-[100rpx]">
                                <problem-example
                                    v-if="problem.length"
                                    :data="problem"
                                    @click-item="sendLock"
                                    @show-more="showExamplePopup = true"
                                />
                            </view>
                        </view>
                    </template>
                    <template #bottom>
                        <chat-input
                            v-model="userInput"
                            :loading="isReceiving"
                            :showStop="isReceiving"
                            v-model:file-plugin="filePlugin.data"
                            :show-file-upload="filePlugin.show"
                            :showContinue="chatList.length"
                            safeAreaInsetBottom
                            @send="sendLock"
                            @pause="chatClose"
                            @focus="handleFocus"
                            @clear="cleanChatLock"
                            @continue="sendLock('继续')"
                        >
                            <template #actions>
                                <view class="flex items-center">
                                    <view
                                        class="text-base flex-1"
                                        v-if="userStore.isLogin"
                                    >
                                        剩余: {{ userStore.userInfo.balance }}
                                        {{ appStore.getTokenUnit }}
                                    </view>
                                </view>
                            </template>
                        </chat-input>
                    </template>
                </z-paging>
            </l-watermark>
        </view>
    </view>
    <!--  会话弹窗  -->
    <session-popup
        v-model:show="showPopup"
        v-model="sessionActive"
        :lists="sessionLists"
        @add="sessionAdd"
        @clear="sessionClear"
        @delete="sessionDelete"
        @edit="sessionEdit"
    />
    <!--  会话弹窗  -->
    <problem-example-popup
        v-model="showExamplePopup"
        :data="problem"
        @click-item="(value:any) => sendLock(value)"
    />
    <!--  生产对话海报  -->
    <dialog-poster ref="posterRef"></dialog-poster>
</template>

<script lang="ts" setup>
import { chatSendText, cleanChatRecord, getChatRecord } from '@/api/chat'
import { useLockFn } from '@/hooks/useLockFn'
import { useUserStore } from '@/stores/user'

import { useRouter } from 'uniapp-router-next'
import { onUnmounted, reactive, watch } from 'vue'
import { getSamplesLists } from '@/api/chat'

import SessionPopup from '@/packages/components/session/popup.vue'
import DialogPoster from '@/packages/components/dialog-poster/dialog-poster.vue'
import ProblemExample from './components/problem-example.vue'
import ProblemExamplePopup from './components/problem-example-popup.vue'
import { ref, shallowRef } from 'vue'
import { useAppStore } from '@/stores/app'
import { RequestErrMsgEnum } from '@/enums/requestEnums'
import { useSessionList } from './components/useSessionList'
import { useAudioPlay } from '@/hooks/useAudioPlay'
import { onShow } from '@dcloudio/uni-app'

const emit = defineEmits(['active'])
const props = withDefaults(
    defineProps<{
        active: number
    }>(),
    {
        active: 0
    }
)

const appStore = useAppStore()
const router = useRouter()
const userStore = useUserStore()
const contentRef = shallowRef()

const showRecorder = ref(false)
const chatList = ref<any[]>([])
const modelKey = ref('')
const {
    getSessionLists,
    initSessionActive,
    sessionActive,
    sessionAdd,
    sessionEdit,
    sessionLists,
    sessionDelete,
    sessionClear,
    currentSession
} = useSessionList()
const showPopup = ref(false)
const showExamplePopup = ref(false)
const problem = ref([])
const userInput = ref('')
const newUserInput = ref('')
const handleFocus = () => {
    if (!userStore.isLogin) {
        return toLogin()
    }
    scrollToBottom()
}

const pagingRef = shallowRef()

if (appStore.getChatConfig?.is_reopen) {
    sessionAdd()
    appStore.getChatConfig.is_reopen = 0
}

const posterRef = shallowRef()
const handleDrawPoster = async (recordId: number) => {
    const result = chatList.value.filter((item: any) => {
        return item.id == recordId
    })
    if (result.length != 2) {
        uni.$u.toast('上下文数据不对～')
        return
    }
    posterRef.value.initPosterData({
        title: result[1].content,
        content: result[0].content
    })
}
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [], count } = await getChatRecord({
            type: 1,
            category_id: sessionActive.value,
            page_size: pageSize / 2,
            page_no: pageNo
        })

        pagingRef.value?.complete(lists.reverse())
        if (count === 0) {
            setTimeout(() => {
                pagingRef.value?.scrollToTop(false)
            }, 200)
        } else if (pageSize === 1) {
            setTimeout(() => {
                scrollToBottom()
            }, 100)
        }
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const keyboardIsShow = ref(false)

const keyboardHeightChange = (res: any) => {
    if (res.height > 0) {
        keyboardIsShow.value = true
    } else {
        keyboardIsShow.value = false
    }
}

const hidedKeyboard = () => {
    keyboardIsShow.value = false
}

watch(
    sessionActive,
    (value) => {
        console.log(value)
        setTimeout(() => {
            if (value) {
                pagingRef.value?.reload()
            } else {
                pagingRef.value?.complete([])
                setTimeout(() => {
                    pagingRef.value?.scrollToTop(false)
                }, 100)
            }
        }, 10)
    },
    {
        immediate: true
    }
)

const filePlugin = reactive({
    show: false,
    data: {
        url: '',
        name: '',
        type: 'image'
    }
})

const changeModel = (value: any) => {
    filePlugin.show = !!value.support_image
    if (!filePlugin.show) filePlugin.data.url = ''
}
const { lockFn: cleanChatLock } = useLockFn(async () => {
    if (!userStore.isLogin) return toLogin()

    const modal = await uni.showModal({
        title: '温馨提示',
        content: '确定清空对话？'
    })
    if (modal.cancel) return
    chatClose()
    await cleanChatRecord({
        type: 1,
        category_id: sessionActive.value
    })
    pagingRef.value?.reload()
})

const cacheLastId = ref<number>(-1)
const { lockFn: rewrite } = useLockFn(async (index: number) => {
    if (isReceiving.value) return
    const last = chatList.value[index]
    const userInput = chatList.value.findLast(({ id }) => id === last.id)

    if (userInput) {
        cacheLastId.value = last.id
        // eslint-disable-next-line vue/no-mutating-props
        chatList.value.splice(index, 2)
        sendLock(userInput.content)
    }
})

const scrollToBottom = async () => {
    pagingRef.value?.scrollToBottom(false)
}

const isReceiving = ref(false)
let streamReader: any = null
const relatedIssuesArr = ref<string[]>([])

const chatClose = () => {
    //#ifdef H5
    streamReader?.cancel()
    //#endif
    //#ifdef MP-WEIXIN
    streamReader?.abort()
    //#endif
    setTimeout(() => {
        userInput.value = ''
    })
}
const chatContent = ref<any>({})
const { pauseAll: pauseAllVoice } = useAudioPlay()
const { lockFn: sendLock } = useLockFn(async (text: string, options?: any) => {
    console.log(options)
    showRecorder.value = false
    if (!userStore.isLogin) {
        return toLogin()
    }
    if (isReceiving.value) return
    const inputValue = text || userInput.value
    if (!inputValue) return
    if (sessionActive.value === 0) {
        await sessionAdd()
    }
    if (currentSession.value === '新的会话') {
        await sessionEdit({
            id: sessionActive.value,
            name: inputValue
        })
    }
    pagingRef.value.addChatRecordData({
        type: 1,
        content: inputValue,
        files_plugin: [{ ...filePlugin.data }]
    })
    chatContent.value = {
        type: 2,
        loading: true,
        content: [] as string[],
        id: Date.now()
    }
    pagingRef.value.addChatRecordData(chatContent.value)
    newUserInput.value = userInput.value
    userInput.value = ''

    try {
        isReceiving.value = true
        await chatSendText(
            {
                type: 1,
                other_id: sessionActive.value,
                question: inputValue,
                model: modelKey.value,
                file: filePlugin.data.url,
                ...options
            },
            {
                onstart(reader) {
                    streamReader = reader
                    pauseAllVoice()
                    userInput.value = ''
                },
                onmessage(value) {
                    value
                        .trim()
                        .split('data:')
                        .forEach(async (text) => {
                            if (text !== '') {
                                try {
                                    const dataJson = JSON.parse(text)
                                    const { object, choices, error } = dataJson
                                    if (error) {
                                        console.log(error)
                                        const { message, code } = error
                                        if (code === 1100) {
                                            if (!appStore.getIsShowRecharge) {
                                                uni.$u.toast(
                                                    `${appStore.getTokenUnit}数量已用完。请联系客服增加`
                                                )
                                            } else {
                                                const { cancel } =
                                                    await uni.showModal({
                                                        title: '温馨提示',
                                                        content: `${appStore.getTokenUnit}数量已用完，请前往充值`
                                                    })
                                                if (cancel) return
                                                router.navigateTo({
                                                    path: '/packages/pages/recharge/recharge'
                                                })
                                            }
                                        } else {
                                            message && uni.$u.toast(message)
                                        }
                                        userInput.value = newUserInput.value
                                        chatList.value.splice(0, 2)
                                        return
                                    }
                                    switch (object) {
                                        case 'chat': {
                                            const index = choices[0]?.index
                                            const data =
                                                choices[0]?.delta?.content
                                            if (
                                                !chatContent.value.content[
                                                    index
                                                ]
                                            ) {
                                                chatContent.value.content[
                                                    index
                                                ] = ''
                                            }
                                            chatContent.value.content[index] +=
                                                data
                                            break
                                        }
                                        case 'question': {
                                            relatedIssuesArr.value = JSON.parse(choices[0]?.delta?.content)
                                            break
                                        }
                                        case 'finish': {
                                            filePlugin.data.url = ''
                                        }
                                    }
                                } catch (error) {}
                            }
                        })
                },
                async onclose() {
                    // 重新对话成功才删除记录
                    if (
                        cacheLastId.value !== -1 &&
                        chatContent.value.content.length
                    ) {
                        await cleanChatRecord({
                            type: 1,
                            id: cacheLastId.value
                        })
                        cacheLastId.value = -1
                    }
                    isReceiving.value = false
                    setTimeout(() => {
                        pagingRef.value?.reload()
                        userStore.getUser()
                    }, 500)
                }
            }
        )
    } catch (error: any) {
        console.log('发送消息失败=>', error)
        if (error.errMsg !== RequestErrMsgEnum.ABORT) {
            chatList.value.splice(0, 2)
        }
        userInput.value = newUserInput.value
        isReceiving.value = false
    }
})

const toLogin = () => {
    router.navigateTo({ path: '/pages/login/login' })
}
const getProblemExample = async () => {
    problem.value = await getSamplesLists()
}

onShow(async () => {
    await getSessionLists()
    initSessionActive()
    getProblemExample()
    setTimeout(() => {
        // 监听键盘高度变化（H5、百度小程序、抖音小程序、飞书小程序不支持）
        // #ifndef H5 || MP-BAIDU || MP-TOUTIAO
        uni.onKeyboardHeightChange(pagingRef.value?._handleKeyboardHeightChange)
        // #endif
    }, 100)
})
onUnmounted(() => {
    chatClose()
})

defineExpose({
    showPopup
})
</script>
