import type { PluginWithOptions } from 'markdown-it'

const defaultMarker = '#'
interface CustomLinkPluginOptions {
    marker: string
}
export const customLinkPlugin: PluginWithOptions<
    Partial<CustomLinkPluginOptions>
> = (md, options = {}): void => {
    const marker = options.marker || defaultMarker

    md.block.ruler.before(
        'paragraph',
        'custom_link',
        function (state, startLine, endLine, silent) {
            const pos = state.bMarks[startLine] + state.tShift[startLine]
            const max = state.eMarks[startLine]

            if (pos >= max) {
                return false
            }
            if (silent) {
                return true
            }
            const text = state.src.substring(pos, max)
            const start = text.indexOf(marker)
            const end = text.lastIndexOf(marker)
            if (start < 0 || end < 0 || start == end) {
                return false
            }
            //#...#规则前面的内容
            const startContent = text.substring(0, start)
            //#...#规则后面的内容
            const endContent = text.substring(end + 1)
            //#...#规则中间的内容
            const content = text.substring(start + 1, end)

            //插入<div>
            const token_div_o = state.push('div_open', 'div', 1)
            token_div_o.attrs = [['class', 'mt-[8px]']]
            token_div_o.map = [startLine, state.line]

            //插入#...#规则前面的内容
            const token_s = state.push('inline', '', 0)
            token_s.content = startContent
            token_s.children = []

            //插入<a class="markdown-custom-link">
            const token_a_o = state.push('link_open', 'a', 1)
            token_a_o.attrs = [['class', 'markdown-custom-link']]
            token_a_o.map = [startLine, state.line]

            //插入#...#规则中间的内容
            const token = state.push('inline', '', 0)
            token.content = content
            token.children = []

            //闭合a标签
            state.push('link_close', 'a', -1)
            const token_e = state.push('inline', '', 0)

            //插入#...#规则后面的内容
            token_e.content = endContent
            token_e.children = []

            //闭合div标签
            state.push('div_close', 'div', -1)

            state.line = startLine + 1

            return true
        },
      {
        alt: ['paragraph']
      //         'reference', 'blockquote'
      }
    )
}
