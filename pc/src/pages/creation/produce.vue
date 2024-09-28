<template>
    <div>
        <NuxtLayout name="default">
            <div class="flex flex-col h-full">
                <div class="px-[16px] pt-[16px] round-[12px]">
                    <swiper
                        :slidesPerView="'auto'"
                        :spaceBetween="16"
                        class="my-swiper category-lists"
                    >
                        <swiper-slide
                            v-for="(item, index) in creationLists"
                            :key="item.id"
                            style="width: auto"
                        >
                            <div
                                v-if="Object.keys(item).includes('name')"
                                class="category-item"
                                :class="{
                                  'is-active': modelState.cateId == item.id
                                }"
                                @click="handleSelect(item.id)"
                            >
                                {{ item.name }}
                            </div>
                        </swiper-slide>
                    </swiper>
                </div>
                <div class="flex-1 min-h-0 flex">
                    <div class="py-[16px] pl-[16px]">
                        <ModelSelect
                            v-model="searchKeyword"
                            :model-state="modelState"
                            :current-model-list="currentModelList"
                            @select="selectModel"
                        />
                    </div>
                    <div class="flex-1 min-w-0 p-4">
                        <div class="h-full flex bg-body rounded-[12px]">
                            <div class="h-full border-r border-solid border-br-light">
                                <CreatePanel
                                    v-model="formData"
                                    class="h-full 2xl:w-[400px] xl:w-[350px] lg:w-[320px] w-[250px]"
                                    :model-data="modelData"
                                    :loading="isReceiving"
                                    @insert="insertExample"
                                    @create="handleCreate"
                                >
                                    <template #actions>
                                        <ModelPicker
                                            class="w-full my-[10px]"
                                            v-model:sub_id="modelState.modelKey"
                                            v-model:modelConfig="chatModel"
                                        />
                                    </template>
                                </CreatePanel>
                            </div>
                            <div class="flex-1 min-w-0">
                                <client-only>

                                    <CreateResults
                                        v-model:current="currentTab"
                                        :chatModel="chatModel"
                                        :current-creation-history="currentCreationHistory"
                                        :loading="isReceiving"
                                        :page-info="pageInfo"
                                        @load="load"
                                        @clean="cleanChatLog"
                                        @rewrite="rewrite"
                                        @refresh="creationHistoryRefresh"
                                    />
                                </client-only>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import {watchDebounced} from '@vueuse/core'
import {cloneDeep} from 'lodash-es'
import type {Sse} from 'utils/http/sse'
import CreatePanel from './_components/create-panel.vue'
import CreateResults from './_components/create-results.vue'
import ModelSelect from './_components/model-select.vue'
import {chatSendText, cleanChatRecord, getChatRecord} from '~/api/chat'
import {
    getCategoryList,
    getCreantionList,
    getCreationDetail
} from '~/api/create'
import {useAppStore} from '~/stores/app'
import {useUserStore} from '@/stores/user'
import {useRechargeStore} from '@/stores/recharge'
import {CreationEnum} from '~/enums/chatEnums'

const searchKeyword = ref('')
const route = useRoute()
const rechargeStore = useRechargeStore()
const isCollapseAllOpen = ref(true)
const collapseOpen = ref<any[]>([])
const formData = ref<any>({})
const appStore = useAppStore()
const userStore = useUserStore()
const router = useRouter()
const {cateId, modelId} = route.query as { cateId: string; modelId: string }
const currentTab = ref('current')
// 当前模型数据
const chatModel = shallowRef<any>({})
provide('chatModel', chatModel)

const modelState = reactive({
    cateId: cateId || '0',
    modelId,
    modelKey: ''
})

const handleSelect = async (id: string) => {
    modelState.cateId = id
    await modelListRefresh()
    const [first] = currentModelList.value || []
    router.replace({
        path: '',
        query: {
            cateId: id,
            modelId: first?.id
        }
    })
}
const {
    data: creationLists,
    pending,
    refresh
} = await useAsyncData(() => getCategoryList(), {
    default() {
        return []
    },
    lazy: true
})
const showModel = ref(false)
const swiperActiveIndex = ref(0)
const handleActiveIndexChange = (swiper: any) => {
    swiperActiveIndex.value = swiper.activeIndex
}
let swiperRef: any = null
const setSwiperRef = (swiper: any) => {
    swiperRef = swiper
}

const slideTo = (index: number) => {
    try {
        swiperRef?.slideTo(index)
    } catch (error) {
        console.error(error)
    }
}

const {data: currentModelList, refresh: modelListRefresh} =
    await useAsyncData(
        () =>
            getCreantionList({
                keyword: searchKeyword.value,
                category_id: modelState.cateId
            }),
        {lazy: true}
    )

watchDebounced(
    searchKeyword,
    (value) => {
        // refresh()
        modelListRefresh()
    },
    {
        debounce: 500
    }
)

const cleanChatLog = async () => {
    await feedback.confirm('确定清空创作记录？')
    await cleanChatRecord({type: 2, other_id: modelState.modelId})
    currentCreationHistory.value = []
    resetHistory()
}

const {data: modelData, refresh: modelApiRefresh} = await useAsyncData(
    () =>
        getCreationDetail({
            id: modelState.modelId
        }),
    {
        default() {
            return {}
        },
        transform(data) {
            return data
        },
        lazy: true
    }
)
const pageInfo = reactive({
    pageNo: 1,
    count: 0,
    pageSize: 15,
    lists: [] as any[]
})

const {refresh: creationHistoryRefresh, pending: creationHistoryPending} =
    await useAsyncData(
        () =>
            getChatRecord({
                other_id: modelState.modelId || 0,
                page_size: pageInfo.pageSize,
                page_no: pageInfo.pageNo,
                type: 2
            }),
        {
            default() {
                return []
            },
            transform(data) {
                pageInfo.count = data.count
                const list = data.lists.map((item: any) => {
                    let title = ''
                    if (isArray(item.ask)) {
                        const ask = item?.ask[0] || {}
                        title = `${ask.title}：${ask.value}`
                    } else {
                        title = item.ask
                    }
                    return {
                        ...item,
                        title
                    }
                })
                if (pageInfo.pageNo === 1) {
                    pageInfo.lists = []
                }
                pageInfo.lists.push(...list)
                return list
            },
            lazy: true
        }
    )
const resetHistory = async () => {
    Object.assign(pageInfo, {
        pageNo: 1,
        count: 0,
        pageSize: 15,
        lists: []
    })
    await creationHistoryRefresh()
}
const load = () => {
    if (creationHistoryPending.value) return
    if (pageInfo.count >= pageInfo.pageNo * pageInfo.pageSize) {
        pageInfo.pageNo++
        creationHistoryRefresh()
    }
}

const selectModel = (model: any) => {
    console.log('model', model)
    modelState.modelId = model.id
    resetHistory()
}
const {lockFn: rewrite} = useLockFn(async (item: any) => {
    if (item.extra) {
        formData.value = item.extra
        await nextTick()
        handleCreate()
    }
})

const insertExample = () => {
    modelData.value?.form?.forEach((item: any) => {
        if (item.props.placeholder && !item.props.defaultValue) {
            formData.value[item.props.field] = item.props.placeholder
        }
    })
}

const setFormDataDefault = () => {
    modelData.value?.form.forEach((item: any) => {
        if (item.props.defaultValue) {
            formData.value[item.props.field] = cloneDeep(item.props.defaultValue)
        } else {
            formData.value[item.props.field] = undefined
        }
    })
}
const toggleCollapsed = () => {
    if (isCollapseAllOpen.value) {
        isCollapseAllOpen.value = false
        collapseOpen.value = []
    } else {
        collapseOpen.value = creationLists.value.map((item: any) => item.id)
        isCollapseAllOpen.value = true
    }
}

const currentCreationHistory = ref<any[]>([])

const generateTitle = () => {
    const firstForm = modelData.value?.form[0] || {}
    return `${firstForm.props.title}：${
        formData.value[firstForm.props.field] || ''
    }`
}
const isReceiving = ref(false)
let sseInstance: Sse

const handleCreate = async () => {
    if (isReceiving.value) return
    currentTab.value = 'current'

    isReceiving.value = true
    currentCreationHistory.value = []
    slideTo(1)
    try {
        sseInstance = chatSendText({
            other_id: modelState.modelId,
            question: formData.value,
            type: 2,
            creation_type: CreationEnum.Normal,
            model: modelState.modelKey,
        })

        sseInstance.addEventListener('close', async () => {
            userStore.getUser()
            setTimeout(async () => {
                isReceiving.value = false
                await resetHistory()
                const currentLen = currentCreationHistory.value.length
                currentCreationHistory.value[currentLen-1].id = pageInfo.lists[0].id
            }, 200)
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
                slideTo(0)
            }, 200)
        })
        sseInstance.addEventListener('chat', ({data: dataJson}) => {
            const {id: chatId, event, data, code, index} = dataJson!
            let chatIndex = currentCreationHistory.value.findIndex(
                (item) => item.id === chatId
            )
            if (chatIndex === -1) {
                currentCreationHistory.value.push({
                    create_time: timeFormat(Date.now(), 'yyyy-mm-dd hh:MM:ss'),
                    title: formData.value.question
                        ? formData.value.question
                        : generateTitle(),
                    reply: [],
                    extra: cloneDeep(formData.value),
                    id: chatId
                })
                chatIndex = currentCreationHistory.value.length - 1
            }

            if (data) {
                if (!currentCreationHistory.value[chatIndex].reply[index]) {
                    currentCreationHistory.value[chatIndex].reply[index] = ''
                }
                currentCreationHistory.value[chatIndex].reply[index] += data
            }
        })
        sseInstance.addEventListener('finish', ({data: dataJson}) => {
            const {data, index, id: chatId} = dataJson!
            const chatIndex = currentCreationHistory.value.findIndex(
                (item) => item.id === chatId
            )
            if (data) {
                currentCreationHistory.value[chatIndex].reply[index] += data
            }
            setFormDataDefault()
        })
    } catch (error) {
        isReceiving.value = false
    }
}

watch(
    creationLists,
    (value) => {
        isCollapseAllOpen.value = false
        toggleCollapsed()
    },
    {
        immediate: true
    }
)

const removeSse = () => {
    sseInstance?.removeEventListener('close')
    sseInstance?.removeEventListener('chat')
    sseInstance?.removeEventListener('error')
    sseInstance?.removeEventListener('finish')
    sseInstance?.abort()
    isReceiving.value = false
}
watch(
    () => route.query,
    ({cateId, modelId}) => {
        modelState.cateId = cateId as string
        modelState.modelId = modelId as string
        currentCreationHistory.value = []
        currentTab.value = 'current'
        slideTo(0)
        removeSse()
        if (modelId) {
            modelApiRefresh()
            resetHistory()
        } else {
            modelData.value = {}
        }
    }
)

watch(
    () => currentModelList.value,
    (value) => {
        if (value.length && modelState.modelId == undefined) {
            router.replace({
                path: '',
                query: {
                    cateId: modelState.cateId,
                    modelId: value[0].id
                }
            })
        }
    },
    {
        deep: true
    }
)

definePageMeta({
    layout: false,
    activePath: '/creation',
    auth: true,
    hasPanel: true,
    hiddenFooter: true
})

onUnmounted(() => {
    removeSse()
})
</script>

<style lang="scss" scoped>
.category {
    :deep() {
        .el-menu {
            --el-menu-item-height: 38px;
            --el-menu-base-level-padding: 15px;
            border-right: none;
            --el-menu-bg-color: transparent;

            .el-menu-item {
                padding: 0 16px;
                margin: var(--el-menu-base-level-padding);
                border-radius: 12px;

                &:hover {
                    background: none;
                    color: var(--el-color-primary);
                }

                &.is-active {
                    background: var(--el-color-primary-light-9);
                    color: var(--el-color-primary);
                }
            }
        }
    }
}

.category-lists {
    border-radius: 12px;
    padding: 8px;
    @apply bg-body;

    .category-item {
        line-height: 34px;
        border-radius: 12px;
        text-align: center;
        height: 36px;
        padding: 0px 24px !important;
        min-width: 85px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        white-space: nowrap;
        @apply bg-body;
        &.is-active {
            @apply text-white bg-primary;
        }
    }
}

:deep() {
    .model-select-drawer {
        .el-drawer__body {
            padding: 0;
        }
    }
}
</style>
