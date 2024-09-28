<template>
    <div class="mj-styles">
        <sidbar-item-title title="风格选择" tips="指定midjourney的渲染风格" />
        <div>
            <el-select
                v-model="currentStyle"
                placeholder="请选择版本"
                class="w-full mt-[8px]"
                size="large"
            >
                <el-option
                    v-for="(item, key) in config?.mj_style"
                    :key="item"
                    :label="item"
                    :value="key"
                />
            </el-select>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import { config } from '../../hooks/useDrawEffect'
import sidbarItemTitle from './../common/sidbar-item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)

const { modelValue: currentStyle } = useVModels(props, emit)
</script>

<style lang="scss" scoped>
.mj-styles{
    :deep(.el-select) {
        @apply bg-page;
        box-shadow: none;
        border-radius: 12px;
        .el-select__wrapper {
            @apply bg-page;
            box-shadow: none;
        }
    }
}
</style>