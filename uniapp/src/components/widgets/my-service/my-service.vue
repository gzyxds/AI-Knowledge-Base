<template>
    <div class="my-service bg-white mx-[20rpx] mb-[20rpx] rounded-lg">
        <template v-if="content.style == 1">
            <div
                class="px-[30rpx] py-[24rpx] text-xl font-bold"
                v-if="content.showTitle"
            >
                {{ content.title }}
            </div>
            <div
                class="flex flex-wrap pt-[20rpx] pb-[0rpx]"
            >
                <div
                    v-for="(item, index) in showList"
                    :key="index"
                    class="flex flex-col items-center w-1/4 mb-[15px]"
                    @click="handleClick(item.link, item.name)"
                >
                    <view class="w-[52rpx] h-[52rpx] relative">
                        <u-image
                            width="52"
                            height="52"
                            :src="getImageUrl(item.image)"
                            alt=""
                        >
                        </u-image>
                        <u-badge
                            v-if="item.link.path === '/packages/pages/notification/notification'"
                            type="error"
                            :offset="[-8, -8]"
                            :count="userStore.userInfo.unread_notice"
                        />
                    </view>
                    <div class= "mt-[7px]">{{ item.name }}</div>
                </div>
            </div>
        </template>
        <template v-if="content.style == 2">
            <div
                class="px-[30rpx] py-[24rpx] text-xl font-bold"
                style="border-bottom: 1px solid var(--color-light, #e5e5e5)"
                v-if="content.showTitle"
            >
                {{ content.title }}
            </div>
            <div>
                <div
                    v-for="(item, index) in showList"
                    :key="index"
                    class="flex items-center nav-item h-[100rpx] px-[24rpx]"
                    @click="handleClick(item.link, item.name)"
                >
                    <view class="w-[48rpx] h-[48rpx] relative">
                        <u-image
                            width="48"
                            height="48"
                            :src="getImageUrl(item.image)"
                            alt=""
                        />
                        <u-badge
                            v-if="item.link.path === '/packages/pages/notification/notification'"
                            type="error"
                            :offset="[-8, -8]"
                            :count="userStore.userInfo.unread_notice"
                        />
                    </view>
                    <div class="ml-[20rpx] flex-1">{{ item.name }}</div>
                    <div class="text-muted">
                        <u-icon name="arrow-right"/>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
<script lang="ts" setup>
import {useAppStore} from '@/stores/app'
import {useUserStore} from '@/stores/user'
import {navigateTo} from '@/utils/navigate'
import {computed} from 'vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})
const {getImageUrl} = useAppStore()
const userStore = useUserStore()

const showList = computed(() => {
    return (
        props.content.data?.filter((item: any) =>
            item.is_show ? item.is_show == '1' : true
        ) || []
    )
})
const handleClick = (link: any, name: string) => {
    link.name = name
    navigateTo(link)
}
</script>

<style lang="scss" scoped>
.nav-item {
    &:not(:last-of-type) {
        @apply border-light border-solid border-0 border-b;
    }
}
</style>
