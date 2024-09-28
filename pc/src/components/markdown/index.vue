<template>
    <div
        class="markdown-it-container"
        :style="{
            '-webkit-line-clamp': lineClamp
        }"
    >
        <div
            ref="markdownBodyRef"
            class="markdown-body"
            :class="[themeComputed]"
            @click="handleClick($event)"
            v-html="result"
        />
        <div v-if="typing" class="markdown-typing" :style="cursorPosition" />
    </div>
</template>

<script setup lang="ts">
import { createMarkdown, preprocessContent } from './markdownParser'
import { useDark } from '@vueuse/core'
import 'highlight.js/styles/atom-one-dark.css'
import './github-markdown.css'

const props = withDefaults(
    defineProps<{
        content: string
        linkList?: any[]
        html?: boolean
        breaks?: boolean
        linkify?: boolean
        typographer?: boolean
        lineNumbers?: boolean
        typing?: boolean
        // 显示富文本行数
        lineClamp?: number
        theme?: 'light' | 'dark'
    }>(),
    {
        content: '',
        html: true,
        breaks: true,
        typographer: true,
        linkify: true,
        lineNumbers: true,
        typing: false,
        lineClamp: 0,
        linkList: () => []
    }
)
const emit = defineEmits<{
    (event: 'click-custom-link', value: string): void
}>()
const result = ref('')
const markdownBodyRef = shallowRef<HTMLDivElement>()
const isDark = useDark()
const themeComputed = computed(() => {
    if (props.theme) return props.theme
    else {
        return isDark.value ? 'dark' : 'light'
    }
})
let md = createMarkdown({
    html: props.html,
    breaks: props.breaks,
    typographer: props.typographer,
    linkify: props.linkify,
    lineNumbers: props.lineNumbers
})

const findLastTextNode = (parent: Node): Node | undefined => {
    const children = parent.childNodes
    for (let i = children.length - 1; i >= 0; i--) {
        const node = children[i]
        if (node.nodeType === Node.TEXT_NODE && /\S/.test(node.nodeValue!)) {
            node.nodeValue?.replace(/\S+$/, '')
            return node
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            const lastNode = findLastTextNode(node)
            if (lastNode) {
                return lastNode
            }
        }
    }
}
const cursorPosition = reactive({
    top: 'auto',
    left: 'auto'
})
const updateCursor = () => {
    if (markdownBodyRef.value) {
        const lastTextNode = findLastTextNode(markdownBodyRef.value)
        const textNode = document.createTextNode('\u200B')
        if (lastTextNode) {
            lastTextNode.parentElement?.appendChild(textNode)
        } else {
            markdownBodyRef.value?.appendChild(textNode)
        }
        const range = document.createRange()
        range.setStart(textNode, 0)
        range.setEnd(textNode, 0)
        const textRect = range.getBoundingClientRect()
        const markdownBodyRect = markdownBodyRef.value?.getBoundingClientRect()
        cursorPosition.left = `${textRect.left - markdownBodyRect.left}px`
        cursorPosition.top = `${textRect.top - markdownBodyRect.top}px`
        textNode.remove()
    }
}

watchEffect(() => {
    md = createMarkdown({
        html: props.html,
        breaks: props.breaks,
        typographer: props.typographer,
        linkify: props.linkify,
        lineNumbers: props.lineNumbers
    })
    result.value = md.render(preprocessContent(props.content))
})

watch(
    () => props.content,
    async (value) => {
        result.value = md?.render(preprocessContent(value), {
            linkList: props.linkList
        })
        await nextTick()
        updateCursor()
    },
    {
        immediate: true
    }
)

const handleClick = async (e: any) => {
    const target: HTMLElement = e.target
    if (target.className === 'code-copy-btn') {
        const text = e.target.parentElement.nextElementSibling.textContent
        await copy(text)
    }
    if (target.className === 'markdown-custom-link') {
        emit('click-custom-link', target.textContent!)
    }
}
</script>

<style lang="scss">
@use 'markdown.scss';
.markdown-it-container {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
