<template>
    <view class="mt-4">
        <item-title title="模型选择" tips="在基础模型上叠加微调模型，让画面更细腻更可控"></item-title>
        <u-form-item label="">
            <view class="flex-1 min-w-0 p-[20rpx] bg-white rounded-lg">
                <view class="flex flex-wrap" v-if="currentLoraLists.length">
                    <view
                        v-for="item in currentLoraLists"
                        class="w-[25%] px-[10rpx] mb-[10rpx]"
                        :key="item.id"
                    >
                        <view
                            class="h-full"
                            @click="selectModel(item.model_name)"
                        >
                            <view
                                class="pt-[100%] relative h-0 rounded-[10rpx] overflow-hidden"
                            >
                                <view class="absolute inset-0 left-0 top-0">
                                    <u-image
                                        :src="item.cover"
                                        class="h-full w-full"
                                        width="100%"
                                        height="100%"
                                    />
                                </view>
                                <view
                                    v-if="lora.includes(
                                    item.model_name
                                )"
                                    class="absolute bg-[rgba(0,0,0,0.5)] inset-0 left-0 top-0 flex items-center justify-center text-white"
                                >
                                    <u-icon
                                        name="checkmark-circle-fill"
                                        :size="40"
                                    />
                                </view>
                            </view>
                            <view class="text-center text-xs line-clamp-1">
                                {{ item.title }}
                            </view>
                        </view>
                    </view>
                </view>
                <view v-else class="flex flex-col items-center justify-center h-full">
                    <image
                        class="w-[200rpx] h-[200rpx]"
                        :src="EmptyImage"
                    >
                    </image>
                    <text class="mt-4 text-content">暂无关联的微调模型</text>
                </view>
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import EmptyImage from '../../../../static/images/empty.png'
import {useVModels} from '@vueuse/core'
import {ref, watch} from 'vue'
import {loraItem, ModelItem} from '@/api/draw'
import ItemTitle from '../item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'changeCate', value: number): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        currentLora: string
        modelList: ModelItem[]
    }>(),
    {
        modelValue: () => []
    }
)
const {modelValue: lora} = useVModels(props, emit)
// 当前的lora模型列表
const currentLoraLists = ref<loraItem[]>([])

watch(
    () => props.currentLora,
    (value: string) => {
        currentLoraLists.value = props.modelList.find((item: ModelItem) => {
            return item.model_name === value
        })?.loras || []

        if (!lora.value.len) {
            lora.value = []
            return
        }
        currentLoraLists.value.map((item: loraItem) => {
            if (!lora.value.includes(item.model_name)) {
                lora.value = lora.value?.filter((ctem: string) => ctem !== item.model_name)
            }
        })
    },
    {immediate: true, deep: false}
)

const selectModel = (name: string) => {
    if (!lora.value.includes(name)) {
        lora.value.push(name);
    } else {
        lora.value = lora.value.filter((item: string) => item !== name)
    }
}
</script>
