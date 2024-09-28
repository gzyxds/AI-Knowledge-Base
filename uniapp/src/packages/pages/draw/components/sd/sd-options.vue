<template>
    <u-collapse>
        <u-collapse-item title="高级参数" :key="'index'" :open="show">
            <view class="flex-1 min-w-0 p-[20rpx] bg-white rounded-lg">
                <!-- 绘画步数 -->
                <view class="text-base text-main">
                    <item-title title="绘画步数" tips="越低：细节简练，耗时更短；越高：细节丰富，耗时变长；注*步数过高可能产生细节扭曲" font-weight="400"></item-title>
                    <view class="flex items-center justify-between mt-[10rpx]">
                        <view class="w-[480rpx]">
                            <app-slider
                                :min="0"
                                :max="150"
                                :step="1"
                                :showValue="false"
                                :activeColor="$theme.primaryColor"
                                v-model="params.step"
                            ></app-slider>
                        </view>
                        <input
                            class="options-input"
                            type="number"
                            :min="0"
                            :max="150"
                            style="width: 80rpx"
                            step="1"
                            v-model="params.step"
                            @blur="limitStepValue"
                        />
                    </view>
                </view>
                <!-- 文本强度 -->
                <view class="text-base text-main mt-2">
                    <item-title title="文本强度" tips="低：淡化输入的特征，淡化风格；高：强化输入的特征，强化风格；最佳使用区间7-12，推荐不超过15" font-weight="400"></item-title>
                    <view class="flex items-center justify-between mt-[10rpx]">
                        <view class="w-[480rpx]">
                            <app-slider
                                :min="0"
                                :max="30"
                                :step="0.5"
                                :showValue="false"
                                :activeColor="$theme.primaryColor"
                                v-model="params.cfg_scale"
                            ></app-slider>
                        </view>
                        <input
                            class="options-input"
                            type="number"
                            :min="0"
                            :max="30"
                            style="width: 80rpx"
                            step="0.5"
                            v-model="params.cfg_scale"
                            @blur="limitCfgValue"
                        />
                    </view>
                </view>
                <!-- 采样模式 -->
                <view class="text-base text-main mt-2">
                    <item-title title="采样模式" tips="靠前的采样（euler）：适合动漫，细节简练，快速；靠后的采样（DPM）：适合写实，细节丰富，较慢" font-weight="400"></item-title>
                    <view class="flex-1 mt-[10rpx]">
                        <app-select
                            v-model="params.sampler_name"
                            popupTitle="采样器"
                            :dataLists="samplers"
                            placeholder="请选择"
                            :closeable="false"
                            name="name"
                            value="name"
                        >
                        </app-select>
                    </view>
                </view>
                <!-- 随机种子 -->
                <view class="text-base text-main mt-2">
                    <item-title title="随机种子" tips="每次生成图的初始画布，种子、提示词、参数和模型相同的情况下，可复原绘画结果" font-weight="400"></item-title>
                    <view class="flex-1 mt-[10rpx]">
                        <input
                            class="options-input"
                            type="number"
                            v-model="params.seed"
                        />
                    </view>
                </view>
            </view>
        </u-collapse-item>
    </u-collapse>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import { samplersItem } from '@/api/draw'
import ItemTitle from '../item-title.vue'
import { ref } from 'vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        samplers: samplersItem[]
    }>(),
    {
        modelValue: () => {},
        samplers: () => []
    }
)
const show = ref<boolean>(true)
const { modelValue: params } = useVModels(props, emit)


const limitStepValue = (e: any) => {
    const value = parseFloat(e.target.value)
    if (value >= 150) {
        params.value.step = 150
    } else if (value < 0) {
        params.value.step = 0
    } else {
        params.value.step = value
    }
}

const limitCfgValue = (e: any) => {
    const value = parseFloat(e.target.value)
    if (value >= 30) {
        params.value.cfg_scale = 30
    } else if (value < 0) {
        params.value.cfg_scale = 0
    } else {
        params.value.cfg_scale = value
    }
}
</script>

<style lang="scss" scoped>
.options-input {
    height: 64rpx;
    padding: 0 20rpx;
    border-radius: 14rpx;
    border: 1px solid #DCDFE6;
}
</style>