<template>
    <div class="flex flex-col h-full creation-result flex-1 min-w-0">
        <div class="rounded-[12px] pt-4 pl-4">
            <el-segmented
                v-model="currentTab"
                :block="false"
                class="segmented !p-[8px] !rounded-[12px] !bg-[#f7f7f7] dark:!bg-page"
                :options="[
                    { name: '本次创作结果', value: 'current' },
                    { name: '历史创作结果', value: 'history' }
                ]"
            >
                <template #default="{ item, index }">
                    <div class="flex flex-col items-center gap-1 p-2">
                        <div class="text-xl">{{ item.name }}</div>
                    </div>
                </template>
            </el-segmented>
        </div>

        <div class="flex-1 min-h-0 h-full">
            <template v-if="currentTab === 'current'">
                <ElScrollbar
                    v-if="currentCreationHistory.length"
                    ref="scrollbarRef"
                >
                    <div
                        v-if="currentCreationHistory.length"
                        ref="currentCreationHistoryRef"
                        class="px-[16px] pt-[16px]"
                    >
                        <template
                            v-for="(item, index) in currentCreationHistory"
                            :key="index"
                        >
                            <TheCreateRecordItem
                                v-for="(text, tindex) in item.reply"
                                :key="tindex"
                                class="mb-[16px]"
                                :content="text"
                                :typing="loading"
                                :time="item.create_time"
                                :title="item.title"
                                :record-id="item.id"
                                :edit="true"
                                :autoresize="false"
                                :show-rewrite="!!item.extra && !loading"
                                :model-name="item.model"
                                @copy="copyContent"
                                @rewrite="emit('rewrite', item)"
                                @refresh="emit('refresh')"
                                @content="
                                    (newContent) =>
                                        updateCurrentCreationHistory(
                                            index,
                                            tindex,
                                            newContent
                                        )
                                "
                            />
                        </template>
                    </div>
                </ElScrollbar>
                <div
                    v-else
                    class="h-full flex flex-col items-center justify-center py-[100px]"
                >
                    <img
                        class="w-[120px] h-[120px]"
                        src="@/assets/image/create_record_null.png"
                        alt=""
                    />
                    <div class="my-[16px] font-medium">
                        AI创作结果会在显示这里，现在你只需要
                    </div>
                    <div class="text-tx-regular text-sm">
                        1. 在左侧填好必要的信息，填写越详细，结果越准确哦
                        <br />
                        2.点击智能创作按钮，静待AI妙笔生花，一般在10秒内搞定
                    </div>
                </div>
            </template>

            <template v-else>
                <ElScrollbar v-if="pageInfo.lists.length" class="h-full">
                    <div
                        v-infinite-scroll="load"
                        infinite-scroll-distance="50"
                        class="h-full px-[16px] pt-[16px]"
                    >
                        <div>
                            <ElButton @click="emit('clean')">
                                清空记录</ElButton
                            >
                        </div>
                        <div
                            v-for="item in pageInfo.lists"
                            :key="item.id"
                            class="mt-[16px]"
                        >
                            <div
                                v-for="(text, tindex) in item.reply"
                                :key="tindex"
                                class="mb-[16px]"
                            >
                                <TheCreateRecordItem
                                    class="cursor-pointer"
                                    :content="text"
                                    :time="item.create_time"
                                    :title="item.title"
                                    :overflow="true"
                                    :show-rewrite="!!item.extra"
                                    :show-voice="appStore.getIsVoiceOpen"
                                    :record-id="item.id"
                                    :model-name="item.model"
                                    :index="tindex"
                                    @click="
                                        handelRecordPreview(item.title, text)
                                    "
                                    @copy="copyContent"
                                    @rewrite="emit('rewrite', item)"
                                    @refresh="emit('refresh')"
                                />
                            </div>
                        </div>
                    </div>
                </ElScrollbar>
                <div
                    v-else
                    class="h-full flex flex-col items-center justify-center py-[100px]"
                >
                    <el-empty :image="create_record_null" />
                </div>
            </template>
        </div>

        <TheCreateRecordPreview
            ref="createRecordPreviewRef"
            :content="previewData.content"
            :title="previewData.title"
            @copy="copyContent"
        />
    </div>
</template>

<script setup lang="ts">
import { useElementSize, useVModel } from '@vueuse/core'
import { ElScrollbar } from 'element-plus'
import create_record_null from '@/assets/image/create_record_null.png'
import { useAppStore } from '@/stores/app'

const props = defineProps<{
    current: string
    loading: boolean
    currentCreationHistory: any[]
    pageInfo: Record<string, any>
}>()
const emit = defineEmits([
    'update:current',
    'load',
    'clean',
    'rewrite',
    'refresh'
])
const currentTab = useVModel(props, 'current', emit)
const scrollbarRef = ref<InstanceType<typeof ElScrollbar>>()
const createRecordPreviewRef = shallowRef()
const currentCreationHistoryRef = shallowRef()
const appStore = useAppStore()
const { copy } = useCopy()
const copyContent = async (content: string) => {
    await copy(content)
}
const previewData = reactive({
    title: '',
    content: ''
})

const updateCurrentCreationHistory = (
    parentIndex: number,
    childIndex: number | string,
    newContent: string
) => {
    if (!props.currentCreationHistory.length) return
    props.currentCreationHistory[parentIndex].reply[childIndex] = newContent
}

const handelRecordPreview = (title: string, content: string) => {
    previewData.title = title
    previewData.content = content
    createRecordPreviewRef.value?.open()
}

const { height } = useElementSize(currentCreationHistoryRef)
watch(height, (value) => {
    if (props.loading) {
        const scrollContainerHeight = scrollbarRef.value?.wrapRef?.offsetHeight
        scrollbarRef.value?.setScrollTop(scrollContainerHeight!)
    }
})

const load = () => {
    emit('load')
}
</script>

<style lang="scss" scoped>
.creation-result {
    .segmented {
        :deep() {
            .el-segmented__item {
                border-radius: 12px;
            }
            .el-segmented__item:not(:last-child) {
                margin-right: 10px;
            }
        }
        :deep(.is-selected) .tabs-active_desc {
            color: white;
        }
        :deep(.el-segmented__item-selected) {
            border-radius: 12px;
            background: linear-gradient(90deg, #70c3ec 0%, #4A92FF 100%);
        }
    }
}
</style>
