<template>
    <view
        class="h-full flex flex-col items-center justify-center"
        v-if="userInfo.nickname"
    >
        <u-avatar size="140" :src="userInfo.avatar"></u-avatar>
        <view class="my-[30rpx] text-xl">{{ userInfo.nickname }}</view>
        <view class="text-[36rpx]">微信绑定成功</view>
    </view>
</template>

<script setup lang="ts">
import { oaAuthBind } from '@/api/account'
import { ref } from 'vue'
// #ifdef H5
import wechatOa, { UrlScene } from '@/utils/wechat'
// #endif

import { onLoad } from '@dcloudio/uni-app'
import { useRoute } from 'uniapp-router-next'

const userInfo = ref<any>({})
const route = useRoute()
const oaBindWx = async (options: any = { getUrl: true }) => {
    let data = null
    const { code, key = '', getUrl } = options

    if (code) {
        data = await oaAuthBind({
            code,
            key
        })
        userInfo.value = data
    }
    if (getUrl) {
        await wechatOa.getUrl(UrlScene.BIND_WX)
    }
}

onLoad(() => {
    const options = route.query
    const authData = wechatOa.getAuthData()
    if (authData.code && authData.scene == UrlScene.BIND_WX) {
        try {
          oaBindWx({ ...options, ...authData })
        } catch (error) {
        } finally {
            wechatOa.setAuthData()
        }
    } else if (options.is_pc && options.key) {
      oaBindWx()
    }
})
</script>

<style lang="scss">
page {
    height: 100%;
    background-color: #fff;
}
</style>
