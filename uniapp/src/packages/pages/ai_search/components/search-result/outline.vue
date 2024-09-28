<template>
    <u-collapse>
        <u-collapse-item open>
            <template #title>
                <view class="flex items-center">
                    <u-icon width="32rpx" name="list-dot" :color="'#333'" />
                    <span class="text-2xl ml-1 font-bold"> 大纲 </span>
                </view>
            </template>
            <da-tree
                :showRadioIcon="false"
                class="leading-snug"
                :data="data"
                labelField="name"
                valueField="name"
                defaultExpandAll
            ></da-tree>
        </u-collapse-item>
    </u-collapse>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import DaTree from '@/components/da-tree/da-tree.vue'
import { isArray } from 'lodash-es'
const props = withDefaults(
    defineProps<{
        content: string
        quote: any[]
    }>(),
    {
        content: '',
        quote: () => []
    }
)

const replaceText = (str: string) => {
    const pattern =
        /(`{3}[\s\S]*?`{3}(?:(?!.)))|(`{3}[\s\S]*)|(`[\s\S]*?`{1}?)|(`[\s\S]*)|(?:\[(?:(?:number )|\^)?([\d]{1,2})\])/g
    return str.replaceAll(pattern, '')
}
const transformTree = (result: Record<string, any>, res: any[] = []) => {
    Object.keys(result).forEach((key) => {
        const item = {
            name: replaceText(key),
            children: [] as any[]
        }
        res.push(item)
        if (!isArray(result[key])) {
            transformTree(result[key], item.children)
        } else {
            item.children = result[key].map((text) => ({
                name: replaceText(text)
            }))
        }
    })
    return res
}
const data = computed(() => {
    try {
        let result = JSON.parse(props.content)
        result = transformTree(result)

        return result
    } catch (error) {
        return []
    }
})
</script>
