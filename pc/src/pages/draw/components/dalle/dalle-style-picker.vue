<template>
    <div class="mt-[15px]">
        <sidbar-item-title title="风格选择" tips="" required />

        <div class="mt-[10px]">
            <div
                v-for="item in styleList"
                :key="item.value"
                class="picture-style-picker rounded-[12px]"
                :class="{
                    'picture-style-picker__active': item.value === value
                }"
                @click="value = item.value"
            >
                {{ item.label }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import sidbarItemTitle from './../common/sidbar-item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: 'vivid'
    }
)
const { modelValue: value } = useVModels(props, emit)

const styleList: any = [
    {
        value: 'vivid',
        label: '生动'
    },
    {
        value: 'natural',
        label: '自然'
    }
]

value.value = 'vivid'
</script>

<style lang="scss" scoped>
.picture-style-picker:nth-child(2n) {
    margin-left: 10px;
}

.picture-style-picker {
    display: inline-block;
    cursor: pointer;
    width: 155px;
    padding: 8px 0;
    text-align: center;
    border: 1px solid transparent;
    @apply bg-page;
}

.picture-style-picker:hover,
.picture-style-picker__active {
    color: var(--el-color-primary);
    border: 1px solid var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);
}
</style>