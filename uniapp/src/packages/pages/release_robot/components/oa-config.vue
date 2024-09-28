<template>
    <u-popup
        v-model="showModel"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
        z-index="999"
    >
        <view class="p-[20rpx]">
            <view class="text-xl font-medium">添加菜单</view>
            <view class="mt-[30rpx]">
                <view
                    >1.进入微信<span class="text-success"
                        >公众号后台</span
                    ></view
                >
                <view class="text-[#999] mt-[10rpx]">
                    <view>请确保您的公众号已过微信认证</view>
                    <view>
                        <view>路径：内容与互动 > 自定义菜单 > 添加菜单</view>

                        <view
                            class="text-primary mt-[10rpx]"
                            @click="previewImage(wxoaConfgMenuImg)"
                        >
                            查看填写示例
                        </view>
                    </view>
                </view>
            </view>
            <view class="mt-[30rpx]">
                <view>2.创建菜单</view>
                <view class="text-[#999] mt-2">
                    <view>填写菜单名称，将以下链接或二维码，配置到菜单里</view>
                </view>
                <view class="mt-[10rpx]">
                    <view>{{ chatLink }}</view>
                    <view
                        class="text-primary mt-[10rpx]"
                        @click="copy(chatLink)"
                    >
                        复制链接
                    </view>
                </view>
            </view>

            <view class="text-xl font-medium mt-[30rpx]"> 自动回复 </view>
            <view class="mt-[30rpx]">
                <view
                    >1.进入微信<span class="text-success"
                        >公众号后台</span
                    ></view
                >
                <view class="text-[#999] mt-[10rpx]">
                    <view>
                        <view>路径：内容与互动 > 自动回复 > 收到消息回复</view>

                        <view
                            class="text-primary mt-[10rpx]"
                            @click="previewImage(wxoaConfgReplyImg)"
                        >
                            查看填写示例
                        </view>
                    </view>
                </view>
            </view>
            <view class="mt-[25rpx]">
                <view>2.创建自动回复</view>
                <view class="text-[#999] mt-[10rpx]">
                    <view>
                        选择自动回复类型，将以下链接或二维码，配置到回复里
                    </view>
                </view>
                <view class="mt-[10rpx]">
                    <view>{{ chatLink }}</view>
                    <view
                        class="text-primary mt-[10rpx]"
                        @click="copy(chatLink)"
                    >
                        复制链接
                    </view>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import wxoaConfgMenuImg from '@/packages/static/images/wxoa_config_menu.png'
import wxoaConfgReplyImg from '@/packages/static/images/wxoa_config_autoreply.png'
import { computed } from 'vue'
import { useVModels } from '@vueuse/core'
import config from '@/config'
import { useCopy } from '@/hooks/useCopy'
const props = defineProps<{
    apikey: string | number
    show: boolean
}>()
const emit = defineEmits<{
    (event: 'confirm', value: any): void
    (event: 'update:show', value: boolean): void
}>()
const { show: showModel } = useVModels(props, emit)
const chatLink = computed(() => {
    let origin = config.baseUrl
    //#ifdef H5
    origin = `${location.origin}/`
    //#endif
    const link = `${origin}chat/${props.apikey}`
    return link
})

const { copy } = useCopy()

const previewImage = (url: string) => {
    uni.previewImage({
        urls: [url]
    })
}
</script>

<style scoped lang="scss"></style>
