<template>
    <u-collapse>
        <u-collapse-item open>
            <template #title>
                <view class="flex items-center">
                    <zui-svg-icon width="32rpx" icon="link" :color="'#333'" />
                    <span class="text-2xl ml-1 font-bold"> 信息来源 </span>
                </view>
            </template>
            <view>
                <scroll-view scroll-x class="h-[250rpx]">
                    <view class="whitespace-nowrap inline-block">
                        <view class="flex mx-[-8rpx]">
                            <view
                                class="bg-page w-[500rpx] p-[30rpx] mx-[8rpx] rounded-[12rpx] text-main"
                                :key="item.seeMoreUrl"
                                v-for="(item, index) in list"
                                @click="clickLink(item.seeMoreUrl)"
                            >
                                <text class="font-bold text-sm line-clamp-1">
                                    <text class="text-primary">
                                        【{{ item.index }}】
                                    </text>
                                    {{ item.title }}
                                </text>
                                <text
                                    class="text-xs text-muted mt-[20rpx] line-clamp-2"
                                >
                                    {{ item.snippet }}
                                </text>
                                <div class="flex justify-end items-center mt-2">
                                    <u-image
                                        :src="item.image"
                                        alt=""
                                        width="24"
                                        height="24"
                                        class="flex"
                                    >
                                        <template #error>
                                            <zui-svg-icon
                                                width="24rpx"
                                                icon="link"
                                                :color="'#333'"
                                            />
                                        </template>
                                        <template #loading> </template>
                                    </u-image>
                                    <div class="text-xs text-tx-secondary ml-1">
                                        {{ item.showName }}
                                    </div>
                                </div>
                            </view>
                        </view>
                    </view>
                </scroll-view>
            </view>
        </u-collapse-item>
    </u-collapse>
</template>
<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        list: any[]
    }>(),
    {
        list: () => []
    }
)

const clickLink = (url: string) => {
    window.open(url, '_blank')
}
</script>
