<template>
    <div>
        <sidbar-item-title
            title="反向词"
            tips="输入你希望AI绘制的内容，例如：white hair,sit，这样AI尽可能避免绘制白色的毛发和坐着的姿势"
        />
        <div class="bg-[var(--el-bg-color-page)] rounded-[12px]">
            <el-input
                v-model="value"
                :rows="4"
                :input-style="{
                    boxShadow: 'unset',
                    backgroundColor: 'transparent'
                }"
                resize="none"
                type="textarea"
                placeholder="请输入反向提示词"
                @focus="checkUserLogin"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import sidbarItemTitle from './../common/sidbar-item-title.vue'
import { useVModels } from '@vueuse/core'
import { checkUserLogin } from './../../hooks/useDrawEffect'

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

const { modelValue: value } = useVModels(props, emit)
</script>

<style lang="scss" scoped></style>
