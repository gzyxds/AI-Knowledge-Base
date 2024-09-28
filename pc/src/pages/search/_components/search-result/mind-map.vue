<template>
    <Collapse>
        <template #title>
            <Icon name="local-icon-mind_map" :size="16" />
            <span class="text-2xl ml-1"> 脑图 </span>
        </template>
        <div
            class="h-[600px] rounded-[12px] border border-solid border-br-light relative"
        >
            <div
                ref="svgWrapRef"
                class="w-full h-full"
                :class="{
                    '!fixed top-0 left-0 w-screen h-screen z-[9999] bg-body':
                        isFullScreen
                }"
            >
                <div class="toolbar">
                    <div class="toolbar-item">
                        <ElButton
                            v-if="isFullScreen"
                            link
                            @click="isFullScreen = false"
                        >
                            <template #icon>
                                <Icon
                                    name="local-icon-fullscreen-exit"
                                    :size="18"
                                />
                            </template>
                        </ElButton>
                        <ElButton v-else link @click="isFullScreen = true">
                            <template #icon>
                                <Icon name="local-icon-fullscreen" :size="18" />
                            </template>
                        </ElButton>
                    </div>
                    <div class="toolbar-item">
                        <ElButton link @click="markmap?.fit()">
                            <template #icon>
                                <Icon name="el-icon-Refresh" :size="20" />
                            </template>
                        </ElButton>
                    </div>
                    <div class="toolbar-item">
                        <ElButton link @click="markmap?.rescale(1.25)">
                            <template #icon>
                                <Icon name="el-icon-ZoomIn" :size="20" />
                            </template>
                        </ElButton>
                    </div>
                    <div class="toolbar-item">
                        <ElButton link @click="markmap?.rescale(0.8)">
                            <template #icon>
                                <Icon name="el-icon-ZoomOut" :size="20" />
                            </template>
                        </ElButton>
                    </div>
                    <div class="toolbar-item">
                        <ElButton link @click="copy(content)">
                            <template #icon>
                                <Icon name="el-icon-CopyDocument" :size="20" />
                            </template>
                        </ElButton>
                    </div>
                </div>
                <svg ref="svgRef" class="w-full h-full"></svg>
            </div>
        </div>
    </Collapse>
</template>
<script setup lang="ts">
import Collapse from '../common/collapse.vue'
import SearchEx from '../common/search-ex.vue'
import { Transformer } from 'markmap-lib'
import { Markmap } from 'markmap-view'
import { useDark } from '@vueuse/core'
import { copy } from '@/utils/util'

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

const transformer = new Transformer()

const svgRef = shallowRef<SVGElement>()
const svgWrapRef = shallowRef<HTMLDivElement>()
const isDark = useDark()
const isFullScreen = ref(false)
let markmap: Markmap | null = null

const transformQuote = (value: string) => {
    const pattern =
        /(`{3}[\s\S]*?`{3}(?:(?!.)))|(`{3}[\s\S]*)|(`[\s\S]*?`{1}?)|(`[\s\S]*)|(?:\[(?:(?:number )|\^)?([\d]{1,2})\])/g

    return value.replaceAll(pattern, function ($1, $2, $3, $4, $5, $6) {
        // console.log($1, $2, $3, $4, $5, $6)
        const item = props.quote[Number($6) - 1]
        if (item) {
            return `<a href="${item.seeMoreUrl}" 
title="${item.title}"
target="_blank"
style="display: inline-block;
width: 15px;
height: 15px;
border-radius: 50%;
font-size: 12px;
text-align: center;
background-color: var(--el-fill-color-lighter);
text-align: center;
font-size: 9px;
color:var(--el-text-color-secondary);
text-decoration: none !important;
vertical-align: middle;
margin: 0 2px 3px;
cursor: pointer;
line-height: 16px;">${$6}</a>`
        } else {
            return ''
        }
    })
}
const renderMarkMap = (value: string) => {
    value = transformQuote(value)
    const { root } = transformer.transform(value)
    markmap?.setData(root)
    markmap?.fit()
}

watch(
    isDark,
    (value) => {
        if (value) {
            document.documentElement.classList.add('markmap-dark')
        } else {
            document.documentElement.classList.remove('markmap-dark')
        }
    },
    {
        immediate: true
    }
)

watch(() => props.content, renderMarkMap)
onMounted(async () => {
    await nextTick()

    markmap = Markmap.create(svgRef.value!)

    renderMarkMap(props.content)
})

watch(isFullScreen, async () => {
    await nextTick()
    markmap?.fit()
})
</script>

<style lang="scss" scoped>
.toolbar {
    position: absolute;
    top: 24px;
    right: 24px;
    display: flex;
    .toolbar-item {
        padding: 6px;
    }
}
</style>
