<template>
    <div class="flex items-center">
        <!--    向量模型    -->
        <el-select
            v-if="type === 'vectorModels'"
            class="flex-1"
            v-model="model"
            filterable
            :disabled="disabled"
        >
            <el-option
                class="!h-fit"
                v-for="item in chatModel.modelList"
                :value="item.id"
                :key="item.name"
                :label="item.alias"
            >
                <div class="my-1">
                    <div class="leading-6">{{ item.alias }}</div>
                </div>
            </el-option>
        </el-select>

        <!--    对话模型    -->
        <div
            v-if="type === 'chatModels'"
            class="select-input flex items-center justify-between flex-1 cursor-pointer rounded-[8px] w-[266px] h-[32px] px-[15px]"
            :class="[
                subModel ? '' : 'text-tx-placeholder',
                disabled ? 'text-tx-placeholder cursor-no-drop bg-[--el-disabled-bg-color]' : ''
            ]"
            @click="popupRef.open()"
        >
            <div class="line-clamp-1 flex-1">
                <span v-if="currentModel.alias">{{ currentModel.alias }}</span>
                <span v-else class="text-[#a8abb2]">请选择</span>
                <span v-if="currentModel.alias && currentModel.price == '0'">
                    (免费)
                </span>
                <span v-else-if="currentModel.alias">
                    ({{
                        `消耗${currentModel.price}${appStore.getTokenUnit}/1000字符`
                    }})
                </span>
            </div>
            <div class="flex-none ml-2 flex items-center">
                <Icon name="el-icon-ArrowDown" />
            </div>
        </div>

        <popup
            v-if="type === 'chatModels'"
            ref="popupRef"
            width="780px"
            title="模型选择"

            customClass="!rounded-[15px]"
        >
            <template #footer>
                <div></div>
            </template>

            <el-scrollbar height="50vh" max-height="70vh">
                <div class="model-container">
                    <el-collapse
                        v-model:active-name="activeName"
                        class="flex flex-wrap justify-between"
                        accordion
                    >
                        <div
                            v-for="(items, fIndex) in [evenArray, oddArray]"
                            :key="fIndex"
                        >
                            <div
                                v-for="(item, index) in items"
                                :key="item.id"
                                class="w-[350px] mt-[15px]"
                            >
                                <el-collapse-item
                                    class="bg-[#f8f8f8] dark:bg-[#0d0e10] border border-solid border-[transparent]"
                                    :class="{
                                        'el-collapse-item--active':
                                            isActiveItem(fIndex, index)
                                    }"
                                    :name="findItemIndex(fIndex, index)"
                                >
                                    <template #title>
                                        <div>
                                            <div
                                                class="flex items-center h-[46px] py-2"
                                            >
                                                <img
                                                    v-if="item.logo"
                                                    :src="item.logo"
                                                    class="w-[30px] h-[30px]"
                                                    alt="模型logo"
                                                />
                                                <span
                                                    class="mx-2 leading-[24px] mt-[2px] font-medium"
                                                    >{{ item.name }}</span
                                                >
                                                <span
                                                    v-if="item.is_free"
                                                    class="bg-[#E3FFF2] text-[#23B571] px-[5px] py-[2px] leading-[20px] rounded-[5px]"
                                                >
                                                    会员免费
                                                </span>
                                            </div>
                                            <!--                                            <div-->
                                            <!--                                                v-if="item.remarks"-->
                                            <!--                                                class="flex items-center text-xs text-tx-placeholder font-normal mb-2"-->
                                            <!--                                            >-->
                                            <!--                                                {{ item.remarks }}-->
                                            <!--                                            </div>-->
                                        </div>
                                    </template>

                                    <el-scrollbar
                                        height="100%"
                                        max-height="250px"
                                    >
                                        <div
                                            v-for="cItem in item.models"
                                            :key="cItem.id"
                                            class="flex justify-between mb-[14px] px-[15px] cursor-pointer hover:text-primary"
                                            :class="{
                                                'text-primary':
                                                    subModel === cItem.id
                                            }"
                                            @click="
                                                setModelAndClose(
                                                    item.id,
                                                    cItem.id
                                                )
                                            "
                                        >
                                            <div class="flex items-center">
                                                <span class="mr-2">{{
                                                    cItem.alias || '请选择'
                                                }}</span>
                                                <span
                                                    v-if="
                                                        cItem.alias &&
                                                        cItem.price == '0'
                                                    "
                                                    class="text-tx-placeholder"
                                                >
                                                    (免费)
                                                </span>
                                                <span
                                                    v-else
                                                    class="text-tx-placeholder"
                                                >
                                                    ({{
                                                        `消耗${cItem.price}${appStore.getTokenUnit}/1千字符`
                                                    }})
                                                </span>
                                            </div>
                                            <div
                                                class="flex items-center"
                                                v-if="subModel === cItem.id"
                                            >
                                                <Icon
                                                    name="el-icon-CircleCheck"
                                                    size="20"
                                                />
                                            </div>
                                            <div
                                                class="flex items-center"
                                                v-else
                                            >
                                                <div
                                                    class="w-[18px] h-[18px] rounded-full border border-solid border-[#cacbd3]"
                                                ></div>
                                            </div>
                                        </div>
                                    </el-scrollbar>
                                </el-collapse-item>
                            </div>
                        </div>
                    </el-collapse>
                </div>
            </el-scrollbar>
        </popup>
    </div>
</template>

<script setup lang="ts">
import { getAiModels } from '@/api/app'
import { useQuery } from '@tanstack/vue-query'
import { useVModel } from '@vueuse/core'
import { useAppStore } from '@/stores/app'
import Popup from '~/components/popup/index.vue'

const emit = defineEmits(['update:id', 'update:sub_id', 'update:modelConfig'])
const props = defineProps({
    id: {
        type: [String, Number],
        default: ''
    },
    sub_id: {
        type: [String, Number],
        default: ''
    },
    setDefault: {
        type: Boolean,
        default: true
    },
    type: {
        type: String as PropType<'chatModels' | 'vectorModels'>,
        default: 'chatModels'
    },
    disabled: {
        type: Boolean,
        default: false
    }
})
const model = useVModel(props, 'id', emit)
const subModel = useVModel(props, 'sub_id', emit)
const appStore = useAppStore()

const popupRef = shallowRef<InstanceType<typeof Popup>>()
const activeName = ref<number | string | number[]>(-1)

const chatModel = reactive({
    modelList: [] as any[]
})

const evenArray = computed(() =>
    chatModel.modelList.filter((_, index) => index % 2 === 0)
)
const oddArray = computed(() =>
    chatModel.modelList.filter((_, index) => index % 2 !== 0)
)

const currentModel = computed(() => {
    if (props.type == 'chatModels') {
        return (
            chatModel.modelList
                .flatMap((item: any) => item.models)
                .find((item: any) => item.id === subModel.value) || {}
        )
    } else {
        return (
            chatModel.modelList
                .find((item: any) => item.id === model.value) || {}
        )
    }
})

watch(
    () => currentModel.value,
    (value) => {
        emit('update:modelConfig', value)
    }
)

const { suspense } = useQuery(['modelLists'], {
    queryFn: getAiModels,
    cacheTime: 1000
})

const getChatModelFunc = async () => {
    try {
        const { data } = await suspense()
        chatModel.modelList = data[props.type]
        if (props.setDefault) {
            setDefaultModel()
        }
    } catch (error) {
        console.log('获取聊天模型数据错误=>', error)
    }
}

const setDefaultModel = () => {
    const defaultGroupIndex =
        chatModel.modelList.findIndex((item) => item.is_default) || 0
    const defaultModel = chatModel.modelList[defaultGroupIndex].models[0]
    if (defaultModel) {
        model.value = chatModel.modelList[defaultGroupIndex].id
        subModel.value = defaultModel.id
        activeName.value = defaultGroupIndex
    }
}

const findItemIndex = (fIndex: number, index: number) =>
    fIndex === 0 ? index * 2 : index * 2 + 1

const isActiveItem = (fIndex: number, index: number) =>
    chatModel.modelList[findItemIndex(fIndex, index)].models.some(
        (item: any) => item.id === subModel.value
    )

const setModelAndClose = (id: string, sub_id: string) => {
    model.value = id
    subModel.value = sub_id
    popupRef.value!.close()
}

getChatModelFunc()
</script>

<style lang="scss" scoped>
.select-input {
    box-shadow: 0 0 0 1px var(--el-border-color) inset;
}
.model-container {
    :deep(.el-collapse) {
        border: none;
    }
    :deep(.el-collapse-item) {
        border-radius: 8px;
        .is-active {
            border: none;
            @apply bg-primary-light-9;
        }
    }
    :deep(.is-active) {
        @apply border border-solid border-primary bg-primary-light-9;
    }
    :deep(.el-collapse-item__header) {
        height: 100%;
        line-height: inherit;
        text-align: left;
        padding: 0 15px;
        border: none;
        border-radius: 8px;
        background-color: inherit;
    }
    :deep(.el-collapse-item__wrap) {
        border: none;
        border-radius: 8px;
        background-color: transparent;
    }
    :deep(.el-collapse-item__content) {
        border-radius: 8px;
        padding-bottom: 0;
    }
    .el-collapse-item--active {
        border-radius: 8px;
        @apply border border-solid border-primary bg-primary-light-9;

        :deep(.el-collapse-item__header) {
            @apply bg-primary-light-9;
        }
    }
}
</style>
