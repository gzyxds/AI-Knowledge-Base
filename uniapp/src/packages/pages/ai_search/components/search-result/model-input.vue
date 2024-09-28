<template>
    <view class="flex bg-white items-center rounded-[12rpx]">
        <SearchModel
            mode="dropdown"
            v-model:type="searchStore.options.type"
            v-model:model="searchStore.options.model"
        />
        <view class="flex-1 ml-[20rpx] min-w-0">
            <u-input
                v-model="searchStore.options.ask"
                placeholder="输入你想搜索的问题"
            />
        </view>
        <view class="p-[4rpx]">
            <u-button
                type="primary"
                size="medium"
                :customStyle="{
                    height: '68rpx'
                }"
                @click="searchStore.launchSearch()"
            >
                <u-icon name="search" />
            </u-button>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import SearchModel from '../common/search-model.vue'
import SearchBtn from '../common/search-btn.vue'
import { useSearch } from '../../useSearch'

const searchStore = useSearch()
</script>
