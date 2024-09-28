<template>
    <view class="h-full flex flex-col">
        <u-navbar :back-text="title"></u-navbar>
        <view class="flex-1 min-h-0 pt-[20rpx]">
            <view class="h-full bg-white flex flex-col">
                <view class="flex px-[20rpx] items-center flex-wrap mb-[20rpx]">
                    <view class="mt-[20rpx] mr-[20rpx]">
                        <u-button
                            type="primary"
                            size="medium"
                            @click="showApi = true"
                        >
                            创建API
                        </u-button>
                    </view>

                    <slot name="btn" />
                    <view class="mt-[20rpx] mr-[20rpx]">
                        <span>API根地址：{{ path }}</span>
                        <span @click="copy(path)" class="text-primary">
                            复制
                        </span>
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
                                            &nbsp; &nbsp; &nbsp; 名 称：
                                        </view>
                                        <view>{{ item.name }}</view>
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
                                        <view class="ml-[10rpx]"> 删除 </view>
                                    </view>
                                </view>
                            </view>
                        </view>
                    </z-paging>
                </view>
            </view>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
    <CreateApi v-model:show="showApi" @confirm="handleCreateApi" />
    <UsageSettings
        v-model:show="usageSetting.show"
        :data="usageSetting.data"
        @confirm="handelUsageSettings"
    />
</template>

<script setup lang="ts">
import { reactive, ref, shallowRef } from 'vue'
import {
    delRelease,
    getReleaseList,
    putRelease,
    postRelease
} from '@/api/robot'
import CreateApi from './create-api.vue'
import UsageSettings from './usage-settings.vue'
import config from '@/config'
import { useCopy } from '@/hooks/useCopy'
import FloatingMenu from "@/components/floating-menu/floating-menu.vue";
const props = defineProps<{
    title: string
    type: string | number
    robotId: number | string
}>()

const { copy } = useCopy()

const showApi = ref(false)
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

const path = ref(`${config.baseUrl}api`)
//#ifdef H5
path.value = `${location.origin}/api`
//#endif
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

const handleCreateApi = async (formData: any) => {
    await putRelease({
        ...formData,
        type: props.type,
        robot_id: props.robotId
    })
    pagingRef.value?.reload()
}
</script>
<style lang="scss">
.data-item {
    border-radius: 16rpx;
    box-shadow: 0px 4rpx 30rpx 0px rgba(0, 0, 0, 0.08);
}
</style>
