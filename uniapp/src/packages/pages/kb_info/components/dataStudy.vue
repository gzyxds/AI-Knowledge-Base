<template>
    <view class="bg-white h-full">
        <z-paging
            ref="pagingRef"
            v-model="dataList"
            :fixed="false"
            height="100%"
            :safe-area-inset-bottom="false"
            :auto-clean-list-when-reload="false"
            @query="queryList"
        >
            <template #top>
                <view class="py-[24rpx] px-[20rpx]">
                    <view class="flex justify-between">
                        <view
                            class="bg-primary py-[20rpx] px-[30rpx] rounded text-white"
                            @click="showFileImportPop"
                        >
                            导入数据
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
                    @click.stop="toItemDetail(item.id)"
                >
                    <view class="p-[30rpx]">
                        <view class="flex items-center">
                            <image
                                v-if="item.is_default"
                                class="w-[32rpx] h-[32rpx] flex-none"
                                src="@/static/images/icon/icon_hands.png"
                            />
                            <image
                                v-else
                                class="w-[32rpx] h-[32rpx] flex-none"
                                :src="getUnitImg(item.name)"
                            />
                            <view class="mx-[10rpx]"> {{ item.name }} </view>
                            <view v-if="item.is_qa == 1" class="flex-none">
                                <u-tag
                                    v-if="item.qa?.status == 0"
                                    type="info"
                                    :text="item.qa?.status_msg"
                                    size="mini"
                                >
                                </u-tag>
                                <u-tag
                                    v-if="item.qa?.status == 1"
                                    type="warning"
                                    :text="item.qa?.status_msg"
                                    size="mini"
                                >
                                </u-tag>
                                <view @click.stop="showText(item.qa?.error)">
                                    <u-tag
                                        v-if="item.qa?.status == 3"
                                        type="error"
                                        :text="item.qa?.status_msg"
                                        size="mini"
                                    >
                                    </u-tag>
                                </view>
                            </view>
                            <u-icon name="arrow-right" />
                        </view>
                        <view class="flex mt-[30rpx]">
                            <view class="flex-1 flex">
                                <view class="text-center">
                                    <view class="text-xl font-medium">
                                        {{ item.wait_sum }}
                                    </view>
                                    <view class="mt-[20rpx]">待训练</view>
                                </view>
                            </view>

                            <view class="flex-1 flex justify-center">
                                <view class="text-center">
                                    <view class="text-xl font-medium">
                                        {{ item.ok_sum }}
                                    </view>
                                    <view class="mt-[20rpx]">已训练</view>
                                </view>
                            </view>
                            <view class="flex-1 flex justify-end">
                                <view class="text-center">
                                    <view class="text-xl font-medium">
                                        {{ item.total_sum }}
                                    </view>
                                    <view class="mt-[20rpx]">数据总量</view>
                                </view>
                            </view>
                        </view>
                    </view>
                    <view
                        class="border-t border-solid border-light border-0 p-[30rpx] flex justify-between items-center"
                    >
                        <view class="text-sm text-muted">{{
                            item.create_time
                        }}</view>
                        <view class="flex items-center">
                            <view
                                class="flex items-center"
                                @click.stop="showRename(item)"
                            >
                                <u-icon class="text-muted" name="edit-pen" />
                                <view class="ml-1"> 重命名 </view>
                            </view>
                            <view
                                v-if="!item.is_default"
                                class="flex items-center ml-[20rpx]"
                                @click.stop="del(item.id)"
                            >
                                <u-icon class="text-muted" name="trash" />
                                <view class="ml-1"> 删除 </view>
                            </view>
                        </view>
                    </view>
                </view>
                <fileImport
                    v-model="fileImportShow"
                    @close="
                        () => {
                            fileImportShow = false
                            pagingRef?.reload()
                        }
                    "
                ></fileImport>
            </view>
        </z-paging>
        <u-modal
            ref="uModalRef"
            v-model="renameState.show"
            title="重命名"
            show-cancel-button
            async-close
            @confirm="rename"
        >
            <view class="slot-content p-[40rpx]">
                <u-input
                    v-model="renameState.name"
                    border
                    placeholder="请输入文件名称"
                />
            </view>
        </u-modal>
    </view>
</template>

<script lang="ts" setup>
import { ref, reactive, computed, shallowRef } from 'vue'
import fileImport from './fileImport.vue'
import { useKB } from '../useKb'
import { fileDataList, fileDel, fileRename } from '@/api/kb'
import { isMiniProgram } from '@/utils/env'
//#ifdef H5
import wechat from '@/utils/wechat'
//#endif
import router from '@/router'

import xlsx from '@/packages/static/images/kb/xlsx.png'
import csv from '@/packages/static/images/kb/csv.png'
import doc from '@/packages/static/images/kb/doc.png'
import pdf from '@/packages/static/images/kb/pdf.png'
import txt from '@/packages/static/images/kb/txt.png'
import markdown from '@/packages/static/images/kb/markdown.png'

const { KBId, KBInfo } = useKB()
const fileImportShow = ref(false)

const keyword = ref()

const dataList = ref<any[]>([])
const pagingRef = shallowRef()

const suffixUnitMap: Record<string, string> = {
    doc: doc,
    pdf: pdf,
    txt: txt,
    xlsx: xlsx,
    csv: csv,
    mark: markdown
}

const getUnitImg = computed(() => {
    return (name: string) => {
        // 截取后缀并匹配相近单词的图标
        const suffix: string | undefined = name.split('.').pop()
        for (const nameKey in suffixUnitMap) {
            if (suffix?.includes(nameKey)) {
                return suffixUnitMap[nameKey]
            }
        }
    }
})

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [] } = await fileDataList({
            page_size: pageSize,
            page_no: pageNo,
            keyword: keyword.value,
            kb_id: KBId.value
        })

        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

//文件导入弹框
const showFileImportPop = () => {
    if (KBInfo.value.power == 3) {
        uni.$u.toast('无权限操作！')
        return
    }
    fileImportShow.value = true
}

//跳转详情
const toItemDetail = (id: number) => {
    const url = `/packages/pages/kb_item/kb_item?id=${id}&kb_id=${KBId.value}&kb_power=${KBInfo.value.power}`
    if (isMiniProgram) {
        wechat.miniProgram.navigateTo({
            url
        })
    } else {
        router.navigateTo(url)
    }
}

const uModalRef = shallowRef()
const renameState = reactive({
    show: false,
    id: 0,
    name: ''
})

const showRename = (item: any) => {
    renameState.id = item.id
    renameState.show = true
    renameState.name = item.name
}
const rename = async () => {
    if (!renameState.name.trim()) {
        uModalRef.value?.clearLoading()
        return uni.$u.toast('请输入文件名称')
    }
    try {
        await fileRename({
            name: renameState.name,
            fd_id: renameState.id
        })
        renameState.show = false
        pagingRef.value?.reload()
    } catch (error) {
        uModalRef.value?.clearLoading()
    }
}
//删除
const del = (fd_id: number) => {
    uni.showModal({
        title: '提示',
        content: '请确认是否删除！',
        success: async function (res) {
            if (res.confirm) {
                await fileDel({ fd_id })
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
</script>

<style lang="scss" scoped></style>
