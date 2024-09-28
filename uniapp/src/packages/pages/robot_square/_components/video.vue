<template>
    <view class="flex flex-col min-h-0 h-full">
        <scroll-view scroll-x class="whitespace-nowrap">
            <view class="inline-flex py-[20rpx] px-[10rpx]">
                <view
                    class="category-item mx-[10rpx]"
                    :class="{
                        'category-item-active': item.id === category.current
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
                auto-show-back-to-top
                v-model="dataList"
                :fixed="false"
                :auto-clean-list-when-reload="true"
                :auto-scroll-to-top-when-reload="false"
                @query="queryList"
            >
                <view class="px-[20rpx] flex flex-wrap mx-[-15rpx]">
                    <view
                        v-for="(item, index) in dataList"
                        :key="index"
                        class="mb-[30rpx] w-[50%] px-[15rpx] relative"
                        @click.stop="toDetail(item)"
                    >
                        <view class="h-[340rpx] relative">
                            <video
                                class="w-full h-full rounded-[10rpx] overflow-hidden pointer-events-none"
                                preload
                                initial-time="0"
                                playsinline
                                webkit-playsinline
                                :show-center-play-btn="false"
                                :controls="false"
                                x-webkit-airplay="allow"
                                x5-video-player-fullscreen="true"
                                x5-video-player-type="h5"
                                :src="item.video_url"
                            />

                            <view
                                class="praise right-[16rpx] bottom-[16rpx]"
                                style="background-color: rgba(0, 0, 0, 0.5)"
                                @click.stop="handlePraise(item)"
                            >
                                <view
                                    class="praise-animate"
                                    :style="{
                                        backgroundImage: `url(${domain}resource/image/api/default/praise.png)`
                                    }"
                                    :class="item.is_collect ? 'praise-entry':'praise-leave'"
                                >
                                </view>
                            </view>
                        </view>
                        <!--    描述词    -->
                        <view class="mt-2 font-medium line-clamp-1">
                            {{ item.prompt }}
                        </view>
                        <!--    用户信息    -->
                        <view class="flex items-center mt-2" v-if="item?.user_info?.name">
                            <view class="flex-none">
                                <u-image
                                    :src="item.user_info.image"
                                    width="40" height="40"
                                    border-radius="50"
                                />
                            </view>
                            <view class="ml-2 text-xs line-clamp-1">{{ item.user_info.name }}</view>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>
    </view>
</template>
<script setup lang="ts">
import {reactive, ref, shallowRef, watch} from 'vue'
import {useRouter} from 'uniapp-router-next'
import {getVideoSquare, videoSquareCollect} from '@/api/square'
import {getSquareCategory} from '@/api/task_reward'
import {useUserStore} from '@/stores/user'
import {watchDebounced} from '@vueuse/core'
import {useCopy} from '@/hooks/useCopy'
import config from '@/config'
const domain = config.baseUrl

const props = defineProps<{ keyword: string }>()

const {copy} = useCopy()
const router = useRouter()
const userStore = useUserStore()

const dataList = ref<any[]>([])
const pagingRef = shallowRef()

const category = reactive<{
    lists: any[],
    current: number | string
}>({
    lists: [],
    current: ''
})

const videoUrl = ref<string>('')
const showDownload = ref<boolean>(false)

const getCategoryData = async () => {
    category.lists = await getSquareCategory({
        type: 3
    })
    category.lists.unshift({name: '全部', id: ''})
}

const getData = () => {
    pagingRef.value?.refresh()
}

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const {lists = []} = await getVideoSquare({
            keyword: props.keyword,
            category_id: category.current,
            page_size: pageSize,
            page_no: pageNo
        })
        pagingRef.value?.complete(lists)
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const handlePraise = async (value: any) => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    try {
        await videoSquareCollect({
            records_id: value.id,
            status: value.is_collect ? 0 : 1
        })
        // 分类ID是0说明是当前在喜欢页
        if (category.current === 0) {
            getData()
        } else {
            value.is_collect = value.is_collect ? 0 : 1
        }
    } catch (e) {
        console.error(e)
        uni.$u.toast(JSON.stringify(e))
    }
}


const copyLink = async () => {
    await copy(videoUrl.value)
    showDownload.value = false
}

const toDetail = async (item: any) => {
    // if (!userStore.isLogin) {
    //     router.navigateTo('/pages/login/login')
    //     return
    // }

    router.navigateTo({
        path: '/packages/pages/video_detail/video_detail',
        query: {
            id: item.id,
            collectId: item.id
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
        debounce: 500,
        immediate: false,
    }
)

getCategoryData()
</script>

<style lang="scss" scoped>
.category-item {
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 10rpx;
    background: #fff;
    height: 68rpx;
    box-shadow: 0 3px 10px #ebeefd;
    padding: 14rpx 40rpx;
}

.category-item-active {
    color: #ffffff;
    background: linear-gradient(90deg, #54C6EE 0%, #3C5EFD 100%);
}


.praise {
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 48rpx;
    height: 48rpx;
    border-radius: 30px;
}

.praise-animate {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 38px;
    height: 38px;
    background-repeat: no-repeat;
    background-position: left;
    background-size: cover;
}

// 没点赞
.praise-leave {
    background-position: left;
}

// 点赞
.praise-entry {
    background-position: right;
    transition: background 1s steps(28);
}
</style>