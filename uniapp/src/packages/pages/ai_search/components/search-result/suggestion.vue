<template>
    <u-collapse>
        <u-collapse-item open>
            <template #title>
                <view class="flex items-center">
                    <u-icon name="search" :size="32" />
                    <span class="text-2xl ml-1 font-bold"> 相关问题 </span>
                </view>
            </template>
            <SearchEx
                class="mt-[10rpx]"
                :lists="lists"
                prop="text"
                @click-item="searchStore.launchSearch"
            />
        </u-collapse-item>
    </u-collapse>
</template>
<script setup lang="ts">
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
const searchStore = useSearch()
</script>

<style lang="scss" scoped></style>
