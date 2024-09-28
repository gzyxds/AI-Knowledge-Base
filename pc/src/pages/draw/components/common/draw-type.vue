<template>
    <div
        class="p-1 bg-[var(--el-bg-color-page)] rounded-[12px]"
        style="--el-border-radius-base: 12px"
    >
        <el-segmented
            :block="false"
            class="w-full h-[36px] !bg-[transparent]"
            v-model="modelValue"
            :options="drawTypeOptions"
        />
    </div>
</template>

<script lang="ts" setup>
import type { DrawTypeOptions } from '../../types/draw'
import { useVModels } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue: 'txt2img' | 'img2img'
    }>(),
    {
        modelValue: 'txt2img'
    }
)

const { modelValue } = useVModels(props, emit)

const drawTypeOptions: DrawTypeOptions[] | undefined = [
    {
        label: '文生图',
        value: 'txt2img'
    },
    {
        label: '图生图',
        value: 'img2img'
    }
]
</script>

<style lang="scss" scoped></style>
