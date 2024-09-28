<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col">
        <!-- #ifdef H5 -->
        <view
            class="bg-white flex justify-between items-center pl-[20rpx] py-[4rpx]"
        >
            <view class="flex items-center text-lg p-[20rpx]" @click="back">
                <u-icon name="arrow-left" :size="26" />
                <view class="flex-1 line-clamp-1 ml-[20rpx]">
                    {{ roleDetail?.name }}
                </view>
            </view>
            <model-picker
                v-model:sub_id="modelKey"
                :hasHiddenUnit="true"
                @update:modelConfig="changeModel"
            />
        </view>
        <!-- #endif -->
        <!-- #ifndef H5 -->
        <model-picker
            v-model:sub_id="modelKey"
            :hasHiddenUnit="false"
            @update:modelConfig="changeModel"
        />
        <!-- #endif -->
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
                    :auto="true"
                    :safe-area-inset-bottom="false"
                    :auto-clean-list-when-reload="false"
                    :show-chat-loading-when-reload="true"
                    :default-page-size="20"
                    @query="queryList"
                    @keyboardHeightChange="keyboardHeightChange"
                    @hidedKeyboard="hidedKeyboard"
                >
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
                                    :record-id="item.id"
                                    :type="item.type == 1 ? 'right' : 'left'"
                                    :content="item.content"
                                    :loading="item.loading"
                                    :avatar="
                                        item.type == 1
                                            ? userStore.userInfo.avatar
                                            : roleDetail.image
                                    "
                                    :index="index"
                                    :time="
                                        item.type == 2 ? item.create_time : ''
                                    "
                                    :showCopyBtn="true"
                                    :showVoiceBtn="item.type == 2"
                                    :files-plugin="item.files_plugin"
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
                            <view class="py-[30rpx]">
                                <chat-record-item
                                    type="left"
                                    :content="roleDetail.tips"
                                    :loading="false"
                                    :avatar="roleDetail.image"
                                    :index="0"
                                    :showCollectBtn="false"
                                    :showCopyBtn="false"
                                    :showVoiceBtn="false"
                                    @click-link="sendLock($event)"
                                ></chat-record-item>
                            </view>
                        </view>
                    </template>
                    <template #bottom>
                        <chat-input
                            v-model="userInput"
                            :loading="isReceiving"
                            :showStop="isReceiving"
                            safeAreaInsetBottom
                            :showContinue="chatList.length"
                            v-model:file-plugin="filePlugin.data"
                            :show-file-upload="filePlugin.show"
                            @send="sendLock"
                            @pause="chatClose"
                            @focus="handleFocus"
                            @clear="cleanChatLock"
                            @continue="sendLock('继续')"
                        >
                            <template #actions>
                                <view
                                    class="text-base"
                                    v-if="userStore.isLogin"
                                >
                                    剩余: {{ userStore.userInfo.balance }}
                                    {{ appStore.getTokenUnit }}
                                </view>
                            </template>
                        </chat-input>
                    </template>
                </z-paging>
            </l-watermark>
        </view>
        <!--  生产对话海报  -->
        <dialog-poster ref="posterRef"></dialog-poster>
    </view>
</template>

<script lang="ts" setup>
import { chatSendText, cleanChatRecord, getChatRecord } from '@/api/chat'
import { getRoleDetail } from '@/api/role'
import { useLockFn } from '@/hooks/useLockFn'
import { useUserStore } from '@/stores/user'

import { onShareAppMessage } from '@dcloudio/uni-app'
import { useRoute, useRouter } from 'uniapp-router-next'
import { onMounted, onUnmounted, reactive } from 'vue'
import { ref, shallowRef } from 'vue'
import { useAppStore } from '@/stores/app'
import { useShareMessage } from '@/hooks/useShareMessage'
import { RequestErrMsgEnum } from '@/enums/requestEnums'
import { useAudioPlay } from '@/hooks/useAudioPlay'
import ModelPicker from '@/components/model-picker/model-picker.vue'
import DialogPoster from '@/packages/components/dialog-poster/dialog-poster.vue'
const { useShare, resolveOptions, removeMixinShare } = useShareMessage()
const appStore = useAppStore()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const contentRef = shallowRef()

const showRecorder = ref(false)
const pageId = ref(0)
const roleDetail = ref<any>({
    image: '',
    tips: '',
    name: ''
})
const modelKey = ref<string>('')
const chatList = ref<any[]>([])
const userInput = ref('')
const newUserInput = ref('')
const handleFocus = () => {
    scrollToBottom()
}
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
const pagingRef = shallowRef()

const getDetail = async () => {
    try {
        roleDetail.value = await getRoleDetail({ id: pageId.value })
        useShare({
            desc: roleDetail.value.describe,
            title: roleDetail.value.name,
            imageUrl: roleDetail.value.image
        })
    } catch (error) {
        console.log('获取角色错误=>', error)
    }
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
            type: 3,
            other_id: pageId.value,
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

const { lockFn: cleanChatLock } = useLockFn(async () => {
    if (!userStore.isLogin) return toLogin()

    const modal = await uni.showModal({
        title: '温馨提示',
        content: '确定清空对话？'
    })
    if (modal.cancel) return
    chatClose()
    await cleanChatRecord({
        type: 3,
        other_id: pageId.value
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
const toLogin = () => {
    router.navigateTo({ path: '/pages/login/login' })
}
const { lockFn: sendLock } = useLockFn(async (text: string) => {
    showRecorder.value = false
    if (!userStore.isLogin) {
        return toLogin()
    }
    if (isReceiving.value) return
    const inputValue = text || userInput.value
    if (!inputValue) return

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
                type: 3,
                other_id: pageId.value,
                question: inputValue,
                model: modelKey.value,
                file: filePlugin.data.url
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
                                                if (cancel) {
                                                    return
                                                }
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
                                            console.log('终端')
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

const back = () => {
    router.navigateBack()
}

onMounted(async () => {
    const { id } = route.query
    pageId.value = id as number
    await getDetail()
})

onUnmounted(() => {
    chatClose()
})

//#ifdef MP
removeMixinShare()
onShareAppMessage(async () => {
    const resolved = await resolveOptions({
        title: roleDetail.value.name,
        imageUrl: roleDetail.value.image
    })
    return resolved
})
//#endif
</script>

<style lang="scss">
page {
    height: 100%;
    background: #f5f8fd;
}
</style>
