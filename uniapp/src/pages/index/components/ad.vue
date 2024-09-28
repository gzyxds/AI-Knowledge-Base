<template>
    <view v-if="isHidden" class="mx-[30rpx] mt-[30rpx]">
        <view
            class="grid gap-y-3 gap-x-[30rpx]"
            :style="{
                'grid-template-columns': `repeat(${ prop.showType }, 1fr)`
            }"
        >
            <view
                v-for="(item, index) in adList"
                :key="index"
                class="flex flex-col items-center min-w-[112rpx] bg-white rounded-[20rpx] overflow-hidden"
                @click="handleClick(item.link)"
            >
                <u-image
                    class="w-full"
                    :width="prop.showType == 2 ? 330 : 210"
                    :height="prop.showType == 2 ? 236 : 148"
                    :src="getImageUrl(item.image)"
                />
                <view
                    class="w-full pb-[30rpx] pt-[24rpx] px-[20rpx] text-center"
                >
                    <view class="text-xl line-clamp-1 font-medium">{{
                        item.title
                    }}</view>
                    <view class="text-content line-clamp-1 mt-[5px] text-sm">
                        {{ item.desc }}
                    </view>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { navigateTo } from '@/utils/navigate'
import { sliceArray } from '@/utils/util'
import { useAppStore } from '@/stores/app'

const { getImageUrl } = useAppStore()
const props = defineProps<{
    isHidden: boolean
    prop: Record<string, any>
}>()

const adList = ref<Record<string, any>>([])

watch(
    () => props.prop.data,
    (val) => {
        adList.value = val.filter((item: any) => item.isShow * 1)
    },
    { deep: true, immediate: true }
)

const handleClick = (link: any) => {
    if (!link) return
    navigateTo(link)
}
</script>
