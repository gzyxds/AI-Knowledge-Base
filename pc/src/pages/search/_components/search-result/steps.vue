<template>
    <el-steps
        :active="result.status"
        style="max-width: 500px"
        align-center
        process-status="finish"
    >
        <el-step
            v-for="(item, index) in stepsOptions"
            :key="index"
            :title="item.title"
        >
            <template #icon>
                <Icon
                    v-if="result.status === index"
                    name="el-icon-Loading"
                    style="animation: loading-rotate 2s linear infinite"
                    :size="24"
                />
                <Icon
                    v-else-if="result.status > index"
                    name="el-icon-SuccessFilled"
                    :size="24"
                />
                <Icon v-else name="local-icon-circular" :size="24" />
            </template>
        </el-step>
    </el-steps>
</template>

<script setup lang="ts">
import { useSearch } from '../../useSearch'
const { options, result } = useSearch()
import { TypeEnums } from '../../searchEnums'

const getTypeText = computed(() => {
    switch (options.value.type) {
        case TypeEnums.ALL:
            return '全网'
        case TypeEnums.DOC:
            return '文档'
        case TypeEnums.SCHOLAR:
            return '学术'
    }
})
const stepsOptions = computed(() => {
    return [
        {
            title: '问题分析'
        },
        {
            title: `${getTypeText.value}搜索`
        },
        {
            title: '整理答案'
        },
        {
            title: '完成'
        }
    ]
})
</script>
