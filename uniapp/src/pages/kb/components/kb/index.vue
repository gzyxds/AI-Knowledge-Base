<template>
    <view class="content">
        <z-paging
            ref="pagingRef"
            v-model="dataList"
            :auto-clean-list-when-reload="false"
            @query="queryList"
            :fixed="false"
        >
            <template #top>
                <view class="py-[20rpx] px-[20rpx]">
                    <view class="bg-white p-[8rpx] flex rounded-[8rpx]">
                        <view
                            v-for="item in tabState.list"
                            :key="item.type"
                            class="px-[9rpx] py-[14rpx] rounded-[8rpx] flex-1 text-center"
                            :class="{
                                'tab-active': item.type == tabState.current
                            }"
                            @click="tabsChange(item.type)"
                        >
                            {{ item.name }}
                        </view>
                    </view>
                </view>
            </template>
            <view class="grid grid-cols-2 gap-2 px-[20rpx]">
                <view
                    class="bg-white rounded-lg overflow-hidden"
                    v-for="item in dataList"
                    :key="item.id"
                    @click="toDetail(item.id)"
                >
                    <view class="flex relative">
                        <u-image
                            class="w-full"
                            :src="item.image"
                            width="100%"
                            height="272rpx"
                        ></u-image>
                        <view
                            class="bg-[rgba(0,0,0,0.4)] text-white absolute px-[10rpx] py-[2rpx] right-[10rpx] bottom-[10rpx] rounded-[6rpx] flex items-center"
                        >
                            <u-icon name="man-add" />
                            <view class="ml-1 text-sm">
                                {{ item.team_people }}
                            </view>
                        </view>
                    </view>

                    <view class="p-[20rpx]">
                        <view class="text-xl font-bold">{{ item?.name }}</view>
                        <view
                            class="text-sm text-info mt-[24rpx] line-clamp-3"
                            >{{ item?.intro || '这个知识库还没有介绍~' }}</view
                        >
                    </view>
                </view>
            </view>
        </z-paging>
        <addBtn @click="handelShowAdd"></addBtn>
        <addPopup
            v-model="showAdd"
            @close="
                () => {
                    showAdd = false
                    reload()
                }
            "
        />
    </view>
</template>

<script lang="ts" setup>
import addBtn from '../add-btn.vue'
import addPopup from './add-popup.vue'
import { getKBList } from '@/api/kb'
import { nextTick, reactive, ref, shallowRef } from 'vue'
import { useRouter } from 'uniapp-router-next'
import { useUserStore } from '@/stores/user'
const userStore = useUserStore()
const props = defineProps<{
    tabIndex: number
    currentIndex: number
}>()

const tabState = reactive({
    list: [
        {
            name: '全部知识库',
            type: 0
        },
        {
            name: '我的知识库',
            type: 1
        },
        {
            name: '共享给我',
            type: 2
        }
    ],
    current: 0
})

const tabsChange = (type: number) => {
    tabState.current = type
    reload()
}

const showAdd = ref(false)

const dataList = ref()

const pagingRef = shallowRef()

const router = useRouter()

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [] } = await getKBList({
            page_size: pageSize,
            page_no: pageNo,
            type: tabState.current
        })

        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const toDetail = (id: number) => {
    router.navigateTo({
        path: `/packages/pages/kb_info/kb_info?id=${id}`
    })
}

const handelShowAdd = async () => {
    if (!userStore.isLogin) return router.navigateTo('/pages/login/login')

    showAdd.value = true
}

const reload = () => {
    nextTick(() => {
        // 刷新列表数据(如果不希望列表pageNo被重置可以用refresh代替reload方法)
        pagingRef.value && pagingRef.value.refresh()
    })
}
</script>

<style lang="scss" scoped>
/* 注意:父节点需要固定高度，z-paging的height:100%才会生效 */
.content {
    height: 100%;
}

.tab-active {
    background: linear-gradient(
        90deg,
        var(--color-minor) 0%,
        var(--color-primary) 100%
    );
    @apply text-white;
}
</style>
