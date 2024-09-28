import { defineComponent, shallowRef } from 'vue'

import { camelCase } from 'lodash-es'
import type { DefineComponent, Slot } from 'vue'

// 将横线命名转大小驼峰
function keysToCamelKebabCase(obj: Record<string, any>) {
  const newObj: typeof obj = {}
  for (const key in obj) newObj[camelCase(key)] = obj[key]
  return newObj
}

export type DefineTemplateComponent<
  Bindings extends object,
  Slots extends Record<string, Slot | undefined>
> = DefineComponent<object> & {
  new (): { $slots: { default(_: Bindings & { $slots: Slots }): any } }
}

export type ReuseTemplateComponent<
  Bindings extends object,
  Slots extends Record<string, Slot | undefined>
> = DefineComponent<Bindings> & {
  new (): { $slots: Slots }
}

export type ReusableTemplatePair<
  Bindings extends object,
  Slots extends Record<string, Slot | undefined>
> = [
  DefineTemplateComponent<Bindings, Slots>,
  ReuseTemplateComponent<Bindings, Slots>
]

const useTemplate = <
  Bindings extends object,
  Slots extends Record<string, Slot | undefined> = Record<
    string,
    Slot | undefined
  >
>(): ReusableTemplatePair<Bindings, Slots> => {
  const render = shallowRef<Slot | undefined>()

  const define = defineComponent({
    setup(_, { slots }) {
      return () => {
        // 将复用模板的渲染函数内容保存起来
        render.value = slots.default
      }
    }
  }) as DefineTemplateComponent<Bindings, Slots>

  const reuse = defineComponent({
    setup(_, { attrs, slots }) {
      return () => {
        // 还没定义复用模板，则抛出错误
        if (!render.value) {
          throw new Error('你还没定义复用模板呢！')
        }
        // 执行渲染函数，传入 attrs、slots
        const vnode = render.value({
          ...keysToCamelKebabCase(attrs),
          $slots: slots
        })
        return vnode.length === 1 ? vnode[0] : vnode
      }
    }
  }) as ReuseTemplateComponent<Bindings, Slots>

  return [define, reuse]
}

export default useTemplate
