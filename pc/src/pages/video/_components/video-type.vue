<template>
    <div
        class="p-1 bg-[var(--el-bg-color-page)] rounded-[12px]"
        style="--el-border-radius-base: 12px"
    >
        <el-segmented
            :block="false"
            class="w-full h-[36px] !bg-[transparent]"
            v-model="modelValue"
            :options="typeOptions"
        />
    </div>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue: number
    }>(),
    {
        modelValue: 1
    }
)

const { modelValue } = useVModels(props, emit)

const typeOptions = [
    {
        label: '文生视频',
        value: 1
    },
    {
        label: '图生视频',
        value: 2
    }
]
</script>

<style lang="scss" scoped></style>
