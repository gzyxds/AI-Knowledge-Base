import type { PluginWithOptions } from 'markdown-it'

export interface APluginOptions {}

export const aPlugin: PluginWithOptions<APluginOptions> = (
  md,
  options = {}
): void => {
  md.renderer.rules.link_open = (tokens, idx, options, env, slf) => {
    const aIndex = tokens[idx].attrIndex('href')

    if (aIndex !== -1) {
      tokens[idx].attrs.push(['target', '_blank'])
    }
    return slf.renderToken(tokens, idx, options)
  }
}
