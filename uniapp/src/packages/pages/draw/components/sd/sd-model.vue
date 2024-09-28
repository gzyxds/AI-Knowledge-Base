<template>
    <view class="mt-4">
        <item-title title="模型选择" tips="让AI根据此模型的风格绘制图片，修改合适的描述词和参数可以让生成效果更加精美" required></item-title>
        <u-form-item label="">
            <view class="flex-1 min-w-0 p-[20rpx] bg-white rounded-lg overflow-hidden">
                <scroll-view class="text-sm whitespace-nowrap" scroll-x>
                    <view
                        v-for="(item, index) in cateList"
                        :key="index"
                        class="inline-block bg-page rounded-full mr-[30rpx] px-[40rpx] text-main"
                        :class="{ '!bg-primary !text-white': index === cateIndex }"
                        @click.stop="throttledSelectCate(index)"
                    >
                        {{ item.label }}
                    </view>
                </scroll-view>

                <swiper
                    class="h-[480rpx]"
                    circular
                    :current="cateIndex"
                    :indicator-dots="false"
                    @change="throttledSelectCate($event)"
                >
                    <swiper-item v-for="(i, index) in cateList" :key="index">
                        <scroll-view
                            scroll-y="true"
                            class="h-[480rpx] pt-[20rpx] mx-[-10rpx]"
                        >
                            <view class="flex flex-wrap" v-if="modelList.length">
                                <view
                                    v-for="item in modelList"
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
                                                v-if="model === item.model_name"
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
                                <text class="mt-4 text-content">暂无相关模型</text>
                            </view>
                        </scroll-view>
                    </swiper-item>
                </swiper>
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import {ref} from 'vue'
import {ModelItem} from '@/api/draw'
import {isNumber, useVModels, useThrottleFn} from '@vueuse/core'
import EmptyImage from '../../../../static/images/empty.png'
import ItemTitle from '../item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'changeCate', value: number): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        cateList: any[]
        modelList: ModelItem[]
    }>(),
    {
        modelValue: () => []
    }
)
const {modelValue: model} = useVModels(props, emit)

const cateIndex = ref<number>(0)

const selectCate =(value: number | any) => {
    const index = isNumber(value) ? value : value.detail.current
    if (cateIndex.value === index) return
    cateIndex.value = index
    emit('changeCate', props.cateList[index].value)
}
const throttledSelectCate = useThrottleFn(selectCate, 50)

const selectModel = (name: string) => {
    model.value = name
}
</script>
