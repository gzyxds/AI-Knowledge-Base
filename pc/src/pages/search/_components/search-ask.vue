<template>
    <div class="h-full p-4">
        <ElScrollbar class="scroll-bar bg-body rounded-[15px]">
            <div class="flex-1 flex flex-col items-center pt-[80px] px-[40px]">
                <div class="text-center text-[50px] title font-bold relative">
                    <h2 class="title text-primary">快搜准答</h2>
                    <h2 class="title">懂你更懂世界</h2>
                    <img
                        class="w-[125px] h-[125px] absolute top-[50px] right-[-60px]"
                        src="@/assets/image/ai_search.png"
                    />
                </div>

                <div class="pt-[30px]">
                    <SearchModel v-model:model="options.model" />
                </div>
                <div class="w-[560px]">
                    <SearchInput
                        mode="textarea"
                        class="mt-[30px]"
                        v-model:input="options.ask"
                        v-model:type="options.type"
                        :model="options.model"
                        @search="launchSearch"
                    />
                    <SearchEx
                        class="pt-[30px] justify-center"
                        :lists="searchEx"
                        @click-item="launchSearch"
                    />
                </div>
            </div>
        </ElScrollbar>
    </div>
</template>

<script setup lang="ts">
import SearchEx from './/common/search-ex.vue'
import SearchModel from './/common/search-model.vue'
import SearchInput from './/common/search-input.vue'
import { useSearch, useSearchEx } from '../useSearch'

const { options, launchSearch } = useSearch()
const { searchEx, getSearchEx } = useSearchEx()

getSearchEx()
</script>

<style lang="scss" scoped>
.scroll-bar {
    :deep() {
        .el-scrollbar__view {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }
    }
    .title {
        font-family: SimHei;
    }
}
</style>
