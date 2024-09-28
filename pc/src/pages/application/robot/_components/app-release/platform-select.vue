<template>
    <div>
        <div v-for="(item, index) in platformLists" :key="index">
            <div class="text-xl font-medium mb-[20px]">{{ item.name }}</div>
            <div class="flex flex-wrap justify-stretch mx-[-10px]">
                <div
                    v-for="(i, idx) in item.lists"
                    :key="idx"
                    class="px-[10px] sm:w-[50%] w-full mb-[20px]"
                >
                    <div
                        class="bg-page hover:bg-primary-light-9 h-full flex items-center rounded-[12px] px-[20px] py-[15px] cursor-pointer"
                        @click="emit('click-item', i.key)"
                    >
                        <!-- eslint-disable-next-line vue/html-self-closing -->
                        <img :src="i.icon" class="w-[44px] h-[44px]" />
                        <div class="ml-[15px]">
                            <div
                                class="text-tx-primary text-xl font-medium flex items-center"
                            >
                                {{ i.name }}
                                <div
                                    v-if="i.disabled"
                                    class="bg-[#EFF0F1] text-tx-regular text-xs px-[10px] py-[2px] rounded ml-[10px] dark:bg-br-light"
                                >
                                    即将开放
                                </div>
                            </div>
                            <div class="text-tx-secondary mt-[6px]">
                                {{ i.desc }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import light_api from '@/assets/image/app-release/light_api.svg'
import light_app from '@/assets/image/app-release/light_app.svg'
import light_dingtalk from '@/assets/image/app-release/light_dingtalk.svg'
import light_enterprise_wechat from '@/assets/image/app-release/light_enterprise_wechat.svg'
import light_wx from '@/assets/image/app-release/light_wx.svg'
import light_flybook from '@/assets/image/app-release/light_flybook.svg'
import light_js from '@/assets/image/app-release/light_js.svg'
import light_public_account from '@/assets/image/app-release/light_public_account.svg'
import light_qq from '@/assets/image/app-release/light_qq.svg'
import light_small_program from '@/assets/image/app-release/light_small_program.svg'
import light_web from '@/assets/image/app-release/light_web.svg'
const emit = defineEmits<{
    (event: 'click-item', value: any): void
}>()

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
            //   name: '飞书',
            //   icon: light_flybook,
            //   desc: '在飞书群聊中提供智能体服务',
            //   disabled: true
            // },
            // {
            //   name: 'QQ',
            //   icon: light_qq,
            //   desc: '用户在此链接可以直接和您的智能体聊天',
            //   disabled: true
            // },
            //
            // {
            //   name: '钉钉',
            //   icon: light_dingtalk,
            //   desc: '用户在此链接可以直接和您的智能体聊天',
            //   disabled: true
            // },
            //
            // {
            //   name: 'APP',
            //   icon: light_app,
            //   desc: '用户在此链接可以直接和您的智能体聊天',
            //   disabled: true
            // },
            // {
            //   name: '小程序',
            //   icon: light_small_program,
            //   desc: '用户在此链接可以直接和您的智能体聊天',
            //   disabled: true
            // }
        ]
    }
])
</script>
