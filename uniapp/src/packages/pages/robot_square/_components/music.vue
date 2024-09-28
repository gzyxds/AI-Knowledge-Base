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
                        v-for="item in dataList"
                        :key="item.id"
                        class="mb-[30rpx] w-[50%] px-[15rpx]"
                        @click="toMusicDetail(item)"
                    >
                        <view class="music-item h-full">

                            <view class="w-full relative">
                                <u-image
                                    :src="item.image_url"
                                    width="100%"
                                    height="340"
                                    mode="aspectFill"
                                    border-radius="15"
                                />

                                <view class="absolute left-[16rpx] bottom-[16rpx]">
                                    <view
                                        class="bg-[rgba(0,0,0,0.5)] inline-flex text-white p-[10rpx] rounded-full text-xs items-center"
                                    >
                                        <image
                                            src="@/static/images/icon/icon_music.png"
                                            class="w-[24rpx] h-[24rpx]"
                                        />
                                        <text class="ml-[6rpx]">
                                            {{ item.duration }}
                                        </text>
                                    </view>
                                </view>

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
                            <view class="mt-1 font-medium line-clamp-1">
                                {{ item?.title }}
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
                </view>
            </z-paging>
        </view>
    </view>
</template>
<script setup lang="ts">
import {onMounted, reactive, ref, shallowRef, watch} from 'vue'
import {useRouter} from 'uniapp-router-next'
import {getMusicSquare, musicSquareCollect} from '@/api/square'
import {getSquareCategory} from '@/api/task_reward'
import {useUserStore} from '@/stores/user'
import {watchDebounced} from '@vueuse/core'
import {onShow} from "@dcloudio/uni-app";
import config from '@/config'
const domain = config.baseUrl

const props = defineProps<{ keyword: string }>()

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

const getCategoryData = async () => {
    category.lists = await getSquareCategory({
        type: 2
    })
    category.lists.unshift({name: '全部', id: ''})
}
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const {lists = []} = await getMusicSquare({
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

const getData = () => {
    pagingRef.value?.refresh()
}

const handlePraise = async (value: any) => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    await musicSquareCollect({
        records_id: value.id,
        status: value.is_collect ? 0 : 1
    })
    // 分类ID是0说明是当前在喜欢页
    if (category.current === 0) {
        getData()
    } else {
        value.is_collect = value.is_collect ? 0 : 1
    }
}

const toMusicDetail = async (item: any) => {
    if (!userStore.isLogin) router.navigateTo('/pages/login/login')

    router.navigateTo({
        path: '/packages/pages/music_player/music_player',
        query: {
            id: item.id,
            mode: 'square'
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
        immediate: false
    }
)

getCategoryData()
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

.music-item {
    border-radius: 15rpx;
    display: flex;
    flex-direction: column;

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
}
</style>