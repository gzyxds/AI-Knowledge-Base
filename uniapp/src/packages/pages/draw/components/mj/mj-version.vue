<template>
    <view class="mt-4">
        <item-title title="版本选择" tips="指定midjourney的渲染版本" required></item-title>
        <u-form-item prop="size">
            <view class="flex-1 bg-white rounded-lg py-[20rpx] px-[20rpx] overflow-hidden">
                <app-select
                    v-model="currentVersion"
                    popupTitle="版本选择"
                    :dataLists="formattedVersionList"
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
import { computed, watch } from 'vue'
import ItemTitle from '../item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        versionList?: any
        drawModel: string
    }>(),
    {
        modelValue: '',
        versionList: []
    }
)
const { modelValue: currentVersion } = useVModels(props, emit)

const formattedVersionList = computed(() => {
    return props?.versionList?.map((item: string[]) => ({
        value: item,
        label: `${item}`
    })) || [];
})

watch(() => [props?.versionList, props.drawModel], () => {
    if (!props?.versionList.length) return
    currentVersion.value = props?.versionList[0]
})
</script>