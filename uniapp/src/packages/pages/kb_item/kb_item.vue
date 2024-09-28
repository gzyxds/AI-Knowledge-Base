<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <z-paging
        ref="pagingRef"
        v-model="dataList"
        :safe-area-inset-bottom="false"
        :auto-clean-list-when-reload="false"
        @query="queryList"
    >
        <template #top>
            <view class="py-[24rpx] px-[20rpx]">
                <view class="flex justify-between">
                    <view
                        class="bg-primary py-[20rpx] px-[30rpx] rounded text-white mr-2"
                        @click="openPop('')"
                    >
                        录入数据
                    </view>

                    <view class="ml-2 flex-1 min-w-0">
                        <u-search
                            v-model="keyword"
                            @input="pagingRef?.reload()"
                            bg-color="#fff"
                            height="80"
                            shape="square"
                            border-color="#e5e5e5"
                            placeholder="请输入关键词"
                            :show-action="false"
                        ></u-search>
                    </view>
                </view>
            </view>
        </template>
        <view class="pb-[24rpx] px-[20rpx] pt-[10rpx]">
            <view
                v-for="(item, index) in dataList"
                :key="index"
                class="mb-[30rpx] shadow rounded-[16rpx]"
            >
                <view class="p-[30rpx]">
                    <view class="flex items-center">
                        <u-tag
                            v-if="item.status == 0"
                            type="info"
                            :text="item.status_msg"
                            size="mini"
                        >
                        </u-tag>
                        <u-tag
                            v-if="item.status == 1"
                            type="warning"
                            :text="item.status_msg"
                            size="mini"
                        >
                        </u-tag>
                        <u-tag
                            v-if="item.status == 2"
                            type="success"
                            :text="item.status_msg"
                            size="mini"
                        >
                        </u-tag>
                        <u-tag
                            v-if="item.status == 3"
                            type="error"
                            :text="item.status_msg"
                            size="mini"
                            @click="showText(item.error)"
                        >
                        </u-tag>
                        <view class="ml-auto text-sm text-muted">
                            {{ item.create_time }}
                        </view>
                    </view>
                    <view class="mt-[30rpx] font-bold line-clamp-1">
                        {{ item.question }}
                    </view>
                    <view class="mt-[25rpx] text-muted line-clamp-3">
                        {{ item.answer }}
                    </view>
                </view>
                <view class="p-[30rpx] flex justify-between items-center">
                    <view class="text-muted">
                        消耗电力值：{{ item.tokens }}
                    </view>
                    <view class="flex items-center">
                        <view
                            class="flex items-center"
                            @click="openPop(item.uuid)"
                        >
                            <u-icon class="text-muted" name="edit-pen" />
                            <view class="ml-1"> 修正 </view>
                        </view>
                        <view
                            v-if="!item.is_default"
                            class="flex items-center ml-[20rpx]"
                            @click="dataDel(item.uuid)"
                        >
                            <u-icon class="text-muted" name="trash" />
                            <view class="ml-1"> 删除 </view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
    </z-paging>
    <addPop
        ref="addPopRef"
        v-if="addPopShow"
        @submit="
            () => {
                addPopShow = false
                pagingRef?.reload()
            }
        "
        @close="addPopShow = false"
    ></addPop>
</template>

<script lang="ts" setup>
import { ref, reactive, onMounted, shallowRef, nextTick } from 'vue'
import { itemFileDataList, fileDataDel } from '@/api/kb'
import { onLoad } from '@dcloudio/uni-app'
import addPop from './components/addPop.vue'

const addPopShow = ref(false)
const addPopRef = shallowRef()

const keyword = ref('')

const fileId = ref('')
const KBId = ref('')
const KBPower = ref(1)

const dataList = ref<any[]>([])
const pagingRef = shallowRef()
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [] } = await itemFileDataList({
            page_size: pageSize,
            page_no: pageNo,
            keyword: keyword.value,
            fd_id: fileId.value,
            kb_id: KBId.value
        })

        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

//打开弹框
const openPop = async (uuid: any) => {
    if (KBPower.value == 3) {
        uni.$u.toast('无权限操作！')
        return
    }
    addPopShow.value = true
    await nextTick()
    setTimeout(() => {
        addPopRef.value.open(KBId.value, fileId.value, uuid)
    }, 100)
}
//数据删除
const dataDel = async (uuid: number) => {
    uni.showModal({
        title: '提示',
        content: '请确认是否删除！',
        success: async function (res) {
            if (res.confirm) {
                await fileDataDel({ kb_id: KBId.value, uuids: [uuid] })
                pagingRef.value?.reload()
            } else if (res.cancel) {
                console.log('用户点击取消')
            }
        }
    })
}
const showText = (msg: string) => {
    uni.showModal({
        title: '错误信息',
        content: msg
    })
}
onLoad((option: any) => {
    fileId.value = option.id
    KBId.value = option.kb_id
    KBPower.value = option.kb_power
})
</script>

<style lang="scss">
page {
    @apply bg-white;
}
</style>
