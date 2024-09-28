<template>
    <div class="h-full">
        <div class="h-full flex flex-col">
            <div class="flex justify-between items-center p-4">
                <div class="font-bold">生成记录</div>
                <div
                    class="text-sm cursor-pointer ml-[10px] text-primary"
                    @click="emit('history')"
                >
                    全部记录
                </div>
            </div>
            <div class="flex-1 min-h-0">
                <ElScrollbar v-if="chatContentList.length">
                    <div class="px-4">
                        <div>
                            <div
                                v-for="item in chatContentList"
                                :key="item.id"
                                class="mb-4"
                            >
                                <div
                                    class="p-[15px] bg-page rounded-[12px] h-full record-item hover:bg-primary-light-9"
                                    :class="{
                                        '!bg-primary-light-9':
                                            currentId == item.id
                                    }"
                                >
                                    <div
                                        class="text-lg font-medium line-clamp-1"
                                    >
                                        帮我生成：{{ item.ask }}
                                    </div>
                                    <div
                                        class="cursor-pointer"
                                        v-for="(text, index) in item.reply"
                                        :key="index"
                                        @click="handlePreview(item.id, text)"
                                    >
                                        <div
                                            class="whitespace-pre-line line-clamp-5 my-[10px] h-[105px]"
                                        >
                                            {{ text }}
                                        </div>
                                        <div class="flex items-center">
                                            <div
                                                class="mr-auto text-tx-secondary text-sm"
                                            >
                                                {{ item.create_time }}
                                            </div>
                                            <div
                                                class="cursor-pointer text-tx-secondary flex"
                                                @click="deleteChatLog(item.id)"
                                            >
                                                <icon name="el-icon-Delete" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </ElScrollbar>
                <div
                    v-else
                    class="h-full flex flex-col items-center justify-center"
                >
                    <el-empty :image="create_record_null" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { cleanChatRecord, getChatRecord } from '~/api/chat'
import create_record_null from '@/assets/image/create_record_null.png'
const props = defineProps<{
    currentId: number
}>()
const emit = defineEmits<{
    (event: 'view', value: any): void
    (event: 'history'): void
}>()

const { data: chatContentList, refresh } = useAsyncData(
    () => getChatRecord({ type: 4, page_size: 5, page_no: 1 }),
    {
        transform(data) {
            return data.lists
        },
        default() {
            return []
        }
    }
)

const handlePreview = (id: number, text: string) => {
    emit('view', { id, text })
}

const handleCommand = (command: string, id: number, text: string) => {
    switch (command) {
        case 'view':
            handlePreview(id, text)
            break
        case 'delete':
            deleteChatLog(id)
            break
    }
}

const deleteChatLog = async (id?: number) => {
    if (!chatContentList.value.length) return
    await feedback.confirm(`确定${id ? '删除' : '清空'}记录？`)
    await cleanChatRecord({ type: 4, id })
    refresh()
}

defineExpose({
    refresh
})
</script>
<style lang="scss" scoped>
.record-item {
    &:hover {
        .view-btn {
            opacity: 1;
        }
    }
}
</style>
