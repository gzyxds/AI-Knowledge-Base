<!-- uniapp vue3 markdown解析 -->
<template>
    <view class="ua__markdown">
        <!--        <rich-text-->
        <!--            space="nbsp"-->
        <!--            selectable-->
        <!--            user-select-->
        <!--            :nodes="parseNodes(content)"-->
        <!--            @itemclick="handleItemClick"-->
        <!--        >-->
        <!--        </rich-text>-->
        <mp-html
            :selectable="true"
            :scrollTable="true"
            :content="parseNodes(content)"
            @linktap="handleItemClick"
        ></mp-html>
    </view>
</template>

<script setup>
import MarkdownIt from './lib/markdown-it.min.js'
import hljs from 'highlight.js/lib/common'
import './lib/highlight/atom-one-dark.css'
// #ifdef APP-NVUE
import parseHtml from './lib/html-parser.js'
// #endif
import markdownItMath from '@iktakahiro/markdown-it-katex'
import MpHtml from '@/uni_modules/mp-html/components/mp-html/mp-html.vue'
import { useCopy } from '@/hooks/useCopy'
import { customLinkPlugin } from './customLink'
import { docQuotePlugin } from './docLInk'
const props = defineProps({
    // 解析内容
    content: String,
    showLine: { type: [Boolean, String], default: true },
    linkList: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['click-link'])

const copyCodeData = []
const markdown = MarkdownIt({
    html: true,
    breaks: true,
    typographer: true,
    linkify: true,
    lineNumbers: true,
    highlight: function (str, lang) {
        let preCode = ''
        try {
            preCode = hljs.highlightAuto(str).value
        } catch (err) {
            preCode = markdown.utils.escapeHtml(str)
        }
        const lines = preCode.split(/\n/).slice(0, -1)
        // 添加自定义行号
        let html = lines
            .map((item, index) => {
                if (item == '') {
                    return ''
                }
                return (
                    '<li><span class="line-num" data-line="' +
                    (index + 1) +
                    '"></span>' +
                    item +
                    '</li>'
                )
            })
            .join('')
        if (props.showLine) {
            html = '<ol style="padding: 0px 30px;">' + html + '</ol>'
        } else {
            html =
                '<ol style="padding: 0px 7px;list-style:none;">' +
                html +
                '</ol>'
        }
        copyCodeData.push(str)
        let htmlCode = `<div class="markdown-wrap">`

        htmlCode += `<div class="copy-line" style="text-align: right;font-size: 12px; margin-bottom: -10px;border-radius: 5px 5px 0 0;">`
        htmlCode += `${lang}<a class="code-copy-btn" code-data-index="${
            copyCodeData.length - 1
        }">复制代码</a>`
        htmlCode += `</div>`

        htmlCode += `<pre class="hljs" style="padding:10px 8px;margin:5px 0;overflow: auto;display: block;border-radius: 5px;"><code>${html}</code></pre>`
        htmlCode += '</div>'
        return htmlCode
    }
})
markdown.use(markdownItMath)
markdown.use(customLinkPlugin)
markdown.use(docQuotePlugin)
const parseNodes = (value) => {
    if (!value) return
    // 解析<br />到\n
    value = value.replace(/<br>|<br\/>|<br \/>/g, '\n')
    value = value.replace(/&nbsp;/g, ' ')
    let htmlString = ''
    if (value.split('```').length % 2) {
        let mdtext = value
        if (mdtext[mdtext.length - 1] != '\n') {
            mdtext += '\n'
        }
        htmlString = markdown.render(mdtext, {
            linkList: props.linkList
        })
    } else {
        htmlString = markdown.render(value, {
            linkList: props.linkList
        })
    }
    // 解决小程序表格边框型失效问题
    htmlString = htmlString.replace(/<table/g, `<table class="table"`)
    htmlString = htmlString.replace(/<tr/g, `<tr class="tr"`)
    htmlString = htmlString.replace(/<th>/g, `<th class="th">`)
    htmlString = htmlString.replace(/<td/g, `<td class="td"`)
    htmlString = htmlString.replace(/<hr>|<hr\/>|<hr \/>/g, `<hr class="hr">`)
    console.log(htmlString)
    // #ifndef APP-NVUE
    return htmlString
    // #endif

    // 将htmlString转成htmlArray，反之使用rich-text解析
    // #ifdef APP-NVUE
    return parseHtml(htmlString)
    // #endif
}

// 复制代码
const handleItemClick = (e) => {
    const { 'code-data-index': codeDataIndex, class: className, innerText } = e
    switch (className) {
        case 'code-copy-btn': {
            // #ifdef H5
            uni.setClipboardData({
                data: copyCodeData[codeDataIndex],
                showToast: false,
                success() {
                    uni.showToast({
                        title: '复制成功',
                        icon: 'none'
                    })
                }
            })
            // #endif
            // #ifndef H5
            const { copy } = useCopy()
            console.log(copyCodeData[codeDataIndex])
            copy(copyCodeData[codeDataIndex])
            // #endif
            break
        }
        case 'markdown-custom-link': {
            emit('click-link', innerText)
        }
    }
}
</script>
