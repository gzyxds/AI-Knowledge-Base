<template>
    <div class="bg-page overflow-hidden flex items-center input-select">
        <div class="flex-none flex px-[8px]">
            <SearchModel
                mode="dropdown"
                v-model:model="options.model"
                v-model:type="options.type"
            />
        </div>

        <el-input
            v-model="options.ask"
            placeholder="输入你想搜索的问题"
            @keydown.enter="launchSearch()"
        />
        <SearchBtn @click="launchSearch()" />
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import SearchModel from '../common/search-model.vue'
import SearchBtn from '../common/search-btn.vue'
import { useSearch } from '../../useSearch'

const { options, launchSearch } = useSearch()
</script>

<style lang="scss" scoped>
.input-select {
    border-radius: 7px;
    padding: 2px;
    :deep() {
        .el-input__wrapper {
            box-shadow: none;
            --el-input-bg-color: transparent;
            padding: 0;
        }
    }
}
</style>
