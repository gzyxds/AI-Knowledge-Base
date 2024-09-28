<template>
    <Collapse>
        <template #title>
            <Icon name="el-icon-Search" :size="16" />
            <span class="text-2xl ml-1"> 相关问题 </span>
        </template>
        <SearchEx :lists="lists" prop="text" @click-item="launchSearch" />
    </Collapse>
</template>
<script setup lang="ts">
import Collapse from '../common/collapse.vue'
import SearchEx from '../common/search-ex.vue'
import { useSearch } from '../../useSearch'

const props = withDefaults(
    defineProps<{
        lists: any[]
    }>(),
    {
        lists: () => []
    }
)
const { launchSearch, result } = useSearch()
</script>

<style lang="scss" scoped></style>
