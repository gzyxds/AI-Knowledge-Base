<template>
    <div>
        <sidbar-item-title
            title="重绘强度"
            required
            tips="低：越接近原图 高：越充满创意" />
        <div class="flex gap-4 items-center pl-3">
            <el-slider v-model="modelValue" :step="0.01" :max="1" />
            <span>{{ modelValue }}</span>
        </div>
    </div>
</template>

<script lang="ts" setup>
import sidbarItemTitle from './../common/sidbar-item-title.vue'

import { useVModels } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'update:modelValue', value: number): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue: number
    }>(),
    {
        modelValue: 0.75
    }
)

const { modelValue } = useVModels(props, emit)
</script>

<style lang="scss" scoped></style>
