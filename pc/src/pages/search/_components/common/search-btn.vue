<template>
    <el-button
        type="primary"
        :style="{
            padding: '8px'
        }"
    >
        <template #icon>
            <Icon name="el-icon-Search" />
        </template>
        搜索
        <span class="text-xs ml-1">
            <template v-if="config.isVipFree"> 会员免费 </template>
            <template v-else-if="config.price > 0">
                -{{ config.price }}{{ appStore.getTokenUnit }}
            </template>
        </span>
    </el-button>
</template>

<script lang="ts" setup>
import { useSearch } from '../../useSearch'
import { useAppStore } from '@/stores/app'
const { config } = useSearch()
const appStore = useAppStore()
</script>
