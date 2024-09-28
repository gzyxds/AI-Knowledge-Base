<template>
    <div>
        <NuxtLayout name="default">
            <div class="h-full flex">
                <div class="p-[16px]">
                    <role-sidebar
                        v-model:keyword="keyword"
                        :sidebarList="sidebarList"
                        :currentId="currentId"
                        @ontoggle="toggleRole"
                    ></role-sidebar>
                </div>
                <div class="flex-1 min-w-0 pr-4 py-4">
                    <el-watermark
                        class="h-full"
                        :content="appStore.getChatConfig.watermark"
                        :font="{
                            color: isDark
                                ? 'rgba(256,256,256,0.08)'
                                : 'rgba(0,0,0,0.06)',
                            fontSize: 12
                        }"
                    >
                        <div class="h-full flex flex-col bg-body rounded-[12px]">
                            <div class="flex-1 min-h-0">
                                <ElScrollbar ref="scrollbarRef">
                                    <div>
                                        <!--    default message    -->
                                        <div
                                            class="px-[40px] pt-[40px]"
                                            v-if="
                                                !chatList.length &&
                                                roleDetail?.tips
                                            "
                                        >
                                            <TheChatMsgItem
                                                type="left"
                                                :avatar="roleDetail.image"
                                                bg="var(--el-bg-color-page)"
                                            >
                                                <TheChatMsgContent
                                                    :content="roleDetail?.tips"
                                                    type="html"
                                                    :typing="false"
                                                    :line-numbers="
                                                        !appStore.isMobile
                                                    "
                                                    :show-collect-btn="false"
                                                    :show-copy-btn="false"
                                                    :show-poster="false"
                                                    :show-voice="false"
                                                    class="mb-[15px] last-of-type:mb-0"
                                                    @click-custom-link="
                                                        chat($event, 'link')
                                                    "
                                                />
                                            </TheChatMsgItem>
                                        </div>
                                        <!--    have message    -->
                                        <div
                                            v-if="chatList.length"
                                            ref="innerRef"
                                            class="px-8"
                                        >
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
                                                        :content="item.content"
                                                        :files-plugin="
                                                            item.files_plugin
                                                        "
                                                    />
                                                    <template #actions>
                                                        <div class="my-[5px]">
                                                            <ElButton
                                                                link
                                                                type="info"
                                                                @click="
                                                                    copy(
                                                                        item.content
                                                                    )
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
                                                    :avatar="roleDetail?.image"
                                                    :time="item.create_time"
                                                    bg="var(--el-bg-color-page)"
                                                    :modelName="item.model"
                                                >
                                                    <template
                                                        v-for="(
                                                            text, i
                                                        ) in item.content"
                                                        :key="i"
                                                    >
                                                        <TheChatMsgContent
                                                            :content="text"
                                                            type="html"
                                                            :typing="
                                                                item.typing
                                                            "
                                                            :line-numbers="
                                                                !appStore.isMobile
                                                            "
                                                            :show-rewrite="
                                                                index ===
                                                                chatList.length -
                                                                    1
                                                            "
                                                            show-copy
                                                            :show-voice="
                                                                appStore.getIsVoiceOpen
                                                            "
                                                            class="mb-[15px] last-of-type:mb-0"
                                                            :class="{
                                                                'pt-[15px] border-t border-solid border-br-light':
                                                                    i > 0
                                                            }"
                                                            :index="i"
                                                            :record-id="item.id"
                                                            show-poster
                                                            :record-list="
                                                                chatList
                                                            "
                                                            @rewrite="rewrite"
                                                        />
                                                    </template>

                                                    <template #outer_actions>
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
                            <div class="mb-[10px] px-[30px]">
                                <TheChatAction
                                    ref="chatActionRef"
                                    :loading="isReceiving"
                                    :show-continue="chatList.length"
                                    :show-file-upload="filePlugin.show"
                                    v-model:file-plugin="filePlugin.data"
                                    @enter="chat"
                                    @clear="clearChatRecord"
                                    @pause="sseInstance?.abort()"
                                    @focus="inputFocus"
                                    @continue="chat('继续', 'btn')"
                                >
                                    <template #btn>
                                        <div class="mr-[10px]">
                                            <ModelPicker
                                                class="min-w-[280px] select-class"
                                                v-model:sub_id="model"
                                                @update:model-config="
                                                    changeModel
                                                "
                                            />
                                        </div>
                                    </template>
                                </TheChatAction>
                            </div>
                        </div>
                    </el-watermark>
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import { useDark, useElementSize, watchThrottled } from '@vueuse/core'
import { ElScrollbar } from 'element-plus'
import { getRoleList, getRoleDetail } from '~/api/role'
import { chatSendText, cleanChatRecord, getChatRecord } from '~/api/chat'
import type { Sse } from '~/utils/http/sse'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import { watchDebounced } from '@vueuse/core'
import RoleSidebar from './_components/role-sidebar.vue'

const isDark = useDark()
const { copy } = useCopy()
const route = useRoute()
const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()

const chatActionRef = shallowRef()
// 内容滚动条ref
const scrollbarRef = shallowRef<InstanceType<typeof ElScrollbar>>()
const innerRef = ref<HTMLDivElement>()

const model = ref<string>('')
const keyword = ref<string>('')
//当前选中的侧边栏
const currentId = ref(Number(route.query.id))

// 获取角色侧边栏
const { data: sidebarList, refresh: getSidebarList } = await useAsyncData(
    () =>
        getRoleList({
            keyword: keyword.value
        }),
    {
        default() {
            return []
        },
        lazy: true,
        immediate: false
    }
)

// 获取角色详情
const { data: roleDetail, refresh: getRoleInfo } = await useAsyncData(
    () =>
        getRoleDetail({
            id: currentId.value
        }),
    {
        lazy: true,
        immediate: false
    }
)

// 获取对话记录
const { data: chatList, refresh: getChatList } = await useAsyncData(
    () =>
        getChatRecord({
            type: 3,
            other_id: currentId.value,
            page_type: 0
        }),

    {
        transform(data) {
            return data.lists || []
        },
        default() {
            return []
        },
        lazy: true
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

const toggleRole = ({ id, image }: any) => {
    router.push({
        path: '/dialogue/role',
        query: {
            id
        }
    })
    currentId.value = Number(id)
    nextTick(async () => {
        await getChatList()
        roleDetail.value = {
            image: image
        }
        await getRoleInfo()
        scrollToBottom()
    })
}

const clearChatRecord = async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    await feedback.confirm('确定清空记录？')
    await cleanChatRecord({
        other_id: currentId.value,
        type: 3
    })
    getChatList()
}

const cacheLastId = ref<number>(-1)
//重写
const { lockFn: rewrite } = useLockFn(async () => {
    const last = chatList.value[chatList.value.length - 1]
    const userInput = chatList.value.find(({ id }) => id === last.id)

    if (userInput) {
        cacheLastId.value = last.id
        // eslint-disable-next-line vue/no-mutating-props
        chatList.value.splice(chatList.value.length - 2, 2)
        chat(userInput.content)
    }
})

const inputFocus = () => {
    if (!userStore.isLogin) {
        chatActionRef.value?.blur()
        return userStore.toggleShowLogin()
    }
    scrollToBottom()
}
let sseInstance: Sse | null = null
const isReceiving = ref(false)
const relatedIssuesArr = ref<string[]>([])

const chat = async (value: string, type = 'input') => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (!value) return feedback.msgError('请输入问题')
    if (isReceiving.value) return
    const key = Date.now()
    isReceiving.value = true
    chatList.value.push({
        type: 1,
        content: value,
        files_plugin: [{ ...filePlugin.data }]
    })
    chatList.value.push({
        type: 2,
        typing: true,
        content: [''],
        key
    })
    chatActionRef.value?.setInputValue()
    const currentChat = chatList.value.find((item: any) => item.key === key)

    sseInstance = chatSendText({
        type: 3,
        other_id: currentId.value,
        question: value,
        model: model.value,
        file: filePlugin.data.url
    })

    sseInstance.addEventListener('chat', ({ data: dataJson }: any) => {
        const { data, index } = dataJson
        if (!currentChat.content[index]) {
            currentChat.content[index] = ''
        }
        currentChat.content[index] += data
    })
    sseInstance.addEventListener('question', ({ data: dataJson }: any) => {
        relatedIssuesArr.value = JSON.parse(dataJson.data)
    })
    sseInstance.addEventListener('finish', ({ data: dataJson }: any) => {
        const { data, index } = dataJson
        if (data) {
            currentChat.content[index] += data
        }
        filePlugin.data.url = ''
    })
    sseInstance.addEventListener('close', async () => {
        // 重新对话成功才删除记录
        if (cacheLastId.value !== -1 && currentChat.content[0].length) {
            await cleanChatRecord({
                type: 1,
                id: cacheLastId.value
            })
            cacheLastId.value = -1
        }
        await userStore.getUser()
        setTimeout(async () => {
            await getChatList()
            isReceiving.value = false
            currentChat.typing = false
            await nextTick()
            scrollToBottom()
        }, 600)
    })
    sseInstance.addEventListener('error', async (ev) => {
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
    { immediate: true }
)

const removeSse = () => {
    sseInstance?.removeEventListener('chat')
    sseInstance?.removeEventListener('close')
    sseInstance?.removeEventListener('error')
    sseInstance?.removeEventListener('finish')
    sseInstance?.abort()
    isReceiving.value = false
}

//选择侧边栏列表第一个选项
const selectFirst = () => {
    if (!currentId.value) {
        toggleRole(sidebarList.value[0].skill[0])
    } else {
        for (let i = 0; i < sidebarList.value.length; i++) {
            const item = sidebarList.value[i]
            for (let j = 0; j < item.skill.length; j++) {
                const item1 = item.skill[j]
                if (item1.id === currentId.value) {
                    toggleRole(item1)
                    return
                }
            }
        }
    }
}

watchDebounced(
    keyword,
    (value) => {
        getSidebarList()
    },
    {
        debounce: 500
    }
)

onMounted(async () => {
    await getSidebarList()
    selectFirst()
    await getRoleInfo()
})

onUnmounted(() => {
    removeSse()
})

definePageMeta({
    layout: false,
    hasPanel: true,
    hiddenFooter: true,
    activePath: '/dialogue/chat'
})
</script>

<style lang="scss" scoped>
.select-class :deep(.select-input) {
    border-radius: 8px;
}
</style>
