<template>
    <view class="h-full flex flex-col">
        <view class="flex-1 min-h-0">
            <z-paging
                ref="pagingRef"
                v-model="dataList"
                :auto-clean-list-when-reload="false"
                @query="queryList"
                :fixed="false"
            >
                <view class="px-[30rpx] pt-[10rpx]">
                    <view
                        v-for="(item, index) in dataList"
                        class="data-item mb-[30rpx]"
                        :key="item.id"
                    >
                        <u-read-more
                            ref="readmoreRef"
                            close-text="展开全部"
                            :index="index"
                            :toggle="true"
                            text-indent="0"
                            show-height="350"
                            @open="openReadMore"
                            @close="closeReadMore"
                        >
                            <view class="p-[20rpx]">
                                <view
                                    class="flex mb-[20rpx] items-center justify-between"
                                >
                                    <view class="flex items-center">
                                        <view
                                            class="flex items-center flex-none"
                                        >
                                            <u-image
                                                width="44"
                                                height="44"
                                                :src="
                                                    item.user.avatar ||
                                                    DefaultAvatar
                                                "
                                                border-radius="50%"
                                            />
                                        </view>
                                        <view
                                            class="ml-2 w-[300rpx] line-clamp-1"
                                            >{{ item.user?.nickname }}</view
                                        >
                                    </view>

                                    <view class="ml-2 text-xs text-muted">
                                        {{ item.create_time }}
                                    </view>
                                </view>
                                <view class="flex mb-[20rpx] items-center">
                                    <view class="flex items-center flex-none">
                                        <image
                                            class="w-[44rpx] h-[44rpx]"
                                            src="@/packages/static/images/ask.png"
                                        />
                                    </view>
                                    <view class="line-clamp-2 ml-2">{{
                                        item.ask
                                    }}</view>
                                </view>
                                <view class="flex mb-[20rpx] items-stretch">
                                    <view class="flex flex-none pt-1">
                                        <image
                                            class="w-[44rpx] h-[44rpx]"
                                            src="@/packages/static/images/answer.png"
                                        />
                                    </view>
                                    <view class="ml-2">
                                        <text-item
                                            v-if="item.show"
                                            :is-markdown="true"
                                            :content="item.reply"
                                        />
                                        <view v-else class="line-clamp-2">
                                            {{ item.reply }}
                                        </view>
                                    </view>
                                    <!-- 
                                        @load="parseLoaded" -->
                                </view>
                                <view class="flex items-center">
                                    <view class="flex items-center flex-none">
                                        <image
                                            class="w-[44rpx] h-[44rpx]"
                                            src="@/packages/static/images/question.png"
                                        />
                                    </view>
                                    <view class="ml-2">
                                        <text-item
                                            v-if="item.show"
                                            :is-markdown="true"
                                            :content="item.feedback"
                                        />
                                        <view v-else class="line-clamp-2">
                                            {{ item.feedback || '-' }}
                                        </view>
                                    </view>
                                </view>
                            </view>
                        </u-read-more>
                        <view
                            class="border-t border-0 border-solid border-light p-[25rpx] flex justify-around"
                        >
                            <view
                                class="flex items-center"
                                @click="showCorrect(item)"
                            >
                                <u-icon name="edit-pen" :size="32" />
                                <view class="ml-[10rpx]"> 修正 </view>
                            </view>
                            <!-- <view
                                class="flex items-center"
                                @click="showReply(item.reply)"
                            >
                                <u-icon name="eye" :size="32" />
                                <view class="ml-[10rpx]"> 查看回复 </view>
                            </view> -->

                            <view
                                class="flex items-center"
                                @click="handleDelete([item.id])"
                            >
                                <u-icon name="trash" :size="32" />
                                <view class="ml-[10rpx]"> 删除 </view>
                            </view>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>
    </view>
    <ReplyPopup v-model:show="reply.show" :content="reply.content" />
    <CorrectPopup v-model:show="correct.show" :data="correct.data" />
</template>
<script lang="ts" setup>
import { useRoute } from 'uniapp-router-next'
import {computed, reactive, ref, shallowRef, watch} from 'vue'
import { delRobotChatRecord, getRobotDataRecord } from '@/api/robot'
import ReplyPopup from './reply-popup.vue'
import CorrectPopup from './correct-popup.vue'
import TextItem from '@/components/chat-record-item/text-item.vue'
import { cloneDeep } from 'lodash-es'
import DefaultAvatar from '@/static/images/user/default_avatar.png'

const route = useRoute()

const props = defineProps({
    isFeedback: {
        type: [String, Number],
        default: -1
    }
})
// const pages = reactive({
//     page_size: 10,
//     page_no: 1,
//     lists: [] as any[],
//     total: 0
// })

const reply = reactive({
    show: false,
    content: ''
})
const showReply = (content: string) => {
    reply.show = true
    reply.content = cloneDeep(content)
}

const correct = reactive({
    show: false,
    data: {}
})

const showCorrect = (data: any) => {
    correct.show = true
    correct.data = cloneDeep(data)
}
const robotId = computed(() => route.query.id)

const dataList = ref<any[]>([])
const pagingRef = shallowRef()

watch(() => props.isFeedback, (value) => {
    pagingRef.value?.reload()
})

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await getRobotDataRecord({
            page_no: pageNo,
            page_size: pageSize,
            robot_id: robotId.value,
            is_feedback: props.isFeedback
        })
        lists.forEach((element: any) => {
            element.show = false
        })
        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}
// const pagesChange = (e: any) => {
//     pages.page_no = e.current
//     getLists()
// }

// const getLists = async () => {
//     const { page_no, page_size } = pages
//     const { count, lists } = await getRobotDataRecord({
//         page_no,
//         page_size,
//         robot_id: robotId.value
//     })
//     pages.lists = lists
//     pages.total = count
// }

const handleDelete = async (ids: number[]) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定要删除？'
    })
    if (cancel) return
    await delRobotChatRecord({ ids, robot_id: robotId.value })
    pagingRef.value?.refresh()
}

const openReadMore = (index: number) => {
    dataList.value[index].show = true
}

const closeReadMore = (index: number) => {
    dataList.value[index].show = false
}

// onShow(() => {
//     getLists()
// })
</script>
<style lang="scss">
.data-item {
    border-radius: 16rpx;
    box-shadow: 0px 4rpx 30rpx 0px rgba(0, 0, 0, 0.08);
}
</style>
