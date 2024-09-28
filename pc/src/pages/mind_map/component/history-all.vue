<template>
    <div class="h-full p-[15px] rounded-[12px]">
        <div class="h-full flex flex-col">
            <div class="flex justify-between items-center">
                <el-page-header @back="emit('history')">
                    <template #content>
                        <span class="text-xl font-medium"> 生成记录 </span>
                    </template>
                </el-page-header>
                <div
                    class="bg-page-base text-sm px-[15px] py-[5px] rounded-[4px] cursor-pointer ml-[10px]"
                    @click="deleteChatLog()"
                >
                    清空记录
                </div>
            </div>
            <div class="flex-1 min-h-0 pt-[10px] mx-[-10px]">
                <ElScrollbar v-if="chatContentList.length">
                    <div class="flex flex-wrap items-stretch">
                        <div
                            class="2xl:w-1/4 xl:w-1/3 w-1/2 p-[8px]"
                            v-for="item in chatContentList"
                            :key="item.id"
                        >
                            <div
                                class="p-[15px] bg-page rounded-[10px] h-full record-item hover:bg-primary-light-9"
                            >
                                <div class="text-lg font-medium line-clamp-1">
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
const emit = defineEmits<{
    (event: 'view', value: any): void
    (event: 'history'): void
}>()

const { data: chatContentList, refresh } = await useAsyncData(
    () => getChatRecord({ type: 4, page_type: 0 }),
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
