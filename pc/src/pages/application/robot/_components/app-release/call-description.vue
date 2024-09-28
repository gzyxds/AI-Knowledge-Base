<template>
  <div>
    <popup
      ref="popupRef"
      title="调用说明"
      cancel-button-text=""
      confirm-button-text=""
      width="900px"
    >
      <template #trigger>
        <!-- <ElButton>调用说明</ElButton> -->
        <ElButton>{{ contentList[type].name }}</ElButton>
      </template>
      <!-- <Markdown :content="content" /> -->
      <Markdown :content="contentList[type].content" />
    </popup>
  </div>
</template>
<script lang="ts" setup>
const prop = defineProps({
  type: {
    type: String,
    default: ''
  }
})
const AppContent = `
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
请参考官方产品对接微信文档。

【chatgpt-on-wechat 的使用请自行看官方文档】
文档地址: https://github.com/zhayujie/chatgpt-on-wechat
`

const contentList: any = {
  app: {
    name: 'API调用说明',
    content: AppContent
  },
  wx: {
    name: '微信调用说明',
    content: WXContent
  }
}
</script>
