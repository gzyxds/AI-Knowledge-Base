<template>
    <div>
        <TinyMceEditor
            api-key="rrsqwlunlutrzwrtd864sti95kxkc2bft4kb5z667yzo17lr"
            v-model="content"
            :init="init"
            :id="tinymceId"
            ref="tinyMceEditorRef"
        ></TinyMceEditor>
    </div>
</template>

<script setup lang="ts">
import TinyMceEditor from '@tinymce/tinymce-vue'
import tinymce from 'tinymce/tinymce'
import 'tinymce/themes/silver'
import 'tinymce/themes/silver/theme'
import 'tinymce/models/dom'
import 'tinymce/icons/default'
import 'tinymce/icons/default/icons'
import 'tinymce/plugins/autoresize'

import type {Editor, RawEditorOptions} from 'tinymce/tinymce'
import type {Sse} from 'utils/http/sse'

import {CreationEnum} from '~/enums/chatEnums'
import {useAppStore} from '~/stores/app'
import {chatSendText} from '~/api/chat'
import {useUserStore} from '~/stores/user'
import {createMarkdown, preprocessContent} from "~/components/markdown/markdownParser";

const route = useRoute()
const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()
const tinyMceEditorRef = shallowRef<typeof TinyMceEditor>()

const emit = defineEmits<{
    (event: 'content', value: string): void
    (event: 'rewrite', value: { type: number, text: string }): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: string
        autoresize?: boolean
        options?: Partial<RawEditorOptions>
    }>(),
    {
        modelValue: '',
        autoresize: true,
        options: () => ({})
    }
)
const chatModel = inject<any>('chatModel')
const content = ref<string>('')

let md = createMarkdown({
    html: true,
    breaks: true,
    typographer: true,
    linkify: true,
    lineNumbers: true
})
const marker = '<!--INSERT-HERE-->' // 定义标记
const markerIndex = ref<number>(-1)

const tinymceId = ref<string>(
    'vue-tinymce-' + +new Date() + ((Math.random() * 1000).toFixed(0) + '')
)

const init = reactive<RawEditorOptions>({
    selector: '#' + tinymceId.value,
    menubar: false,
    statusbar: false,
    plugins: [props.autoresize ? 'autoresize' : ''],
    toolbar: [
        {name: 'styles', items: ['styles']},
        {name: 'formatting', items: ['h1', 'h2', 'h3', 'bold', 'italic', 'underline', 'forecolor', 'backcolor']},
        {name: 'alignment', items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']},
        {name: 'indentation', items: ['outdent', 'indent']},
        {name: 'custom', items: ['customBtn1', 'customBtn2', 'customBtn3', 'customBtn4']},
        {name: 'history', items: ['undo', 'redo']}
    ],
    toolbar_mode: 'sliding',
    toolbar_sticky: true,
    font_formats:
        'Arial=arial,helvetica,sans-serif; 宋体=SimSun; 微软雅黑=Microsoft Yahei; Impact=impact,chicago;',
    branding: false,
    language: 'zh_CN',
    language_url: '/tinymce/langs/zh_CN.js',
    skin: 'oxide',
    skin_url: '/tinymce/skins/ui/oxide',
    content_css: '/tinymce/skins/content/default/content.css',
    content_style: 'body {font-family: Arial; font-size: 14px; color: #333;}',
    image_dimensions: false,

    setup(editor: Editor) {
        // 注册默认的工具栏
        editor.ui.registry.addContextToolbar('textselection', {
            predicate: () => !editor.selection.isCollapsed(),
            items: 'h1 h2 h3 bold italic underline forecolor backcolor customBtn1 customBtn2 customBtn3 customBtn4',
            position: 'selection',
            scope: 'node'
        })
        // 注册扩写工具栏
        editor.ui.registry.addButton('customBtn1', {
            text: '扩写',
            tooltip: '消耗' + chatModel.value.price + appStore.getTokenUnit,
            onAction: () => {
                if (editor.selection.isCollapsed()) {
                    return feedback.msgError('请选择文本')
                }
                send(CreationEnum.Expand, editor.selection.getContent())
                editor.insertContent(editor.selection.getContent() + `${marker}`)
                editor.insertContent(editor.selection.getContent() + '<p></p>')
                markerIndex.value = content.value.indexOf(marker)
            }
        })
        // 注册改写工具
        editor.ui.registry.addMenuButton('customBtn2', {
            text: '改写',
            tooltip: '消耗' + chatModel.value.price + appStore.getTokenUnit,
            fetch: (callback) => {
                const items: any[] = [
                    {
                        type: 'menuitem',
                        text: '正式得体',
                        tooltip: '消耗' + chatModel.value.price + appStore.getTokenUnit,
                        onAction: () => {
                            if (editor.selection.isCollapsed()) {
                                return feedback.msgError('请选择文本')
                            }
                            send(CreationEnum.Change1, editor.selection.getContent())
                            editor.insertContent(editor.selection.getContent() + `${marker}`)
                            editor.insertContent(editor.selection.getContent() + '<p></p>')
                            markerIndex.value = content.value.indexOf(marker)
                        }
                    },
                    {
                        type: 'menuitem',
                        text: '严肃庄重',
                        onAction: () => {
                            if (editor.selection.isCollapsed()) {
                                return feedback.msgError('请选择文本')
                            }
                            send(CreationEnum.Change2, editor.selection.getContent())
                            editor.insertContent(editor.selection.getContent() + `${marker}`)
                            editor.insertContent(editor.selection.getContent() + '<p></p>')
                            markerIndex.value = content.value.indexOf(marker)
                        }
                    },
                    {
                        type: 'menuitem',
                        text: '轻松',
                        onAction: () => {
                            if (editor.selection.isCollapsed()) {
                                return feedback.msgError('请选择文本')
                            }
                            send(CreationEnum.Change3, editor.selection.getContent())
                            editor.insertContent(editor.selection.getContent() + `${marker}`)
                            editor.insertContent(editor.selection.getContent() + '<p></p>')
                            markerIndex.value = content.value.indexOf(marker)
                        }
                    }
                ];
                callback(items);
            }
        });
      // 注册简写工具
        editor.ui.registry.addButton('customBtn3', {
            text: '简写',
            tooltip: '消耗' + chatModel.value.price + appStore.getTokenUnit,
            onAction: () => {
                if (editor.selection.isCollapsed()) {
                    return feedback.msgError('请选择文本')
                }
                send(CreationEnum.Simple, editor.selection.getContent())
                editor.insertContent(editor.selection.getContent() + `${marker}`)
                editor.insertContent(editor.selection.getContent() + '<p></p>')
                markerIndex.value = content.value.indexOf(marker)
            }
        })
      // 注册续写工具
        editor.ui.registry.addButton('customBtn4', {
            text: '续写',
            tooltip: '消耗' + chatModel.value.price + appStore.getTokenUnit,
            onAction: () => {
                const text = !editor.selection.isCollapsed() ?
                    editor.selection.getContent() :
                    editor.getContent();
                send(CreationEnum.Continue, text)
                editor.insertContent(editor.selection.getContent() + `${marker}`)
                editor.insertContent(editor.selection.getContent() + '<p></p>')
                markerIndex.value = content.value.indexOf(marker)
            }
        })

        editor.on('Change', () => {
            // 设置一个延迟防止没有获取到最新的修改
            setTimeout(() => {
                emit('content', content.value)
            }, 100)
        })
    },
    ...props.options
})

let sseInstance: Sse;
// 临时对话变量
const newSSeContent = ref<string>('')
const send = (type: CreationEnum, text: string) => {

    sseInstance = chatSendText({
        other_id: route.query.modelId,
        question: text,
        type: 2,
        creation_type: type,
        model: chatModel.value.id,
    })

    sseInstance.addEventListener('close', async () => {
        resetParams()
        userStore.getUser()
    })
    sseInstance.addEventListener('error', async (ev) => {
        resetParams()
        if (ev.data?.code === 1100) {
            if (!appStore.getIsShowRecharge) {
                feedback.msgError(
                    `${appStore.getTokenUnit}数量已用完。请联系客服增加`
                )
            } else {
                await feedback.confirm(
                    `${appStore.getTokenUnit}数量已用完，请前往充值`
                )
                router.push('/user/recharge')
            }
            return
        }
        if (ev.errorType === 'connectError') {
            feedback.msgError('请求失败，请重试')
        }
    })
    sseInstance.addEventListener('chat', ({data: dataJson}) => {
        const {data} = dataJson!

        if (data) {
            if (markerIndex.value !== -1) {
                newSSeContent.value += data
                const parsedData = md?.render(preprocessContent(newSSeContent.value))
                content.value = replaceAt(content.value, markerIndex.value, parsedData);
            } else {
                // 如果没有找到标记，则在内容末尾插入数据
                content.value += data
            }
        }
    })
    sseInstance.addEventListener('finish', ({data: dataJson}) => {
        const {data} = dataJson!
        if (data) {
            if (markerIndex.value !== -1) {
                newSSeContent.value += data
                const parsedData = md?.render(preprocessContent(newSSeContent.value))
                content.value = replaceAt(content.value, markerIndex.value, parsedData);
            } else {
                // 如果没有找到标记，则在内容末尾插入数据
                content.value += data
            }
        }
    })
}

const resetParams = () => {
    markerIndex.value = -1
    newSSeContent.value = ''
    beforeIndex.value = ''
    afterIndex.value = ''
}

const removeSse = () => {
    sseInstance?.removeEventListener('close')
    sseInstance?.removeEventListener('chat')
    sseInstance?.removeEventListener('error')
    sseInstance?.removeEventListener('finish')
    sseInstance?.abort()
}

//  替换字符串指定位置的字符串
const beforeIndex = ref<string>('')
const afterIndex = ref<string>('')
function replaceAt(str: string, index: number, input: string) {
  if (!beforeIndex.value || !afterIndex.value) {
      // 获取指定index之前的部分
      beforeIndex.value = str.substring(0, index);
      // 获取指定index之后的部分，从标记结束后继续
      afterIndex.value = str.substring(index + marker.length);
  }

  // 返回拼接后的新字符串
  return beforeIndex.value + input + afterIndex.value;
}

// 外部调用续写功能
const continuation = async () => {
    await nextTick()
    const editor = tinyMceEditorRef.value?.getEditor()

    setTimeout(() => {
        const text = !editor.selection.isCollapsed() ?
            editor.selection.getContent() :
            editor.getContent();
        send(CreationEnum.Continue, text)
        editor.focus();

        // 将光标定位到尾部
        markerIndex.value = -1
        if (!editor.selection.isCollapsed()) return
        editor.selection.select(editor.getBody(), true);
        editor.insertContent(editor.selection.getContent() + '<p></p>')
    }, 100)
}

// 供外部获取内容
const getContent = () => {
    return unref(content.value)
}

watch(
    () => props.modelValue,
    (value) => {
        content.value = md?.render(preprocessContent(value))
    },
    {
        immediate: true
    }
)

onUnmounted(() => {
    removeSse()
})

onBeforeUnmount(() => {
    tinymce.remove('#' + tinymceId.value)
})

onMounted(() => {
    tinymce.init(init)
})

defineExpose({ continuation, getContent })
</script>

<style lang="scss">
.tinymce-body {
    font-size: 0.89em;
}
</style>
