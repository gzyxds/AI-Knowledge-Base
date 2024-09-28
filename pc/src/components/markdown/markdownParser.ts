import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'
import 'highlight.js/styles/atom-one-dark.css'
import markdownItMath from '@iktakahiro/markdown-it-katex'
import { codePlugin, type CodePluginOptions } from './codePlugin'
import { customLinkPlugin } from './customLink'
import { aPlugin } from './aPlugin'
import { docQuotePlugin } from './docLInk'

interface Options extends Partial<MarkdownIt.Options> {
    lineNumbers?: boolean
}

export const createMarkdown = (options: Options) => {
    const md = new MarkdownIt({
        ...options,
        langPrefix: 'language-',
        highlight(str: any, lang: any) {
            try {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(lang, str, true).value
                }
                return hljs.highlightAuto(str).value
            } catch (error) {
                return str
            }
        }
    })
    md.use<CodePluginOptions>(codePlugin, {
        lineNumbers: options.lineNumbers
    })
    md.use(markdownItMath, {
        output: 'mathml'
    })
    md.use(aPlugin).use(docQuotePlugin).use(customLinkPlugin)
    return md
}

export const preprocessContent = (content: string) => {
    content += ''
    return content
        .replace(/\n(#.*#)/g, '\n\n$1')
        .replaceAll('\\(', '$')
        .replaceAll('\\)', '$')
        .replaceAll('\\[', '$$')
        .replaceAll('\\]', '$$')
}
