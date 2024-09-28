<template>
    <u-collapse>
        <u-collapse-item open>
            <template #title>
                <view class="flex items-center">
                    <zui-svg-icon
                        width="32rpx"
                        icon="mind_map"
                        :color="'#333'"
                    />
                    <span class="text-2xl ml-1 font-bold"> 脑图 </span>
                </view>
            </template>
            <div class="h-[710rpx] relative">
                <div
                    ref="svgWrapRef"
                    class="w-full h-full"
                    :class="{
                        '!fixed top-0 left-0 w-screen h-screen z-[9999] bg-white':
                            isFullScreen
                    }"
                >
                    <div class="toolbar">
                        <div class="toolbar-item">
                            <view
                                class="flex"
                                v-if="isFullScreen"
                                @click="isFullScreen = false"
                            >
                                <zui-svg-icon
                                    icon="fullscreen-exit"
                                    color="#fff"
                                    width="36rpx"
                                />
                            </view>
                            <view
                                v-else
                                class="flex"
                                @click="isFullScreen = true"
                            >
                                <zui-svg-icon
                                    icon="fullscreen"
                                    color="#fff"
                                    width="36rpx"
                                />
                            </view>
                        </div>
                        <div class="toolbar-item">
                            <view class="flex" @click="markmap?.fit()">
                                <u-icon name="reload" size="38" color="#fff" />
                            </view>
                        </div>
                        <div class="toolbar-item">
                            <view class="flex" @click="markmap?.rescale(1.25)">
                                <u-icon
                                    name="plus-circle"
                                    size="38"
                                    color="#fff"
                                />
                            </view>
                        </div>
                        <div class="toolbar-item">
                            <view class="flex" @click="markmap?.rescale(0.8)">
                                <u-icon
                                    name="minus-circle"
                                    size="38"
                                    color="#fff"
                                />
                            </view>
                        </div>
                        <div class="toolbar-item">
                            <view class="flex" @click="copy(content)">
                                <u-icon
                                    name="file-text"
                                    size="38"
                                    color="#fff"
                                />
                            </view>
                        </div>
                    </div>
                    <svg ref="svgRef" class="w-full h-full"></svg>
                </div>
            </div>
        </u-collapse-item>
    </u-collapse>
</template>
<script setup lang="ts">
import { Transformer } from 'markmap-lib'
import { Markmap } from 'markmap-view'
import { useDark } from '@vueuse/core'
import { useCopy } from '@/hooks/useCopy'
import { shallowRef, ref, watch, onMounted, nextTick } from 'vue'
const { copy } = useCopy()
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
text-align: center;
color: #485568 !important;
background-color: #edeff1 !important;
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
    background-color: rgba(0, 0, 0, 0.6);
    position: absolute;
    bottom: 20rpx;
    right: 20rpx;
    display: flex;
    align-items: center;
    z-index: 999;
    border-radius: 10rpx;
    .toolbar-item {
        padding: 6px;
    }
}
</style>
