<template>
    <u-collapse>
        <u-collapse-item title="高级参数" :key="'index'" :open="show">
            <view class="flex-1 min-w-0 p-[20rpx] bg-white rounded-lg">
                <!-- 参考图权重 -->
                <view class="text-base text-main">
                    <item-title title="参考图权重" tips="设置生成图片时垫图的权重，值越大越像垫图，取值范围0.5-2， 默认值1" font-weight="400"></item-title>
                    <view class="flex items-center justify-between mt-[10rpx]">
                        <view class="w-[480rpx]">
                            <app-slider
                                :step="0.1"
                                :min="0.5"
                                :max="2"
                                :showValue="false"
                                :activeColor="$theme.primaryColor"
                                v-model="params.iw"
                            ></app-slider>
                        </view>
                        <input
                            class="options-input"
                            type="number"
                            :step="0.1"
                            :min="0.5"
                            :max="2"
                            style="width: 80rpx"
                            v-model="params.iw"
                            @blur="limitStepValue"
                        />
                    </view>
                </view>
                <!-- 图片质量 -->
                <view class="text-base text-main mt-2">
                    <item-title title="图片质量" tips="设置图片的质量，越大质量越高，取值范围0.3-5，默认值1" font-weight="400"></item-title>
                    <view class="flex-1 mt-[10rpx]">
                        <app-select
                            v-model="params.q"
                            popupTitle="图片质量"
                            :dataLists="formattedQList"
                            placeholder="请选择"
                            :closeable="false"
                            name="label"
                            value="value"
                        >
                        </app-select>
                    </view>
                </view>
                <!-- 风格化值 -->
                <view class="text-base text-main mt-2">
                    <item-title title="风格化值" tips="设置生成图片时的风格化程度，值越大，风格化的程度越高，取值范围0-1000， 默认值100" font-weight="400"></item-title>
                    <view class="flex items-center justify-between mt-[10rpx]">
                        <view class="w-[480rpx]">
                            <app-slider
                                :step="1"
                                :max="1000"
                                :min="0"
                                :showValue="false"
                                :activeColor="$theme.primaryColor"
                                v-model="params.s"
                            ></app-slider>
                        </view>
                        <input
                            class="options-input"
                            type="number"
                            :step="1"
                            :max="1000"
                            :min="0"
                            style="width: 80rpx"
                            v-model="params.s"
                            @blur="limitSValue"
                        />
                    </view>
                </view>
                <!-- 混乱值 -->
                <view class="text-base text-main mt-2">
                    <item-title title="混乱值" tips="本参数会控制生成4张图的差别， 值越大，生成4张图的区别越大，值越小,生成的4张图越接近，取值范围0-100， 默认值0" font-weight="400"></item-title>
                    <view class="flex items-center justify-between mt-[10rpx]">
                        <view class="w-[480rpx]">
                            <app-slider
                                :step="1"
                                :max="100"
                                :min="0"
                                :showValue="false"
                                :activeColor="$theme.primaryColor"
                                v-model="params.c"
                            ></app-slider>
                        </view>
                        <input
                            class="options-input"
                            type="number"
                            :step="1"
                            :max="100"
                            :min="0"
                            style="width: 80rpx"
                            v-model="params.c"
                            @blur="limitCValue"
                        />
                    </view>
                </view>
                <!-- 随机种子 -->
                <view class="text-base text-main mt-2">
                    <item-title title="随机种子" tips="种子用于指定生成效果，可以用于生成套图，保障生成的一系列图片保持同一种风格" font-weight="400"></item-title>
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
import { computed, ref } from 'vue'
import ItemTitle from '../item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        qList: any
    }>(),
    {
        modelValue: () => {},
        qList: {}
    }
)
const show = ref<boolean>(true)
const { modelValue: params } = useVModels(props, emit)

const formattedQList = computed(() => {
    return Reflect.ownKeys(props.qList).map(key => ({
        value: key,
        label: props.qList[key]
    }));
})

const limitStepValue = (e: any) => {
    const value = parseFloat(e.target.value)
    if (value >= 2) {
        params.value.iw = 2
    } else if (value < 0) {
        params.value.iw = 0.5
    } else {
        params.value.iw = value
    }
}

const limitSValue = (e: any) => {
    const value = parseFloat(e.target.value)
    if (value >= 1000) {
        params.value.s = 1000
    } else if (value < 0) {
        params.value.s = 0
    } else {
        params.value.s = value
    }
}

const limitCValue = (e: any) => {
    const value = parseFloat(e.target.value)
    if (value >= 100) {
        params.value.c = 100
    } else if (value < 0) {
        params.value.c = 0
    } else {
        params.value.c = value
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