<template>
    <view class="h-full">
        <SearchResult v-if="searchStore.showResult" class="h-full" />
        <SearchAsk v-else class="h-full" />
        <!-- <u-empty text="移动端暂时不支持，请前往PC端" mode="list"></u-empty> -->
    </view>
</template>
<script setup lang="ts">
import SearchAsk from './search-ask/index.vue'
import SearchResult from './search-result/index.vue'
import { ModelEnums, TypeEnums, useSearch } from '../useSearch'
import { watch, onBeforeUnmount } from 'vue'
const searchStore = useSearch()

watch(
    () => searchStore.options.model,
    (value) => {
        if (value !== ModelEnums.STUDY) {
            searchStore.options.type = TypeEnums.ALL
        }
    },
    { flush: 'post', immediate: true }
)
</script>
