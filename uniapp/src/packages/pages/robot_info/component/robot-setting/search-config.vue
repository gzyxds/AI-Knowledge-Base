<template>
    <view>
        <u-form-item label="AI模型" prop="model_sub_id" required>
            <view class="flex-1 min-w-0">
                <model-picker
                    :setDefault="false"
                    v-model:id="formData.model_id"
                    v-model:sub_id="formData.model_sub_id"
                >
                    <template #default="{ item }">
                        <view class="input flex">
                            <view class="flex-1 min-w-0">
                                <view class="line-clamp-1">
                                    <text v-if="item.alias">{{ item.alias }}</text>
                                    <text class="text-[#888]" v-else>请选择</text>
                                    <text class="text-muted" v-if="item.alias && item.price == '0'">
                                        (免费)
                                    </text>
                                    <text class="text-muted" v-else-if="item.alias">
                                        ({{ `消耗${item.price}${appStore.getTokenUnit}/1千字符` }})
                                    </text>
                                </view>
                            </view>
                            <view
                                class="text-muted flex flex-none mx-[20rpx]"
                                v-if="formData.model_id"
                                @click.stop="formData.model_id = '';formData.model_sub_id=''"
                            >
                                <u-icon name="close-circle"/>
                            </view>
                            <view class="text-muted flex">
                                <u-icon name="arrow-down" />
                            </view>
                        </view>
                    </template>
                </model-picker>
            </view>
        </u-form-item>
        <u-form-item label="相似度" prop="search_similarity">
            <view class="flex-1">
                <view>
                    <app-slider
                        :min="0"
                        :max="1"
                        :step="0.001"
                        :activeColor="$theme.primaryColor"
                        v-model="formData.search_similarity"
                    ></app-slider>
                </view>

                <view class="text-muted">
                    输入0-1之间的数值，支持3位小数点；高相似度推荐设置0.8以上
                </view>
            </view>
        </u-form-item>
        <u-form-item label="单次搜索数量" prop="search_limits">
            <view class="flex-1 w-full">
                <view>
                    <app-slider
                        :min="0"
                        :max="20"
                        :activeColor="$theme.primaryColor"
                        v-model="formData.search_limits"
                    ></app-slider>
                </view>

                <view class="text-muted">
                    默认设置为5，请输入0-20之间的整数数值
                </view>
            </view>
        </u-form-item>
        <u-form-item label="空搜索回复" label-position="left" label-width="150">
            <view class="flex-1 flex justify-end">
                <u-radio-group v-model="formData.search_empty_type">
                    <u-radio :name="1"> AI回复 </u-radio>
                    <u-radio :name="2"> 自定义回复 </u-radio>
                </u-radio-group>
            </view>
        </u-form-item>
        <u-form-item v-if="formData.search_empty_type === 2">
            <view class="flex-1 min-w-0">
                <u-input
                    v-model="formData.search_empty_text"
                    type="textarea"
                    :height="200"
                    placeholder="请输入回复内容，当搜索匹配不上内容时，直接回复填写的内容"
                    :border="true"
                />
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { useAppStore } from '@/stores/app'
const props = withDefaults(
    defineProps<{
        modelValue: Record<string, any>
    }>(),
    {}
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
}>()
const appStore = useAppStore()
const formData = useVModel(props, 'modelValue', emit)
</script>

<style lang="scss" scoped>
.input {
    min-height: 70rpx;
    width: 100%;
    border-radius: 8rpx;
    line-height: 50rpx;
    border: 1px solid var(--color-light, #e5e5e5);
    &-placeholder {
        color: rgb(136, 136, 136);
    }
    padding: 10rpx 20rpx;
    box-sizing: border-box;
}
</style>
