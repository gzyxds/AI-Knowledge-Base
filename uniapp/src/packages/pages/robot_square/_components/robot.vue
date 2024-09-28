<template>
    <view class="flex flex-col min-h-0 h-full">
        <scroll-view scroll-x class="whitespace-nowrap">
            <view class="inline-flex py-[20rpx] px-[10rpx]">
                <view
                    class="category-item mx-[10rpx]"
                    :class="{
                        'category-item-active': item.id == category.current
                    }"
                    v-for="item in category.lists"
                    :key="item.id"
                    @click="category.current = item.id"
                >
                    {{ item.name }}
                </view>
            </view>
        </scroll-view>
        <view class="h-full">
            <z-paging
                ref="pagingRef"
                v-model="dataList"
                :fixed="false"
                :auto-clean-list-when-reload="false"
                @query="queryList"
            >
                <view class="px-[20rpx] flex flex-wrap mx-[-15rpx]">
                    <view
                        v-for="item in dataList"
                        :key="item.id"
                        class="mb-[30rpx] w-[50%] px-[15rpx]"
                        @click="openRobot(item)"
                    >
                        <view class="robot-item h-full">
                            <view class="flex items-center">
                                <u-image
                                    :src="item.image"
                                    width="80"
                                    height="80"
                                    shape="circle"
                                />
                                <view class="flex-1 min-w-0 ml-[16rpx]">
                                    <view class="text-bold line-clamp-1">
                                        {{ item.name }}
                                    </view>
                                    <view
                                        v-if="item?.author"
                                        class="text-xs text-muted line-clamp-1"
                                    >
                                        {{ item.author }}
                                    </view>
                                </view>
                            </view>
                            <view class="mt-[20rpx] text-muted flex-1 line-clamp-3">
                                {{ item.intro }}
                            </view>
                            <view class="entry-btn">开始对话</view>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>
    </view>
</template>
<script setup lang="ts">
import {onMounted, reactive, ref, shallowRef, watch} from 'vue'
import {useRouter} from 'uniapp-router-next'
import {getRobotCategory, getRobotSquare, putRobotRecord} from '@/api/robot'
import {useUserStore} from '@/stores/user'
import {watchDebounced} from '@vueuse/core'

const props = defineProps<{ keyword: string }>()

const router = useRouter()
const userStore = useUserStore()

const category = reactive({
    lists: [] as any[],
    current: 0
})
const getCategoryData = async () => {
    category.lists = await getRobotCategory()
    category.lists.unshift({
        name: '全部',
        id: 0
    })
}
const dataList = ref<any[]>([])
const pagingRef = shallowRef()
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const {lists = []} = await getRobotSquare({
            keyword: props.keyword,
            cid: category.current,
            page_size: pageSize,
            page_no: pageNo
        })
        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const getData = () => {
    pagingRef.value?.reload()
}

const openRobot = async (item: any) => {
    if (!userStore.isLogin) router.navigateTo('/pages/login/login')
    const {id} = await putRobotRecord({
        id: item.id
    })
    router.navigateTo({
        path: '/packages/pages/square_chat/square_chat',
        query: {
            id
        }
    })
}

watch(
    () => category.current,
    () => {
        getData()
    }
)

watchDebounced(
    () => props.keyword,
    (value) => {
        getData()
    },
    {
        debounce: 500
    }
)

onMounted(async () => {
    await getCategoryData()
})
</script>

<style lang="scss" scoped>
.category-item {
    border-radius: 10rpx;
    background: #fff;
    box-shadow: 0 3px 10px #ebeefd;
    padding: 14rpx 40rpx;
}
.category-item-active {
    color: #ffffff;
    background: linear-gradient(90deg, #54C6EE 0%, #3C5EFD 100%);
}
.robot-item {
    border-radius: 15rpx;
    background: #ffffff;
    border: 1px solid rgba(67, 111, 246, 0.3);
    padding: 30rpx;
    display: flex;
    flex-direction: column;
    .entry-btn {
        border-radius: 30px;
        background: linear-gradient(90deg, #70c3ec 0%, #426df7 100%);
        box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.2);
        padding: 10rpx;
        margin-top: 30rpx;
        text-align: center;
        @apply text-white;
    }
}
</style>