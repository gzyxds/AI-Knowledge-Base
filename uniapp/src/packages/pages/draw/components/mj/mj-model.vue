<template>
    <view class="mt-4">
        <item-title
            title="模型选择"
            tips="让AI根据此模型的风格绘制图片，修改合适的描述词和参数可以让生成效果更加精美"
            required
        ></item-title>
        <u-form-item label="">
            <view class="flex-1 min-w-0 pt-[20rpx] px-[20rpx] bg-white rounded-lg overflow-hidden">
                <view class="flex flex-wrap" v-if="modelList.length">
                    <template v-for="(item, index) in modelList" :key="item.cover">
                        <view
                            class="w-[50%] px-[10rpx]">
                            <view
                                class="h-full"
                                @click="selectModel(item.value)"
                            >
                                <view
                                    class="pt-[70%] relative h-0 rounded-[10rpx] overflow-hidden"
                                >
                                    <view class="absolute inset-0 left-0 top-0">
                                        <u-image
                                            :src="item.cover"
                                            class="h-full w-full"
                                            width="100%"
                                            mode="widthFix"
                                        />
                                    </view>
                                    <view
                                        v-if="model === item.value"
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
                    </template>
                </view>
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import {useVModels} from '@vueuse/core'
import ItemTitle from '../item-title.vue'
import config from '@/config'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'changeCate', value: number): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)
const {modelValue: model} = useVModels(props, emit)

// #ifdef H5
const domain = window.origin + '/'
// #endif
// #ifndef H5
const domain = config.baseUrl
// #endif
const modelList = [
    {
        value: 'mj',
        title: '真实感强',
        cover: domain + 'resource/image/api/draw/mj.png'
    },
    {
        value: 'niji',
        title: '卡通动漫',
        cover: domain + 'resource/image/api/draw/nj.png'
    }
]

const selectModel = (name: string) => {
    model.value = name
}
</script>
