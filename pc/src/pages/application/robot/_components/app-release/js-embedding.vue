<template>
    <div>
        <popup
            ref="popupRef"
            title="JS嵌入"
            :async="true"
            width="900px"
            confirm-button-text=""
            cancel-button-text=""
        >
            <el-form ref="formRef" label-position="top" label-width="84px">
                <el-form-item>
                    <template #label>
                        <div class="flex items-start">
                            <div class="mr-auto">
                                要在您网站的任何位置添加聊天智能体，请将此
                                iframe 添加到您的 html 代码中
                            </div>
                        </div>
                    </template>
                    <div class="flex-1 min-w-0 rounded-md overflow-hidden">
                        <markdown :content="htmlCode" />
                    </div>
                </el-form-item>
                <el-form-item>
                    <template #label>
                        <div class="flex items-start">
                            <div class="mr-auto">
                                要在您网站的右下角添加聊天气泡，请复制添加到您的
                                html中
                            </div>
                        </div>
                    </template>
                    <div class="flex-1 min-w-0 rounded-md overflow-hidden">
                        <markdown :content="jsCode" />
                    </div>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
const props = defineProps<{
    channelId: string | number
}>()
const emit = defineEmits<{
    (event: 'confirm', value: any): void
}>()
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const { copy } = useCopy()
const open = () => {
    popupRef.value?.open()
}
const close = () => {
    popupRef.value?.close()
}

const chatLink = computed(() => {
    return `${location.origin}/chat/${props.channelId}`
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
    js.src = '${location.origin}/js-iframe.js'
    var header = document.getElementsByTagName('head')[0]
    header.appendChild(js)
<\/script>
\`\`\`
`
})

defineExpose({
    open,
    close
})
</script>
