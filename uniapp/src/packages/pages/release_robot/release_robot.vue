<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full">
        <ReleaseWeb
            ref="releaseWebRef"
            title="发布为网页/朋友圈海报"
            v-if="currentKey === 'web'"
            :type="1"
            :robot-id="robotId"
            :show-more-action="true"
        >
            <template #actions="{ item }">
                <view class="flex items-center" @click="showPoster(item)">
                    <u-icon name="photo" :size="32" />
                    <view class="ml-[10rpx]"> 生成海报 </view>
                </view>
            </template>
        </ReleaseWeb>
        <ReleaseWeb
            title="发布为JS"
            v-if="currentKey === 'js'"
            :type="2"
            :robot-id="robotId"
        >
            <template #actions="{ item }">
                <view class="flex items-center" @click="showJsEmbedding(item)">
                    <u-icon name="eye" :size="32" />
                    <view class="ml-[10rpx]"> 查看代码 </view>
                </view>
            </template>
        </ReleaseWeb>
        <ReleaseWeb
            title="发布为公众号"
            v-if="currentKey === 'oa'"
            :type="3"
            :robot-id="robotId"
        >
            <template #actions="{ item }">
                <view class="flex items-center" @click="showOaConfig(item)">
                    <u-icon name="chat" :size="32" />
                    <view class="ml-[10rpx]"> 公众号配置 </view>
                </view>
            </template>
        </ReleaseWeb>
        <ReleaseApi
            title="发布为API"
            v-if="currentKey === 'api'"
            :type="4"
            :robot-id="robotId"
        >
            <template #btn>
                <view class="mr-[20rpx] mt-[20rpx]">
                    <CallDescription title="API调用说明" :content="ApiContent">
                        <u-button size="medium"> 调用说明 </u-button>
                    </CallDescription>
                </view>
            </template>
        </ReleaseApi>
        <ReleaseApi
            title="发布为企业微信"
            v-if="currentKey === 'qwx'"
            :type="5"
            :robot-id="robotId"
        >
            <template #btn>
                <view class="mr-[20rpx] mt-[20rpx]">
                    <CallDescription title="微信调用说明" :content="WXContent">
                        <u-button size="medium"> 调用说明 </u-button>
                    </CallDescription>
                </view>
            </template>
        </ReleaseApi>
        <JsEmbedding
            v-model:show="jsEmbedding.show"
            :apikey="jsEmbedding.apikey"
        />
        <OaConfig v-model:show="oaConfig.show" :apikey="oaConfig.apikey" />
        <Poster
            v-bind="posterState"
            v-model:show="posterState.show"
            @update="releaseWebRef.refresh()"
        />
    </view>
</template>

<script lang="ts" setup>
import { useRoute } from 'uniapp-router-next'
import ReleaseWeb from './components/release-web.vue'
import ReleaseApi from './components/release-api.vue'
import JsEmbedding from './components/js-embedding.vue'
import OaConfig from './components/oa-config.vue'
import CallDescription from './components/call-description.vue'
import Poster from './components/poster.vue'
import { computed, reactive, ref, shallowRef } from 'vue'

const route = useRoute()
const releaseWebRef = shallowRef()
const currentKey = computed(() => {
    return route.query.key
})
const robotId = computed<string>(() => {
    return route.query.id as string
})
const jsEmbedding = reactive({
    show: false,
    apikey: ''
})
const oaConfig = reactive({
    show: false,
    apikey: ''
})

const showOaConfig = (item: any) => {
    oaConfig.show = true
    oaConfig.apikey = item.apikey
}

const showJsEmbedding = (item: any) => {
    jsEmbedding.show = true
    jsEmbedding.apikey = item.apikey
}

const posterState = reactive({
    show: false,
    url: '',
    apikey: '',
    shareId: ''
})

const showPoster = (row: any) => {
    posterState.show = true
    posterState.url = row.share_bg
    posterState.apikey = row.apikey
    posterState.shareId = row.id
}

const ApiContent = `
【接口地址】
请求方式: POST
接口地址: /api/v1/chat/completions
调用示例: http(s)://yourdomain.com/api/v1/chat/completions

【Body参数】
\`\`\` json
{
    "messages": [
        {
            "role": "user",
            "content": "你要提问的问题"
        }
    ]
}
\`\`\`

【Header参数】
Authorization: 此参数是发布渠道的 apikey (必须的)

【PHP代码示例】
\`\`\` php
public function chat()
{
    // 设置SSE响应
    header('Access-Control-Allow-Origin: *');
    header('Connection: keep-alive');
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');
    
    // 处理响应回调
    $response = true;
    $callback = function ($ch, $data) use (&$response, &$total) {
        if (str_starts_with($data, 'data:')) {
            echo $data;
        }

        if(!connection_aborted()){
            return strlen($data);
        } else {
            return 1;
        }
    };

    // 请求的参数
    $data = [
        'messages'  => [
            ['role'=>'user', 'content'=>'你好吗?']
        ]
    ];

    // 请求头参数
    $headers  = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: web-8b582192d72b20931b9142155d1476cc7Fnmp' // 此参数是 apikey (必须的)
    ];

    // 发起接口请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http(s)://【你自己的域名】/api/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
    curl_exec($ch);
    curl_close($ch);

    if(true !== $response){
        throw new Exception($response);
    }

    exit();
}
\`\`\`
`

const WXContent = `
【接口地址】
请求方式: POST
接口地址: /api/v1/chat/completions
调用示例: http(s)://yourdomain.com/api/v1/chat/completions

【参数说明】
open_ai_api_key:  apiKey密钥
open_ai_api_base: 请求的域名

\`\`\`
{
  "channel_type": "wx", // 渠道类型: wx=个人微信的意思
  "open_ai_api_key": "wx-f228079c92d0ab83548067bba13967d1xR1cu",          // 修改此处密钥
  "open_ai_api_base": "http(s)://yourdomain.com/api/v1", // 修改此处域名
    "model": "gpt-3.5-turbo",
    "text_to_image": "dall-e-2",
    "voice_to_text": "openai",
    "text_to_voice": "openai",
    "proxy": "",
    "hot_reload": false,
    "single_chat_prefix": [
        "bot",
        "@bot"
    ],
    "single_chat_reply_prefix": "[bot] ",
    "group_chat_prefix": [
        "@bot"
    ]
    ......
}
\`\`\`

【更详细的接入文档】
文档地址: https://www.mddai.cn/docs/cw/deployment/wechat.html

【chatgpt-on-wechat 的使用请自行看官方文档】
文档地址: https://github.com/zhayujie/chatgpt-on-wechat
`
</script>

<style>
page {
    height: 100%;
}
</style>
