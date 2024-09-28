<template>
    <view class="flex-col flex flex-1 min-h-0">
        <view class="px-[20rpx] pt-[20rpx] pb-[10rpx]">
            <u-search
                v-model="keyword"
                placeholder="请输入关键词搜索"
                height="72"
                bg-color="#ffffff"
                @search="getData()"
                @custom="getData()"
                @clear="getData()"
            />
        </view>
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
        <view class="flex-1 min-h-0">
            <z-paging
                ref="pagingRef"
                v-model="dataList"
                :auto-clean-list-when-reload="false"
                :fixed="false"
                @query="queryList"
            >
                <view class="px-[24rpx] pb-[1rpx]">
                    <view
                        v-for="item in dataList"
                        :key="item.id"
                        class="mb-[20rpx]"
                        @click="openRobot(item)"
                    >
                        <view class="role-item flex items-center p-[20rpx]">
                            <u-image
                                :src="item.image"
                                width="130"
                                height="130"
                                border-radius="20"
                            />
                            <view class="flex-1 min-w-0 ml-[20rpx]">
                                <view
                                    class="font-medium text-lg line-clamp-1"
                                >
                                    {{ item.name }}
                                </view>
                                <view
                                    class="mt-[10rpx] text-sm text-muted line-clamp-1"
                                >
                                    {{ item.describe }}
                                </view>
                                <view
                                    class="mt-[10rpx] text-xs text-muted line-clamp-1"
                                >
                                    <u-icon name="account"></u-icon>
                                    10人使用过
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, watch, shallowRef } from 'vue'
import { useRouter } from 'uniapp-router-next'
import { onShow } from '@dcloudio/uni-app'
import { getRoleCateList, getRoleMobileList } from '@/api/role'
import { useUserStore } from '@/stores/user'
const userStore = useUserStore()
const router = useRouter()
const keyword = ref('')
const category = reactive({
    lists: [] as any[],
    current: 0
})
const getCategoryData = async () => {
    category.lists = await getRoleCateList()
}
const dataList = ref<any[]>([])
const pagingRef = shallowRef()

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [] } = await getRoleMobileList({
            keyword: keyword.value,
            category_id: category.current,
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
    router.navigateTo({
        path: '/packages/pages/dialogue_role/dialogue_role',
        query: {
            id: item.id
        }
    })
}
watch(
    () => category.current,
    () => {
        getData()
    }
)
onShow(async () => {
    await getCategoryData()
    getData()
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
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
}
.role-item {
    @apply bg-white;
    border-radius: 15rpx;
}
</style>
