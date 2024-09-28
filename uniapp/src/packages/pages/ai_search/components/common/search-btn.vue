<template>
    <view>
        <u-button
            type="primary"
            size="medium"
            :customStyle="{
                height: '68rpx'
            }"
        >
            <u-icon name="search" />

            <span class="text-xs ml-[8rpx]">
                <template v-if="searchStore.config.isVipFree">
                    会员免费
                </template>
                <template v-else-if="searchStore.config.price > 0">
                    -{{ searchStore.config.price }}{{ appStore.getTokenUnit }}
                </template>
            </span>
        </u-button>
    </view>
</template>

<script lang="ts" setup>
import { useSearch } from '../../useSearch'
import { useAppStore } from '@/stores/app'
const appStore = useAppStore()
const searchStore = useSearch()
</script>
