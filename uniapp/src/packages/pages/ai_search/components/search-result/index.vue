<template>
    <view class="h-full flex flex-col">
        <u-navbar
            back-text="AI搜索"
            :custom-back="() => (searchStore.showResult = false)"
        ></u-navbar>
        <view class="p-[20rpx]">
            <ModelInput />
        </view>
        <view class="flex-1 min-h-0">
            <scroll-view class="h-full bg-white" scroll-y>
                <view class="px-[20rpx] pb-[20rpx]">
                    <Steps
                        class="pt-[20rpx]"
                        v-if="searchStore.result.status < StatusEnums.SUCCESS"
                    />
                    <view class="pt-[20rpx]" v-if="searchStore.result.query">
                        <view class="break-words text-[36rpx] font-medium">
                            {{ searchStore.result.query }}
                        </view>
                        <view class="pt-1 text-tx-secondary text-xs">
                            内容由 AI 生成，不能保证真实
                        </view>
                    </view>
                    <view class="mt-[20rpx]">
                        <uv-skeletons
                            v-if="
                                searchStore.result.status < StatusEnums.SUMMARY
                            "
                            :loading="true"
                            :skeleton="[
                                {
                                    type: 'line',
                                    num: 5,
                                    gap: '20rpx'
                                }
                            ]"
                        ></uv-skeletons>
                    </view>
                    <view
                        v-for="(item, index) in searchStore.result.markdown"
                        :key="index"
                        class="mt-[20rpx]"
                    >
                        <div
                            v-if="
                                item.type === 'markdown' ||
                                item.type === 'expand_query'
                            "
                        >
                            <ua-markdown
                                :link-list="
                                    searchStore.result.markdown[index - 1]
                                        ?.content
                                "
                                :content="item.content"
                            />
                        </div>
                    </view>

                    <view
                        class="mt-[20rpx]"
                        v-if="searchStore.result.suggestion.type"
                    >
                        <ActionBtns />
                        <Suggestion
                            class="mt-[20rpx]"
                            v-if="searchStore.result.suggestion.data?.length"
                            :lists="searchStore.result.suggestion.data"
                        />
                        <MindMap
                            v-if="searchStore.result.outline.text"
                            class="mt-[20rpx]"
                            :content="searchStore.result.outline.text"
                            :quote="
                                searchStore.result.outline.source_attributions
                            "
                        />
                        <Outline
                            v-if="searchStore.result.outlineJson.text"
                            class="mt-[20rpx]"
                            :content="searchStore.result.outlineJson.text"
                            :quote="
                                searchStore.result.outlineJson
                                    .source_attributions
                            "
                        />
                        <Doc
                            v-if="searchStore.result.search.length"
                            :list="searchStore.result.search"
                        />
                    </view>
                </view>
            </scroll-view>
        </view>
    </view>
</template>
<script setup lang="ts">
import { useSearch, StatusEnums } from '../../useSearch'
import ModelInput from './model-input.vue'
import Steps from './steps.vue'
import ActionBtns from './action-btns.vue'
import Suggestion from './suggestion.vue'
import MindMap from './mind-map.vue'
import Outline from './outline.vue'
import Doc from './doc.vue'
import { onBeforeUnmount } from 'vue'
const searchStore = useSearch()
onBeforeUnmount(() => {
    searchStore.abort()
    searchStore.$reset()
})
</script>
