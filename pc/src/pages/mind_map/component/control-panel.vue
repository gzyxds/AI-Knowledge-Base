<template>
    <div class="bg-body rounded-[12px] w-[330px] h-full flex flex-col">
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="px-[15px] py-[15px]">
                    <div>
                        <div>
                            <h3 class="font-bold">
                                <span>帮我生成</span>
                                <span class="text-error">*</span>
                            </h3>

                            <!--    输入框    -->
                            <div class="mt-[10px] flex">
                                <l-textarea
                                    v-model="userInput"
                                    maxlength="99999"
                                    :rows="7"
                                    :clearable="true"
                                    :show-word-limit="false"
                                    :customStyle="{
                                        paddingBottom: '24px'
                                    }"
                                    placeholder="请输入简单描述，AI将智能输出markdown内容"
                                    @click="handleClick"
                                >
                                </l-textarea>
                            </div>
                        </div>
                        <div class="mt-[15px]">
                            <div class="flex">
                                <h3 class="font-bold mr-auto">
                                    <span>需求描述</span>
                                </h3>
                                <ElButton
                                    link
                                    type="primary"
                                    v-if="configs.is_example"
                                    @click="useExample"
                                >
                                    试试示例
                                </ElButton>
                            </div>

                            <textarea
                                ref="textareaRef"
                                v-model="descInput"
                                class="bg-page h-[400px] w-full mt-[10px] rounded-[12px] p-[10px] resize-none border-[1px] border-solid border-[transparent] focus:border-primary"
                            />
                        </div>
                    </div>
                </div>
            </ElScrollbar>
        </div>
        <div class="px-[15px] pb-[15px]">
            <el-button
                size="large"
                type="primary"
                class="w-full !border-none"
                @click="handleCreate"
                :loading="isReceiving"
            >
                <span class="text-base font-bold">生成思维导图</span>
                <span class="text-sm ml-[10px]" v-if="configs.member_free">
                    会员免费
                </span>
                <span class="text-sm ml-[10px]" v-else-if="configs.balance > 0">
                    消耗 {{ configs.balance }} {{ appStore.getTokenUnit }}
                </span>
            </el-button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { watchThrottled } from '@vueuse/core'
import { chatSendText } from '@/api/chat'
import { getMindMapConfig } from '@/api/app'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { Sse } from '@/utils/http/sse'

const emit = defineEmits<{
    (event: 'update', value: string): void
    (event: 'history'): void
    (event: 'refresh'): void
}>()
const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()
const userInput = ref('')
const descInput = ref('')
const textareaRef = shallowRef<HTMLTextAreaElement>(null)

const isReceiving = ref(false)
const { data: configs, refresh } = useAsyncData(() => getMindMapConfig(), {
    lazy: true,
    default() {
        return {}
    }
})
const currentIndex = ref(-1)
const useExample = () => {
    const length = configs.value.example_content?.length
    if (length) {
        let index = Math.round(Math.random() * (length - 1))
        if (currentIndex.value === index) {
            if (index < length - 1) {
                index++
            } else {
                index--
            }
        }
        currentIndex.value = index
    }
}

watch(configs, () => {
    !descInput.value && useExample()
})

watch(currentIndex, (value) => {
    if (!configs.value.example_content) return
    const content = configs.value.example_content[value]
    if (content) {
        descInput.value = content
    }
})

const handleClick = () => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin()
    }
}
let sseInstance: Sse | null = null
const handleCreate = async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    if (!userInput.value) return feedback.msgError('请输入内容')
    if (isReceiving.value) return
    const key = Date.now()
    isReceiving.value = true

    sseInstance = chatSendText({
        type: 4,
        question: userInput.value
    })
    descInput.value = ''
    sseInstance.addEventListener('chat', ({ data: dataJson }: any) => {
        const { data, index } = dataJson

        descInput.value += data
    })
    sseInstance.addEventListener('finish', ({ data: dataJson }: any) => {
        const { data, index } = dataJson
        if (data) {
            descInput.value += data
        }
    })
    sseInstance.addEventListener('close', async () => {
        refresh()
        emit('refresh')
        await userStore.getUser()

        setTimeout(async () => {
            isReceiving.value = false

            scrollToBottom()
        }, 600)
    })
    sseInstance.addEventListener('error', async (ev) => {
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

        setTimeout(() => {
            isReceiving.value = false
        }, 200)
    })
}

const scrollToBottom = () => {
    textareaRef.value?.scrollTo({
        top: textareaRef.value?.scrollHeight
    })
}
watchThrottled(
    descInput,
    async (value) => {
        setTimeout(() => {
            emit('update', value)
        }, 500)
        isReceiving.value && scrollToBottom()
    },
    {
        throttle: 500
    }
)

defineExpose({
    changDescInput(value: string) {
        descInput.value = value
    }
})
</script>
<style lang="scss" scoped>
.submit-btn {
    font-size: 16px;
    height: 52px !important;
    border-radius: 8px !important;
    background: linear-gradient(
        90deg,
        var(--gradient-1) 0%,
        var(--gradient-2) 100%
    );
}
</style>
