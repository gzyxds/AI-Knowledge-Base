<template>
    <scroll-view class="h-full search-ask" scroll-y>
        <view class="flex flex-col items-center px-[20rpx]">
            <Title />
            <SearchModel v-model:model="searchStore.options.model" />
            <SearchInput
                v-model:input="searchStore.options.ask"
                :model="searchStore.options.model"
                class="w-full mt-[30rpx]"
                v-model:type="searchStore.options.type"
                @search="searchStore.launchSearch()"
            />
            <SearchEx
                class="mt-[30rpx] justify-center"
                :lists="searchStore.searchEx"
                @click-item="searchStore.launchSearch"
            />
        </view>
    </scroll-view>
</template>
<script setup lang="ts">
import Title from './title.vue'
import SearchModel from '../common/search-model.vue'
import SearchInput from './search-input.vue'
import SearchEx from '../common/search-ex.vue'
import { useSearch } from '../../useSearch'

const searchStore = useSearch()
searchStore.getSearchEx()
searchStore.getConfig()
</script>

<style lang="scss" scoped>
.search-ask {
    background: url('../../../../static/images/ai_search_bg.png') no-repeat;
    background-size: 100% auto;
}
</style>
