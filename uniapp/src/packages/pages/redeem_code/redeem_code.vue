<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="redeem-code p-[24rpx]">
        <view
            class="px-[30rpx] py-[50rpx] bg-white rounded-[16rpx]"
            v-if="redeemCodeConfig.is_open"
        >
            <template v-if="redeemCodeConfig.is_show">
                <view>购买链接</view>
                <view class="flex justify-between pb-6">
                    <view class="line-clamp-1 leading-[40rpx] w-[520rpx]">
                        {{ redeemCodeConfig.buy_site }}
                    </view>
                    <view class="flex-none text-primary"  @click="onCopy(redeemCodeConfig.buy_site)">
                        复制
                    </view>
                </view>
            </template>
            <view class="py-[10rpx] px-[20rpx] bg-[#f5f5f5] rounded">
                <u-input v-model="code" placeholder="请输入卡密编号"></u-input>
            </view>
            <view class="w-full mt-[30px]">
                <button class="entry-btn" :loading="isQuery" @click="queryRedeem">
                    查询
                </button>
            </view>
        </view>
        <view v-else class="py-[400rpx]">
            <u-empty text="功能未开启"></u-empty>
        </view>
        <u-popup v-model="showCheckResult" mode="center" border-radius="24">
            <view class="w-[690rpx] px-[24rpx]">
                <view class="p-[30rpx] text-lg text-center font-medium">
                    查询结果
                </view>
                <view class="min-h-[200rpx] px-[20rpx]">
                    <view class="flex mt-[30rpx]">
                        <text>卡密类型：</text>
                        <text class="ml-[20rpx]">{{
                            checkResult.type_desc
                        }}</text>
                    </view>
                    <view class="flex mt-[30rpx]">
                        <text>卡密面额：</text>
                        <text class="ml-[20rpx]">{{
                            checkResult.content
                        }}</text>
                    </view>
                    <view class="flex mt-[30rpx]">
                        <text class="flex-none">兑换时间：</text>
                        <text class="ml-[20rpx]">{{
                            checkResult.failure_time
                        }}</text>
                    </view>
                    <view class="flex mt-[30rpx]" v-if="checkResult.valid_time">
                        <text class="flex-none">有效期至：</text>
                        <text class="ml-[20rpx]">{{
                                checkResult.valid_time
                            }}</text>
                    </view>


                </view>
                <view class="py-[50rpx] px-[16rpx] bg-white">
                    <button class="entry-btn" :loading="isUse" @click="onUseRedeemCode">
                        立即兑换
                    </button>
                </view>
            </view>
        </u-popup>
        <!--    底部导航    -->
        <tabbar></tabbar>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>
<script setup lang="ts">
import { ref, computed } from 'vue'
import { useCopy } from '@/hooks/useCopy'
import { useAppStore } from '@/stores/app'
import { useLockFn } from '@/hooks/useLockFn'
import type { RedeemCodeResponse } from '@/api/user'
import { checkRedeemCode, useRedeemCode } from '@/api/user'
import FloatingMenu from "@/components/floating-menu/floating-menu.vue";

const appStore = useAppStore()
// 兑换码
const code = ref<string>('')
// 显示查询结果
const showCheckResult = ref<boolean>(false)
// 查询结果
const checkResult = ref<RedeemCodeResponse>({
    content: '',
    failure_time: '',
    id: '',
    sn: '',
    type: '',
    type_desc: '',
    valid_time: ''
})

// 获取卡密信息设置
const redeemCodeConfig = computed(() => appStore.getCardCodeConfig)

const onCopy = (text: string) => {
    const { copy } = useCopy()
    copy(text)
}

const { isLock: isQuery, lockFn: queryRedeem } = useLockFn(async () => {
    try {
        const data = await checkRedeemCode({ sn: code.value })
        showCheckResult.value = true
        checkResult.value = data
    } catch (error) {
        code.value = ''
        console.log('查询卡密失败=>', error)
    }
})

const { isLock: isUse, lockFn: onUseRedeemCode } = useLockFn(async () => {
    try {
        await useRedeemCode({ sn: code.value })
        showCheckResult.value = false
        uni.$u.toast('兑换成功')
        code.value = ''
    } catch (error) {
        console.log('兑换卡密失败=>', error)
    }
})
</script>

<style>
page {
    height: 100%;
}
</style>

<style lang="scss" scoped>
.redeem-code {
    height: 100%;
    background: #f4f8fd;
}

.entry-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 82rpx;
    border-radius: 30px;
    font-weight: 500;
    font-size: 30rpx;
    background: linear-gradient(90deg, #70c3ec 0%, #426df7 100%);
    box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.2);
    @apply text-white;
}
</style>