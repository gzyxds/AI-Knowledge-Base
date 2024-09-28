<template>
    <div class="mj-version" v-if="config?.mj_version">
        <sidbar-item-title title="版本选择" tips="指定midjourney的渲染版本" />
        <div>
            <el-select
                v-model="currentVersion"
                placeholder="请选择版本"
                class="w-full mt-[8px]"
                size="large"
            >
                <el-option
                    v-for="(item, key) in config?.mj_version[formData.draw_model]"
                    :key="item"
                    :label="item"
                    :value="item"
                />
            </el-select>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import { config, formData } from '../../hooks/useDrawEffect'
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

const { modelValue: currentVersion } = useVModels(props, emit)

watch(() => [formData.value.draw_model, config.value?.mj_version], ([v1, v2]) => {
    if (!config.value?.mj_version) return
    if (currentVersion.value !== v1) {
        currentVersion.value = config.value?.mj_version[formData.value.draw_model][0]
    }
})
</script>

<style lang="scss" scoped>
.mj-version {
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