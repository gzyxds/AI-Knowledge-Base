<template>
    <Collapse>
        <template #title>
            <Icon name="local-icon-list-2" :size="16" />
            <span class="text-2xl ml-1"> 大纲 </span>
        </template>
        <el-tree
            style="max-width: 600px"
            :data="data"
            node-key="label"
            default-expand-all
            :expand-on-click-node="false"
        >
            <template #default="{ node, data }">
                <div class="flex item-center">
                    <span class="mr-1">•</span>
                    <span>{{ node.label }}</span>
                </div>
            </template>
        </el-tree>
    </Collapse>
</template>
<script setup lang="ts">
import Collapse from '../common/collapse.vue'
import { computed } from 'vue'

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
            label: replaceText(key),
            children: [] as any[]
        }
        res.push(item)
        if (!isArray(result[key])) {
            transformTree(result[key], item.children)
        } else {
            item.children = result[key].map((text) => ({
                label: replaceText(text)
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

<style lang="scss" scoped>
.el-tree {
    --el-tree-node-hover-bg-color: transparent;
    :deep() {
        .el-tree-node__content {
        }
    }
}
</style>
