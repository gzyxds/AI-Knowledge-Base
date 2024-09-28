<template>
    <u-popup
        v-model="showModel"
        safe-area-inset-bottom
        closeable
        border-radius="16"
        mode="bottom"
    >
        <view class="h-[80vh] flex flex-col">
            <view
                class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
            >
                查看代码
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="p-[20rpx]">
                        <u-form
                            ref="uFormRef"
                            label-position="top"
                            :border-bottom="false"
                        >
                            <u-form-item
                                label="要在您网站的任何位置添加聊天智能体，请将此 iframe 添加到您的 html 代码中"
                            >
                                <view class="flex-1 min-w-0">
                                    <ua-markdown :content="htmlCode" />
                                </view>
                            </u-form-item>
                            <u-form-item
                                label="要在您网站的右下角添加聊天气泡，请复制添加到您的 html中"
                            >
                                <view class="flex-1 min-w-0">
                                    <ua-markdown :content="jsCode" />
                                </view>
                            </u-form-item>
                        </u-form>
                    </view>
                </scroll-view>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { useVModels } from '@vueuse/core'
import config from '@/config'
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

const htmlCode = computed(() => {
    return `\`\`\`html
<iframe 
    src="${chatLink.value}" 
    class="chat-iframe"
    frameborder="0"
>
</iframe>
<style>
    /* iframe框默认占满全屏，可根据需求自行调整样式  */
    .chat-iframe {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        z-index: 9999;
    }
</style>
\`\`\``
})

const jsCode = computed(() => {
    let origin = config.baseUrl
    //#ifdef H5
    origin = `${location.origin}/`
    //#endif
    return `\`\`\`html
<script>
    window.chat_iframe_src = '${chatLink.value}'
    window.chat_iframe_width = '375px' //聊天窗口宽
    window.chat_iframe_height = '667px'  //聊天窗口高
    window.chat_icon_bg = '#3C5EFD' //聊天悬浮按钮背景
    window.chat_icon_color = '#fff' //聊天悬浮按钮颜色
    var js = document.createElement('script')
    js.type = 'text/javascript'
    js.async = true
    js.src = '${origin}js-iframe.js'
    var header = document.getElementsByTagName('head')[0]
    header.appendChild(js)
<\/script>
\`\`\`
`
})
</script>
