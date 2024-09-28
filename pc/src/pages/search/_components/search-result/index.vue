<template>
    <div class="h-full p-4">
        <div class="h-full bg-body rounded-[15px] flex flex-col">
            <div class="p-4">
                <div class="flex items-center mb-4">
                    <div
                        class="flex bg-body p-[5px] text-bold rounded-[50%] text-primary shadow-light cursor-pointer"
                        @click="showSearchResult = false"
                    >
                        <Icon name="el-icon-Back" :size="18" />
                    </div>
                    <div class="text-xl flex-1 min-w-0 ml-[10px]">AI搜索</div>
                </div>

                <InputSelect class="w-[550px]" />
            </div>
            <div class="flex-1 min-h-0">
                <div class="h-full flex">
                    <div class="flex-[2.7] h-full">
                        <ElScrollbar>
                            <div
                                class="py-[10px]"
                                v-if="result.status < StatusEnums.SUCCESS"
                            >
                                <Steps v-if="result.query" />
                            </div>
                            <div class="pl-4 pr-[40px]">
                                <div v-if="result.query">
                                    <div
                                        class="break-words text-3xl font-medium"
                                    >
                                        {{ result.query }}
                                    </div>
                                    <div class="pt-1 text-tx-secondary text-xs">
                                        内容由 AI 生成，不能保证真实
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <el-skeleton
                                        v-if="
                                            result.status < StatusEnums.SUMMARY
                                        "
                                        :rows="5"
                                    />
                                </div>

                                <div
                                    v-for="(item, index) in result.data"
                                    :key="index"
                                    class="mt-4"
                                >
                                    <div
                                        v-if="
                                            item.type === 'markdown' ||
                                            item.type === 'expand_query'
                                        "
                                    >
                                        <Markdown
                                            :link-list="
                                                result.data[index - 1]?.content
                                            "
                                            :content="item.content"
                                            :typing="item.target === 'update'"
                                        />
                                    </div>
                                    <div v-if="item.type === 'suggestion'">
                                        <ActionBtns />
                                        <Suggestion
                                            v-if="item.content.length"
                                            class="mt-4"
                                            :lists="item.content"
                                        />
                                        <MindMap
                                            v-if="result.outline.text"
                                            class="mt-4"
                                            :content="result.outline.text"
                                            :quote="
                                                result.outline
                                                    .source_attributions
                                            "
                                        />
                                        <Outline
                                            v-if="result.outline_json.text"
                                            class="mt-4"
                                            :content="result.outline_json.text"
                                            :quote="
                                                result.outline_json
                                                    .source_attributions
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </ElScrollbar>
                    </div>
                    <div class="flex-1 h-full">
                        <ElScrollbar>
                            <div class="px-4">
                                <div class="" v-if="result.search.length">
                                    <div class="text-xl font-medium">
                                        信息来源
                                    </div>
                                    <div class="mt-4">
                                        <Doc />
                                    </div>
                                </div>
                            </div>
                        </ElScrollbar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import InputSelect from './input-select.vue'
import Steps from './steps.vue'
import Doc from './doc.vue'
import Suggestion from './suggestion.vue'
import ActionBtns from './action-btns.vue'
import MindMap from './mind-map.vue'
import Outline from './outline.vue'
import { useSearch } from '../../useSearch'
import { StatusEnums } from '../../searchEnums'
import { onBeforeUnmount } from 'vue'

const { result, abortSearch, showSearchResult } = useSearch()

onBeforeUnmount(() => {
    abortSearch()
})
</script>
