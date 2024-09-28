<template>
    <view class="h-full flex flex-col">
        <u-navbar :back-text="title"></u-navbar>
        <view class="flex-1 min-h-0 pt-[20rpx]">
            <view class="h-full bg-white flex flex-col">
                <view class="flex py-[20rpx] px-[30rpx]">
                    <view>
                        <u-button
                            type="primary"
                            size="medium"
                            @click="showSharePopup('add')"
                        >
                            创建链接
                        </u-button>
                    </view>
                </view>
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
                                v-for="item in dataList"
                                class="data-item mb-[30rpx]"
                                :key="item.id"
                            >
                                <view class="p-[20rpx]">
                                    <view
                                        class="flex mb-[20rpx] items-baseline"
                                    >
                                        <view class="flex-none text-muted">
                                            &nbsp; APIKEY：
                                        </view>
                                        <view>{{ item.apikey }}</view>
                                    </view>
                                    <view
                                        class="flex mb-[20rpx] items-baseline"
                                    >
                                        <view class="flex-none text-muted">
                                            分享名称：
                                        </view>
                                        <view>{{ item.name }}</view>
                                    </view>
                                    <view
                                        class="flex mb-[20rpx] items-baseline"
                                    >
                                        <view class="flex-none text-muted">
                                            访问密码：
                                        </view>
                                        <view>{{ item.secret || '-' }}</view>
                                    </view>
                                    <view
                                        class="flex mb-[20rpx] items-baseline"
                                        v-if="type === 1"
                                    >
                                        <view class="flex-none text-muted">
                                            对话模式：
                                        </view>
                                        <view>
                                            {{
                                                item.chat_type == 1
                                                    ? '文本'
                                                    : '形象'
                                            }}
                                        </view>
                                    </view>
                                    <view class="flex items-baseline">
                                        <view class="flex-none text-muted">
                                            使用时间：
                                        </view>
                                        <view>
                                            {{ item.use_time }}
                                        </view>
                                    </view>
                                </view>
                                <view
                                    class="border-t border-0 border-solid border-light p-[25rpx] flex justify-between"
                                >
                                    <slot name="actions" :item="item" />
                                    <view
                                        v-if="type == 1"
                                        class="flex items-center"
                                        @click="copyLink(item)"
                                    >
                                        <u-icon name="file-text" :size="32" />
                                        <view class="ml-[10rpx]">
                                            复制链接
                                        </view>
                                    </view>
                                    <view
                                        class="flex items-center"
                                        @click="showMore(item)"
                                    >
                                        <u-icon
                                            name="more-dot-fill"
                                            :size="32"
                                        />
                                        <view class="ml-[10rpx]"> 更多 </view>
                                    </view>
                                    <!-- <template v-else>
                                        <view
                                            class="flex items-center"
                                            @click="showUsageSettings(item)"
                                        >
                                            <u-icon name="setting" :size="32" />
                                            <view class="ml-[10rpx]">
                                                用量设置
                                            </view>
                                        </view>
                                        <view
                                            class="flex items-center"
                                            @click="shareDelete(item.id)"
                                        >
                                            <u-icon name="trash" :size="32" />
                                            <view class="ml-[10rpx]">
                                                删除
                                            </view>
                                        </view>
                                    </template> -->
                                </view>
                            </view>
                        </view>
                    </z-paging>
                </view>
            </view>
        </view>
    </view>
    <CreateShare
        v-model:show="showShare"
        :data="actionState.item"
        :mode="actionState.type"
        @confirm="handleCreateShare"
        :type="type"
    />
    <UsageSettings
        v-model:show="usageSetting.show"
        :data="usageSetting.data"
        @confirm="handelUsageSettings"
    />
    <u-action-sheet
        :list="actionState.lists"
        v-model="actionState.show"
        @click="handleAction"
    ></u-action-sheet>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>
<script lang="ts">
export default {
    options: {
        multipleSlots: true // 在组件定义时的选项中启用多slot支持
    }
}
</script>
<script setup lang="ts">
import { reactive, ref, shallowRef } from 'vue'
import {
    delRelease,
    getReleaseList,
    putRelease,
    postRelease,
    postReleaseInfo
} from '@/api/robot'
import CreateShare from './create-share.vue'
import UsageSettings from './usage-settings.vue'
import { useCopy } from '@/hooks/useCopy'
import config from '@/config'
import FloatingMenu from "@/components/floating-menu/floating-menu.vue";
const props = withDefaults(
    defineProps<{
        title: string
        type: string | number
        robotId: number | string
        showMoreAction?: boolean
    }>(),
    {
        showMoreAction: false
    }
)

const showShare = ref(false)
const actionState = reactive({
    lists: [
        {
            text: '编辑',
            type: 'edit'
        },
        {
            text: '用量设置',
            type: 'setting'
        },
        {
            text: '删除',
            color: '#FA5151',
            type: 'delete'
        }
    ],
    show: false,
    item: {} as any,
    type: 'add'
})

const showMore = (item: any) => {
    actionState.item = item
    actionState.show = true
}

const showSharePopup = (type: string) => {
    actionState.type = type
    showShare.value = true
}
const handleAction = (index: number) => {
    const action = actionState.lists[index].type
    switch (action) {
        case 'setting':
            showUsageSettings(actionState.item)
            break
        case 'edit':
            showSharePopup('edit')
            break
        case 'delete':
            shareDelete(actionState.item.id)
            break
    }
}
const dataList = ref<any[]>([])
const pagingRef = shallowRef()
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await getReleaseList({
            page_no: pageNo,
            page_size: pageSize,
            type: props.type,
            robot_id: props.robotId
        })

        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const shareDelete = async (id: number) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定要删除？'
    })
    if (cancel) return
    await delRelease({
        id,
        type: props.type
    })
    pagingRef.value?.refresh()
}

const usageSetting = reactive({
    show: false,
    data: {} as any
})
const showUsageSettings = (item: any) => {
    usageSetting.show = true
    usageSetting.data = item
}

const handelUsageSettings = async (formData: any) => {
    await postRelease({
        ...formData,
        type: props.type,
        robot_id: props.robotId
    })
    pagingRef.value?.refresh()
}

const { copy } = useCopy()
const copyLink = (item: any) => {
    let origin = config.baseUrl
    //#ifdef H5
    origin = `${location.origin}/`
    //#endif
    const link = `${origin}chat/${item.apikey} #${item.name} ${
        item.secret ? `密码: ${item.secret}` : ''
    }`
    copy(link)
}

const handleCreateShare = async ({value: formData, type}: any) => {
    await (type == 'add'
        ? putRelease({
              ...formData,
              type: props.type,
              robot_id: props.robotId
          })
        : postReleaseInfo({
              ...formData
          }))
    pagingRef.value?.reload()
}
// const handleCreateShare = async (formData: any) => {
//     await putRelease({
//         ...formData,
//         type: props.type,
//         robot_id: props.robotId
//     })
//     pagingRef.value?.reload()
// }

const refresh = () => {
    pagingRef.value?.refresh()
}

defineExpose({
    refresh
})
</script>

<style lang="scss">
.data-item {
    border-radius: 16rpx;
    box-shadow: 0px 4rpx 30rpx 0px rgba(0, 0, 0, 0.08);
}
</style>
