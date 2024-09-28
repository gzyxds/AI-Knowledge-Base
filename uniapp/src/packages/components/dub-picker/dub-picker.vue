<template>
    <view class="inline-flex items-center">
        <view class="relative w-[500rpx]">
            <DubItem
                closeable
                :value="current.name || keyModel"
                :url="current.example"
                placeholder="请选择配音"
                @close="keyModel = ''"
            />
        </view>

        <view class="ml-[20rpx]" @click="show = true">
            <u-button
                type="primary"
                plain
                :customStyle="{ height: '70rpx', lineHeight: '70rpx' }"
            >
                选择配音
            </u-button>
        </view>
        <u-popup
            v-model="show"
            safe-area-inset-bottom
            closeable
            border-radius="16"
            mode="bottom"
        >
            <view class="h-[70vh] flex flex-col">
                <view
                    class="text-xl mx-[20rpx] py-[28rpx] font-bold border-b border-solid border-light border-0"
                >
                    选择配音
                </view>
                <view class="flex-1 min-h-0">
                    <scroll-view class="h-full" scroll-y>
                        <view class="flex flex-wrap mx-[-10rpx] p-[20rpx]">
                            <view
                                class="w-[50%] mb-[20rpx] px-[10rpx]"
                                v-for="(item, value) in dubList"
                                :key="value"
                            >
                                <view
                                    class="w-full"
                                    @click="currentKey = value"
                                >
                                    <DubItem
                                        :is-active="value == currentKey"
                                        :value="item.name"
                                        :url="item.example"
                                    />
                                </view>
                            </view>
                        </view>
                    </scroll-view>
                </view>
                <view class="p-[20rpx]">
                    <u-button type="primary" @click="handleConfirm">
                        确定
                    </u-button>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { getDubbingList } from '@/api/digital'
import { useVModel } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import DubItem from './item.vue'
const props = defineProps<{
    modelValue: string
}>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const currentKey = ref('')
const show = ref(false)
const keyModel = useVModel(props, 'modelValue', emit)
const dubList = ref<Record<string, any>>({})
const getData = async () => {
    dubList.value = await getDubbingList()
}
const current = computed(() => {
    return dubList.value[keyModel.value] || {}
})

const handleConfirm = () => {
    keyModel.value = currentKey.value
    show.value = false
}

getData()

watch(show, () => {
    currentKey.value = props.modelValue
})
</script>
