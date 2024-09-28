<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="app-center">
        <!-- #ifndef H5 -->
        <u-navbar
            title="应用中心"
            :is-back="false"
            :background="{ background: 'none' }"
        ></u-navbar>
        <!-- #endif -->
        <page-status :status="status">
            <view class="header">
                <view class="flex items-center">
                    <image
                        src="@/packages/static/images/app_center/robot.png"
                        mode="widthFix"
                    ></image>
                    <text class="title">{{ appCenterData.mobile_title }}</text>
                </view>
                <view class="flex items-center search-container">
                    <input
                        v-model="searchQuery"
                        class="search flex-1"
                        placeholder="输入您想搜索的应用"
                        placeholderClass="search-placeholder"
                        @input="filterApps"
                    />
                    <view
                        class="flex justify-center items-center w-[80rpx] h-[80rpx]"
                        @click.stop="filterApps"
                    >
                        <u-icon name="search" size="32"></u-icon>
                    </view>
                </view>
            </view>
            <view class="main">
                <view v-if="filteredApps.length" class="grid grid-cols-2" style="gap: 24rpx;">
                    <view
                        class="flex items-center p-[20rpx] bg-white rounded-lg"
                        v-for="(item, index) in filteredApps"
                        :key="index"
                        @click.stop="handleJump(item)"
                    >
                        <view class="flex-none">
                            <u-image :src="getImageUrl(item.image)" width="82" height="82" border-radius="18"/>
                        </view>
                        <view class="ml-[20rpx] py-[20rpx]">
                            <view class="font-medium text-main text-lg line-clamp-1">{{ item.title }}</view>
                            <text class="text-muted text-xs mt-2 line-clamp-1">{{ item.desc }}</text>
                        </view>
                    </view>
                </view>
                <!--        数据为空        -->
                <view class="flex justify-center items-center" style="min-height: 60vh" v-else>
                    <u-empty text="找不到更多应用了～" mode="list"></u-empty>
                </view>
            </view>

            <tabbar/>

            <template #error>
                <view
                    class="flex justify-center items-center"
                    style="height: 80vh; color: #999999"
                >
                    发生错误，请稍后重试
                </view>
            </template>
        </page-status>
    </view>
</template>

<script lang="ts" setup>
import {onLoad, onPullDownRefresh} from "@dcloudio/uni-app";
import {PageStatusEnum} from "@/enums/appEnums";
import {getDecorate} from "@/api/shop";
import {ref} from "vue";
import {useAppStore} from "@/stores/app";
import {navigateTo} from "@/utils/navigate";

const {getImageUrl} = useAppStore()
const status = ref(PageStatusEnum.LOADING)
const appCenterData = ref({
    data: [] as any,
    mobile_title: '',
    pc_background: '',
    pc_text_color: 1,
    pc_title: ''
})
const searchQuery = ref('');
const filteredApps = ref([])

// 过滤出合适的描述或者标题
const filterApps = () => {
    const query = searchQuery.value.trim().toLowerCase();
    if (query === '') {
        filteredApps.value = appCenterData.value.data;
    } else {
        filteredApps.value = appCenterData.value.data.filter((item: any) => {
            const title = item.title.toLowerCase();
            const desc = item.desc.toLowerCase();
            console.log(title.includes(query) || desc.includes(query))
            return title.includes(query) || desc.includes(query);
        });
    }
};

const handleJump = (item: any) => {
    navigateTo(item.link)
}

const getData = async () => {
    try {
        const res = await getDecorate({id: 11})
        const content = JSON.parse(res.data)[0].content
        content.data = content.data.filter((item: any) => item.is_show == 1 && item.link.path) || []
        appCenterData.value = content
        status.value = PageStatusEnum.NORMAL
        filterApps()
    } catch (error) {
        console.error(error)
        status.value = PageStatusEnum.ERROR
    }
}

onLoad(async () => {
    await getData()
})

onPullDownRefresh(async () => {
    try {
        await getData()
    } catch (error) {
    }
    uni.stopPullDownRefresh()
})
</script>

<style>page {
    height: 100%;
}</style>
<style lang="scss" scoped>
.app-center {
    height: 100%;
    background: linear-gradient(180deg, #E0EBFD 0%, rgba(224, 235, 254, 0) 100%);

    .header {
        padding: 0 45rpx;
        padding-top: 52rpx;
        background-image: url("../../static/images/app_center/bg.png");
        background-repeat: no-repeat;
        background-size: 100% auto;

        image {
            width: 186rpx;
            height: 220rpx;
        }

        text {
            font-family: Source Han Sans;
            font-size: 42rpx;
            font-weight: 500;
            margin-left: 20rpx;
            margin-top: 20rpx;
        }

        .search-container {
            position: relative;
            z-index: 10;
            //height: 90rpx;
            margin-top: -20rpx;
            border-radius: 20rpx;
            background-color: #ffffff;
        }

        .search {
            padding: 20rpx 30rpx;
        }

        .search-placeholder {
            font-size: 26rpx;
            font-weight: normal;
            color: #999999;
        }
    }

    .main {
        padding: 0 24rpx;
        padding-top: 60rpx;
    }
}
</style>