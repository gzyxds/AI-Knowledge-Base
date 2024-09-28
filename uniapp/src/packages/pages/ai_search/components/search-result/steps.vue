<template>
    <u-steps
        :list="stepsOptions"
        :current="searchStore.result.status"
        type=" primary"
    ></u-steps>
</template>

<script setup lang="ts">
import { TypeEnums, useSearch } from '../../useSearch'
import { computed } from 'vue'
const searchStore = useSearch()

const getTypeText = computed(() => {
    switch (searchStore.options.type) {
        case TypeEnums.ALL:
            return '全网'
        case TypeEnums.DOC:
            return '文档'
        case TypeEnums.SCHOLAR:
            return '学术'
        default:
            return ''
    }
})
const stepsOptions = computed(() => {
    return [
        {
            name: '问题分析'
        },
        {
            name: `${getTypeText.value}搜索`
        },
        {
            name: '整理答案'
        },
        {
            name: '完成'
        }
    ]
})
</script>
