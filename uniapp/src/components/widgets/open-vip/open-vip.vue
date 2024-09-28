<template>
    <view
        class="open-vip mx-[20rpx] rounded-t-lg"
        :style="{ 'background-image': `url(${getImageUrl(content.bg)})` }"
        v-if="appStore.config?.member_package_open"
    >
<!--         v-if="appStore.getIsShowVip"-->
        <view class="p-[20rpx] flex items-center">
            <u-image
                width="60"
                height="60"
                :src="getImageUrl(content.icon)"
                alt=""
            />
            <view class="flex justify-between w-full text-white">
                <view
                    class="ml-[20rpx] flex flex-col justify-between"
                >
                    <view class="text-lg">
                        <text v-if="user?.package_is_overdue">
                            会员已到期
                        </text>
                        <text v-else>
                            {{ user.package_name ? user.package_name : content.title }}
                        </text>
                    </view>
                    <view class="text-[22rpx] mt-[12rpx]">
                        <text v-if="user?.package_is_overdue">
                            {{ user.package_time ? '到期时间：' + user.package_time : content.sub_title }}
                        </text>
                        <text v-else>
                            {{ user.package_time ? '有效期至：' + user.package_time : content.sub_title }}
                        </text>

                    </view>
                </view>
                <view class="flex flex-col justify-center">
<!--                    appStore.getIsShowVip-->
                    <u-button
                        shape="circle"
                        size="medium"
                        type="primary"
                        :customStyle="{
                            padding: '0 24rpx',
                            height: '56rpx',
                            background: '#ffffff',
                            border: 'none',
                            color: $theme.primaryColor
                        }"
                        hover-class="none"
                        @click="navigateTo('/packages/pages/member_center/member_center')"
                    >
                        <text v-if="user?.package_is_overdue">
                            去开通
                        </text>
                        <text v-else>
                            {{ user.package_time ? '立即续费' : content.btn }}
                        </text>
                    </u-button>
                </view>
            </view>
        </view>
    </view>
</template>
<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { useRouter } from 'uniapp-router-next'
// import { useRouter } from 'uniapp-router-next-zm'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    },
    user: {
        type: Object,
        default: () => ({})
    },
    isLogin: {
        type: Boolean
    }
})
const router = useRouter()
const appStore = useAppStore()
const { getImageUrl } = appStore
const navigateTo = (path: string) => {
    router.navigateTo(path)
    // console.log(path)
    // uni.switchTab({ url: path })
}
</script>

<style lang="scss">
.open-vip {
    background-repeat: no-repeat;
    background-size: cover;
}
</style>
