<template>
    <view class="mt-4">
        <item-title title="风格选择" tips="指定midjourney的渲染风格" required></item-title>
        <u-form-item prop="size">
            <view class="flex-1 bg-white rounded-lg py-[20rpx] px-[20rpx] overflow-hidden">
                <app-select
                    v-model="currentStyles"
                    popupTitle="风格选择"
                    :dataLists="formattedStyleList"
                    placeholder="请选择"
                    :closeable="false"
                    name="label"
                    value="value"
                >
                </app-select>
            </view>
        </u-form-item>
    </view>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import ItemTitle from '../item-title.vue'
import {computed} from "vue";

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        stylesList?: any
    }>(),
    {
        modelValue: '',
        stylesList: {}
    }
)
const { modelValue: currentStyles } = useVModels(props, emit)

const formattedStyleList = computed(() => {
    return Reflect.ownKeys(props.stylesList).map(key => ({
        value: key,
        label: props.stylesList[key]
    }));
})
</script>