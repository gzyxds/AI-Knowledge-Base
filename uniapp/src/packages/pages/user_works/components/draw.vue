<template>
    <view class="nav flex px-4 pb-2">
        <view class="category-list flex-1">
            <view
                v-for="item in cateLists"
                :key="item.type"
                class="category-item"
                :class="{
                    'category-item--active': item.type === queryParams.model
                }"
                @click="changeCategory(item.type)"
            >
                {{ item.name }}
            </view>
        </view>
    </view>
    <z-paging
        ref="pagingRef"
        v-model="dataList"
        :auto-clean-list-when-reload="false"
        :safe-area-inset-bottom="true"
        @query="queryList"
        :fixed="false"
        height="100%"
    >
        <view class="w-full h-full px-[20rpx]">
            <view
                v-for="item in dataList"
                class="inline-block w-[50%] px-[10rpx] mb-[20rpx]"
                :key="item.id"
                @click.stop="handleTouch(item.id)"
            >
                <view
                    class="relative rounded-[15rpx] overflow-hidden"
                    :class="{
                        'border-[2px] !border-primary border-solid': isBatch && selectId.includes(item.id)
                    }"
                    style="border: 2px solid transparent"
                    @click="toDetail(item)"
                >
                    <view class="w-full h-full">
                        <u-image
                            :src="item.image"
                            width="100%"
                            height="340"
                        />
                    </view>
                    <view class="absolute right-0 top-[20rpx]" v-if="isBatch">
                        <u-checkbox
                            :model-value="selectId.includes(item.id)"
                            shape="circle"
                        >
                        </u-checkbox>
                    </view>
                </view>
            </view>
        </view>
        <template #bottom>
            <view
                v-if="isBatch"
                class="h-[120rpx] flex items-center px-[20rpx] rounded-t-[25rpx] bg-white shadow-light"
            >
                <u-checkbox v-model="isSelectAll" shape="circle">
                    全选
                </u-checkbox>
                <view
                    >已选<text class="text-error">{{ selectId.length }}</text
                    >/{{ dataList.length }}</view
                >
                <view class="ml-auto">
                    <u-button
                        size="medium"
                        type="error"
                        :disabled="!selectId.length"
                        @click="removeDraw(selectId)"
                    >
                        删除
                    </u-button>
                </view>
            </view>
        </template>
    </z-paging>

    <u-popup
        v-model="actionState.show"
        mode="center"
        border-radius="15"
        closeable
    >
        <view class="w-[650rpx] p-[40rpx]">
            <view class="text-lg font-bold text-center mb-[40rpx]">
                下载图片
            </view>
            <view class="mb-[40rpx]">
                当前环境不支持下载，请复制链接到浏览器打开下载，或长按图片选择保存
            </view>

            <u-button type="primary" shape="circle" @click="copyLink">
                复制链接
            </u-button>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { computed, reactive, ref, shallowRef, watch, onUnmounted } from 'vue'
import { drawingRecord, drawingDelete, DrawRecordItem } from '@/api/draw'
import { useRouter } from 'uniapp-router-next'
import { isWeixinClient } from '@/utils/client'
import { useCopy } from '@/hooks/useCopy'
import { downloadFile } from '@/utils/download'

interface QueryParamsType {
    status: number
    page_no?: number
    page_size?: number
    model: string
}

const props = defineProps<{
    isBatch: boolean
}>()
const router = useRouter()
const pagingRef = shallowRef()
const { copy } = useCopy()
const dataList = ref<any[]>([])
const selectId = ref<number[]>([])
const queryParams = reactive<QueryParamsType>({
    status: 3,
    model: 'sd'
})
const cateLists = ref<any[]>([
    { name: 'SD绘画', type: 'sd' },
    { name: 'DALLE绘画', type: 'dalle3' },
    { name: 'MJ绘画', type: 'mj_goapi' }
])
const isSelectAll = computed({
    get() {
        return selectId.value.length === dataList.value.length
    },
    set() {
        if (selectId.value.length === dataList.value.length) {
            selectId.value = []
        } else {
            selectId.value = dataList.value.map((item) => item.id)
        }
    }
})

watch(
    () => props.isBatch,
    () => {
        selectId.value = []
    }
)

const toDetail = (item: DrawRecordItem) =>{
    if ( props.isBatch ) return
    router.navigateTo({
        path: '/packages/pages/draw_detail/draw_detail',
        query: {
            id: item.id
        }
    })
}

const handleTouch = (id: number) => {
    if (props.isBatch) {
        const index = selectId.value.findIndex((item) => item === id)
        if (index > -1) {
            selectId.value.splice(index, 1)
        } else {
            selectId.value.push(id)
        }
        return
    }
}

const removeDraw = async (ids:  number[]) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await drawingDelete({ ids: ids })
    selectId.value = []
    refresh()
}

const actionState = reactive({
    show: false,
    item: {} as any
})
const copyLink = async () => {
    await copy(actionState.item.video_url)
    actionState.show = false
}

const downloadDraw = (item: any) => {
    if (isWeixinClient()) {
        actionState.show = true
        actionState.item = item
    } else {
        downloadFile(item.image, '图片', 'image')
    }
}

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await drawingRecord({
            page_no: pageNo,
            page_size: pageSize,
            ...queryParams
        } as QueryParamsType)

        pagingRef.value.complete(lists)
    } catch (error) {
        pagingRef.value.complete(false)
    } finally {
    }
}

const changeCategory = (type: string) => {
    if (queryParams.model === type) return
    queryParams.model = type
    refresh()
}

const refresh = () => {
    pagingRef.value?.refresh()
}
</script>

<style lang="scss" scoped>
.category-list {
    .category-item {
        display: inline-block !important;
        line-height: 30px;
        border-radius: 6px;
        box-shadow: 0 2px 6px #ebeefd;
        text-align: center;
        height: 30px;
        padding: 0 18px;
        margin-right: 10px;
        cursor: pointer;
        background-color: white;
        @apply line-clamp-1 text-main;
        &--active {
            box-shadow: 0 3px 6px;
            @apply bg-primary text-white shadow-light;
        }
    }
}
</style>