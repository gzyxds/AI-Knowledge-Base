<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="buy-list">
        <view class="buy-list__header bg-white">
            <u-tabs
                :list="state.tabs"
                :is-scroll="false"
                :current="state.current"
                :active-color="$theme.primaryColor"
                @change="handleChange"
            ></u-tabs>
        </view>

        <view class="buy-list__main">
            <z-paging
                ref="paging"
                v-model="state.lists"
                :fixed="false"
                height="100%"
                empty-view-text="暂无记录～"
                :empty-view-img="EmptyWithdrawImage"
                :empty-view-style="{ 'margin-top': '100px' }"
                :empty-view-center="false"
                :auto-clean-list-when-reload="false"
                :auto-scroll-to-top-when-reload="false"
                :empty-view-img-style="{ width: '360rpx', height: '360rpx' }"
                @query="queryList"
            >
                <view class="">
                    <detailPop
                        v-for="item in state.lists"
                        :key="item.id"
                        :type="state.current"
                        :detail-data="item"
                    >
                        <view
                            class="bg-white flex items-center border-light rounded-lg mt-2 mx-[20rpx] px-[26rpx] py-[24rpx]"
                        >
                            <image
                                class="w-[80rpx] h-[80rpx]"
                                :src="state.current === 1 ? rechargeIcon : memberIcon "
                            ></image>


                            <!--        会员记录        -->
                            <view
                                v-if="state.current === 0"
                                class="flex-1 ml-[20rpx] flex flex-col justify-between"
                            >
                                <view class="flex justify-between">
                                    <view class="text-lg font-bold">
                                        {{ item.name || item.package_name }}
                                    </view>
                                    <view>{{ item.original_package_long_time }}</view>
                                </view>
                                <view v-if="item.pay_way_text" class="text-xs text-info mt-1">
                                    {{ item.pay_way_text }} | {{ item.pay_time_text || item.create_time }}
                                </view>
                                <view v-else class="text-xs text-info mt-1">
                                    {{ item.channel_text }} | {{ item.pay_time || item.create_time }}
                                </view>
<!--                                <view  class="text-xs text-info mt-1">-->
<!--                                    有效期至：{{ item.package_long_time }}-->
<!--                                </view>-->
                            </view>

                            <!--        充值套餐记录        -->
                            <view
                                v-else
                                class="flex-1 ml-[20rpx] flex flex-col justify-between"
                            >
                                <view class="flex justify-between">
                                    <view class="text-lg font-bold">
                                        {{ item.name }}
                                    </view>
                                    <view>{{ item.channel == 2 ? '-' : '￥' + item.order_amount }}</view>
                                </view>
                                <view v-if="item.pay_way_text && item.channel != 2" class="text-xs text-info mt-1">
                                    {{ item.pay_way_text }} | {{ item.pay_time || item.pay_time_text || item.create_time }}
                                </view>
                                <view v-else class="text-xs text-info mt-1">
                                    {{ item.channel_text }} | {{ item.pay_time == '-' ? item.create_time : item.pay_time }}
                                </view>
                            </view>
                        </view>
                    </detailPop>
                </view>
            </z-paging>
        </view>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <!-- <floating-menu></floating-menu> -->
    <!-- #endif -->
</template>

<script lang="ts" setup>
import {reactive, ref, shallowRef} from 'vue'
import {rechargeRecord} from '@/api/recharge'
import {memberPackageBuyLog} from '@/api/member'
import rechargeIcon from '@/packages/static/images/recharge.png'
import memberIcon from '@/packages/static/images/vip-icon.png'
import detailPop from './components/detailPop.vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
import EmptyWithdrawImage from "@/packages/static/empty/withdraw.png";
import {onLoad} from "@dcloudio/uni-app";

const state = reactive({
    current: 0,
    tabs: [
        {
            name: '会员开通记录',
            type: 1
        },
        {
            name: '充值记录',
            type: 2
        }
    ],
    lists: []
})

const paging = shallowRef()

const handleChange = (index: number) => {
    state.current = index
    paging.value.reload()
}

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        if (state.tabs[state.current].type == 1) {
            const data = await memberPackageBuyLog()
            paging.value.complete(data)
        } else {
            const data = await rechargeRecord({
                page_no: pageNo,
                page_size: pageSize
            })
            paging.value.complete(data.lists)
        }
    } catch (error) {
        paging.value.complete(false)
    }
}
</script>

<style lang="scss" scoped>
.buy-list {
    &__header {
        overflow: hidden;
        margin: 20rpx;
        border-radius: 14rpx;
    }

    &__main {
        height: calc(100vh - 40px - env(safe-area-inset-bottom));

        .list {
            &__item {
                border-bottom: 1px solid #e5e5e5;
            }

            &__item:last-child {
                border-bottom: none;
            }
        }
    }
}
</style>
