<template>
    <view class="h-full">
        <scroll-view class="h-full" scroll-y>
            <view class="mt-[20rpx] bg-white p-[20rpx]">
                <view v-for="(item, index) in platformLists" :key="index">
                    <view class="text-xl font-medium mb-[20rpx]">{{
                        item.name
                    }}</view>
                    <view class="flex flex-wrap justify-stretch mx-[-20rpx]">
                        <view
                            v-for="(i, idx) in item.lists"
                            :key="idx"
                            class="px-[20rpx] w-full mb-[20rpx]"
                        >
                            <view
                                class="bg-[#FBFBFC] border border-solid border-light h-full flex items-center rounded-lg px-[40rpx] py-[30rpx] cursor-pointer"
                                @click="toRelease(i)"
                            >
                                <!-- eslint-disable-next-line vue/html-self-closing -->
                                <image
                                    :src="i.icon"
                                    class="w-[88rpx] h-[88rpx] flex-none"
                                />
                                <view class="ml-[30rpx]">
                                    <view
                                        class="text-tx-primary text-xl font-medium flex items-center"
                                    >
                                        {{ i.name }}
                                        <view
                                            v-if="i.disabled"
                                            class="bg-[#EFF0F1] text-tx-regular text-xs px-[20rpx] py-[4rpx] rounded ml-[20rpx]"
                                        >
                                            即将开放
                                        </view>
                                    </view>
                                    <view class="text-tx-secondary mt-[12rpx]">
                                        {{ i.desc }}
                                    </view>
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </scroll-view>
    </view>
</template>
<script lang="ts" setup>
import light_api from '@/packages/static/images/app-release/light_api.svg'
import light_app from '@/packages/static/images/app-release/light_app.svg'
import light_dingtalk from '@/packages/static/images/app-release/light_dingtalk.svg'
import light_enterprise_wechat from '@/packages/static/images/app-release/light_enterprise_wechat.svg'
import light_flybook from '@/packages/static/images/app-release/light_flybook.svg'
import light_js from '@/packages/static/images/app-release/light_js.svg'
import light_public_account from '@/packages/static/images/app-release/light_public_account.svg'
import light_qq from '@/packages/static/images/app-release/light_qq.svg'
import light_small_program from '@/packages/static/images/app-release/light_small_program.svg'
import light_wx from '@/packages/static/images/app-release/light_wx.svg'
import light_web from '@/packages/static/images/app-release/light_web.svg'
import { reactive } from 'vue'
import { useRoute, useRouter } from 'uniapp-router-next'

const platformLists = reactive([
    {
        name: 'WebAPP',
        lists: [
            {
                name: '网页',
                icon: light_web,
                desc: '用户在此链接可以直接和您的智能体聊天',
                key: 'web',
                disabled: false
            },
            {
                name: 'JS嵌入',
                icon: light_js,
                desc: '可添加到网站的任何位置，将此 iframe 添加到 html 代码中',
                key: 'js',
                disabled: false
            },
            {
                name: '微信公众号',
                icon: light_public_account,
                desc: '可在微信公众号后台配置，提供智能体服务',
                key: 'oa',
                disabled: false
            },
            {
                name: '朋友圈海报',
                icon: light_wx,
                desc: '用户扫码后，可直接和您的智能体聊天',
                key: 'web',
                disabled: false
            }
        ]
    },

    {
        name: 'API对接',
        lists: [
            {
                name: 'API调用',
                icon: light_api,
                desc: '用户在此链接可以直接和您的智能体聊天',
                key: 'api',
                disabled: false
            },

            {
                name: '企业微信',
                icon: light_enterprise_wechat,
                desc: '用户在此链接可以直接和您的智能体聊天',
                key: 'qwx',
                disabled: false
            }
            // {
            //     name: '飞书',
            //     icon: light_flybook,
            //     desc: '在飞书群聊中提供智能体服务',
            //     disabled: true
            // },
            // {
            //     name: 'QQ',
            //     icon: light_qq,
            //     desc: '用户在此链接可以直接和您的智能体聊天',
            //     disabled: true
            // },
            //
            // {
            //     name: '钉钉',
            //     icon: light_dingtalk,
            //     desc: '用户在此链接可以直接和您的智能体聊天',
            //     disabled: true
            // },
            //
            // {
            //     name: 'APP',
            //     icon: light_app,
            //     desc: '用户在此链接可以直接和您的智能体聊天',
            //     disabled: true
            // },
            // {
            //     name: '小程序',
            //     icon: light_small_program,
            //     desc: '用户在此链接可以直接和您的智能体聊天',
            //     disabled: true
            // }
        ]
    }
])
const router = useRouter()
const route = useRoute()
const toRelease = (item: any) => {
    if (item.disabled) return
    router.navigateTo({
        path: '/packages/pages/release_robot/release_robot',
        query: {
            key: item.key,
            id: route.query.id
        }
    })
}
</script>
