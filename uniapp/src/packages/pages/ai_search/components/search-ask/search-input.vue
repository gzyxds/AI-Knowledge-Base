<template>
    <view class="bg-white rounded-[18rpx] px-[20rpx] py-[10rpx]">
        <view>
            <u-input v-model="inputModel" type="textarea" :border="false" />
        </view>
        <view class="flex items-center justify-between">
            <view class="">
                <SearchType v-model:type="typeModel" :model="model">
                    <template #default="{ icon, label }">
                        <view class="flex items-center">
                            <zui-svg-icon
                                width="28rpx"
                                :icon="icon"
                                color="#333"
                            />
                            <span class="px-[12rpx]">
                                {{ label }}
                            </span>
                            <u-icon name="arrow-down" />
                        </view>
                    </template>
                </SearchType>
            </view>
            <SearchBtn @click="$emit('search')" />
        </view>
    </view>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import SearchType from '../common/search-type.vue'
import SearchBtn from '../common/search-btn.vue'
const props = defineProps<{
    mode: 'input' | 'textarea'
    model: string
    type: string
    input: string
}>()
const emit = defineEmits<{
    (event: 'update:model', value: string): void
    (event: 'update:input', value: string): void
    (event: 'search'): void
}>()

const { type: typeModel, input: inputModel } = useVModels(props, emit)
</script>
