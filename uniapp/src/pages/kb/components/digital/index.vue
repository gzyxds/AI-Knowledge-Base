<template>
    <view class="content">
        <!--  :enable-back-to-top="currentIndex===tabIndex" 在微信小程序上可以多加这一句，因为默认是允许点击返回顶部的，但是这个页面有多个scroll-view，会全部返回顶部，所以需要控制是当前index才允许点击返回顶部 -->
        <!-- 如果当前页已经加载过数据或者当前切换到的tab是当前页，才展示当前页数据（懒加载） -->
        <z-paging
            v-if="firstLoaded || isCurrentPage"
            ref="pagingRef"
            v-model="dataList"
            :auto-clean-list-when-reload="false"
            @query="queryList"
            :fixed="false"
        >
            <!-- 如果希望其他view跟着页面滚动，可以放在z-paging标签内 -->
            <view class="px-[20rpx] pt-[30rpx]">
                <view class="flex flex-wrap mx-[-15rpx]">
                    <view
                        class="item w-[50%] px-[15rpx] mb-[30rpx]"
                        v-for="item in dataList"
                        :key="item.id"
                        @click="toInfo(item)"
                    >
                        <view class="digital-item">
                            <view
                                class="flex text-center items-center justify-center text-white absolute inset-0 bg-[rgba(0,0,0,0.5)] z-[100] text-xs"
                                v-if="item.is_disable"
                            >
                                <view>
                                    该形象涉权违规，已禁用
                                    <br />
                                    请重新创建形象
                                </view>
                            </view>
                            <u-image
                                :src="item.image"
                                width="100%"
                                height="256rpx"
                                mode="aspectFill"
                            />
                            <view class="digital-info">
                                <view class="flex items-center">
                                    <u-avatar
                                        :size="70"
                                        :src="item.avatar"
                                    ></u-avatar>
                                    <view
                                        class="ml-[8rpx] line-clamp-1 flex-1 min-w-0 text-white"
                                    >
                                        {{ item.name }}
                                    </view>
                                    <view
                                        class="p-[6rpx] bg-white rounded-[50%] flex items-center justify-center text-primary"
                                    >
                                        <u-icon name="arrow-right" :size="20" />
                                    </view>
                                </view>
                            </view>
                            <view
                                class="absolute z-[10000] right-[20rpx] top-[20rpx] bg-white rounded-[50%] w-[40rpx] h-[40rpx] flex items-center justify-center"
                                @click.stop="handleDelete(item.id)"
                            >
                                <u-icon name="trash" />
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </z-paging>
        <AddBtn @click="handelShowAdd" />
        <AddPopup v-model="showAdd" @update="pagingRef.reload()" />
    </view>
</template>

<script setup lang="ts">
import { watch, shallowRef, ref, nextTick } from 'vue'
import AddBtn from '../add-btn.vue'
import AddPopup from './add-popup.vue'
import { useRouter } from 'uniapp-router-next'
import { useUserStore } from '@/stores/user'
import { getDigitalList, delDigital } from '@/api/digital'
const props = defineProps<{
    tabIndex: number
    currentIndex: number
}>()

const userStore = useUserStore()

const dataList = ref<any[]>([])
const pagingRef = shallowRef()
const firstLoaded = ref(false)
const isCurrentPage = ref(false)
const showAdd = ref(false)

const handleDelete = async (id: number) => {
    const { cancel } = await uni.showModal({
        title: '温馨提示',
        content: '确定删除？'
    })
    if (cancel) return
    await delDigital({ id })
    const index = dataList.value.findIndex((item) => item.id == id)
    dataList.value.splice(index, 1)
}

const router = useRouter()
const toInfo = (item: any) => {
    if (item.is_disable) return
    router.navigateTo({
        path: '/packages/pages/digital_setting/digital_setting',
        query: {
            id: item.id
        }
    })
}
const handelShowAdd = async () => {
    if (!userStore.isLogin) return router.navigateTo('/pages/login/login')

    showAdd.value = true
}
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [] } = await getDigitalList({
            page_size: pageSize,
            page_no: pageNo
        })

        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const reload = () => {
    nextTick(() => {
        // 刷新列表数据(如果不希望列表pageNo被重置可以用refresh代替reload方法)
        pagingRef.value && pagingRef.value.refresh()
    })
}

watch(
    () => props.currentIndex,
    (newVal) => {
        if (newVal === props.tabIndex) {
            // 懒加载，当滑动到当前的item时，才去加载
            if (!firstLoaded.value) {
                // 这里需要延迟渲染z-paging的原因是为了避免在一些平台上立即渲染可能引发的底层报错问题
                nextTick(() => {
                    setTimeout(() => {
                        isCurrentPage.value = true
                    }, 100)
                })
            }
        }
    },
    {
        immediate: true
    }
)

defineExpose({
    reload
})
</script>

<style lang="scss">
/* 注意:父节点需要固定高度，z-paging的height:100%才会生效 */
.content {
    height: 100%;
}

.digital-item {
    border-radius: 16rpx;
    background: #fff;
    box-shadow: 0 0 16rpx #3c5efd0f;
    overflow: hidden;
    box-shadow: 0px 4px 38px 0px rgba(0, 0, 0, 0.15);
    position: relative;
    .digital-info {
        border-radius: 49px;
        background: linear-gradient(
            90deg,
            rgba(84, 198, 238, 0.8) 0%,
            rgba(60, 94, 253, 0.8) 100%
        );
        box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.25);
        padding: 10rpx 15rpx;
        margin: 0 20rpx;
        transform: translateY(-50%);
    }
}
</style>
